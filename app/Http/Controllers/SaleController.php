<?php

namespace App\Http\Controllers;

use App\Exports\SalesExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Customer;
use App\Models\Emi;
use App\Models\Finance;
use App\Models\LedgerEntries;
use App\Models\PaymentMaster;
use App\Models\PointsHistories;
use App\Models\RewardPoint;
use App\Models\RewardPointTransaction;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Bank;
use App\Models\SalesItems;
use App\Models\ShopMaster;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    // your existing functions...

    public function export()
    {
        return Excel::download(new SalesExport, 'sales.xlsx');
    }

    public function saleList(Request $request)
    {
        $page_title = "Sales List";
    
        $search = $request['search'] ?? "";
    
        if ($search) {
    
            $sales = Sale::where('invoice_number', 'LIKE', "%$search%")
                ->orWhereHas('customer', function ($query) use ($search) {
                    $query->where('name', 'LIKE', "%$search%")
                        ->orWhere('phone', 'LIKE', "%$search%");
                })
                ->paginate(20)
                ->withQueryString();
    
            $data = compact('sales', 'search', 'page_title');
    
        }
        
        // ✅ DATE FILTER (ONLY FIXED THIS PART)
        elseif ($request->has('start_date') && $request->has('end_date')) {
    
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'date|after_or_equal:start_date|nullable',
            ]);
    
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $endDate = Carbon::parse($request->end_date)->endOfDay();
    
            // 🔥 First create query
            $query = Sale::whereBetween('created_at', [$startDate, $endDate]);
    
            // 🔥 Get full total (before pagination)
            $totalSaleAmount = (clone $query)->sum('total');
    
            // 🔥 Then paginate
            $sales = $query->paginate(10)
                ->withQueryString();
    
            $data = compact('sales', 'totalSaleAmount', 'page_title');
    
        } else {
    
            $sales = Sale::with(['customer', 'items.product'])
                ->orderBy('created_at', 'desc')
                ->paginate(25)
                ->withQueryString();
    
            $data = compact('sales', 'page_title');
        }
    
        return view('admin.sale.salesList')->with($data);
    }


    public function saleCreate()
    {
        $page_title = 'Create Sale';
        $banks=Bank::orderBy('name')->get();
        $finances=Finance::orderBy('name','asc')->get();
        $data = compact('page_title','finances','banks');
        return view('admin.sale.createSale', $data)->with($data);
    }

    public function saleStore(Request $request)
    {
        $request->validate([
            'customer_phone' => 'required|integer',
            'customer_name' => 'required|string',
            'address' => 'required|string',
            'customer_whatsapp' => 'required|integer',
            'customer_pin' => 'required|integer',
            'total_amount' => 'required|numeric',
            'gst_applicable' => 'required|in:yes,no',
            'gst_number' => 'nullable|string',
            'cash_amount' => 'nullable|numeric',
            'online_amount' => 'nullable|numeric',
            'finance' => 'nullable|integer|exists:finance,id',
            'products' => 'required|json',
            'final_discount'=>'nullable|numeric',
            'used_reward_points' =>'nullable|numeric|min:0',
            'referral_mobile' =>'nullable|string|max:15',
    
        ]);
        if (isset($request->online_amount)) {
            $request->validate(['bank'=>'required|exists:banks,id']);
        }
        // Decode products JSON
        $products = json_decode($request->products, true);
        // Start a database transaction

        try {
            DB::beginTransaction();
            $customer = Customer::where('phone', $request->customer_phone)
                // ->where('name', $request->customer_name)
                // ->where('wpnumber', $request->customer_whatsapp)
                // ->where('address', $request->address)
                ->where('pin', $request->customer_pin)
                ->first();

            if (!$customer) {
                $customer = Customer::create([
                    'phone' => $request->customer_phone,
                    'name' => $request->customer_name,
                    'wpnumber' => $request->customer_whatsapp,
                    'address' => $request->address,
                    'pin' => $request->customer_pin,
                    'gst_number' => $request->gst_number,
                ]);
            }

            $customerId = $customer->id;
            $invoiceData = $this->generateInvoiceNumber();

            $invoiceNo = $invoiceData['number'];
            $serial = $invoiceData['serial'];
            
            // TOTAL PURCHASE VALUE
 $totalPurchaseValue = 0;

 foreach ($products as $productData) {

    $product = Product::where(
        'code',
        $productData['product_code']
    )->first();

    if($product){

        $totalPurchaseValue +=
            (
                $product->purchase_withgst
                *
                $productData['quantity']
            );
    }
 }

// REWARD CALCULATION

  if ($totalPurchaseValue < 5000) {

    // Below 5000 = 2%
    $earnedPoint = ($totalPurchaseValue * 2) / 100;

  } elseif ($totalPurchaseValue >= 5000 && $totalPurchaseValue <= 19000) {

    // 5000 to 19000 = 1%
    $earnedPoint = ($totalPurchaseValue * 1) / 100;

   } else {

    // Above 19000 = 0.50%
    $earnedPoint = ($totalPurchaseValue * 0.5) / 100;
  }

    // // OLD REWARD CALCULATION

    // if ($totalPurchaseValue <= 5000) {

    //     $earnedPoint =
    //         ($totalPurchaseValue * 1) / 100;

    // } else {

    //     $earnedPoint =
    //         ($totalPurchaseValue * 0.5) / 100;
    // }

    // REWARD MOBILE

    $rewardMobile =
        $request->referral_mobile
        ? $request->referral_mobile
        : $request->customer_phone;

    // USED POINT

    $usedPoint =
        $request->used_reward_points ?? 0;

    // REWARD ACCOUNT

    $rewardAccount =
        RewardPoint::firstOrCreate(

            [
                'mobile' => $rewardMobile
            ],

            [
                'customer_name' =>
                    $request->customer_name
            ]
        );

    // CHECK ONLY WHEN USER USING POINT

    if($usedPoint > 0){

    if($usedPoint > $rewardAccount->balance){

        return redirect()
            ->back()
            ->with(
                'fail',
                'Insufficient Reward Balance.'
            );
    }
}

    // FINAL BILL

    $finalAmount =
        $request->total_amount - $usedPoint;

    // PREVENT NEGATIVE

    if($finalAmount < 0){

        $finalAmount = 0;
    }

            // Create purchase master record
            $saleMaster = Sale::create([
                'referral_mobile' =>$request->referral_mobile,
                'reward_mobile' =>$rewardMobile,
                'used_reward_points' =>$usedPoint,
                'earned_reward_points' =>round($earnedPoint,2),
                'customer_id' => $customerId,
                'total' => $finalAmount,
                'invoice_number' => $invoiceNo,
                'invoice_serial' => $serial,
                'gst' => $request->gst_applicable,
                'discount' => $request->final_discount,
                'sale_by' => auth()->user()->id,
            ]);

            // Save purchase details
            foreach ($products as $productData) {
                $product_exsits = Product::where('code', $productData['product_code'])->first();

                if (!$product_exsits) {
                    return redirect()->back()->with('error', 'Product with code ' . $productData['product_code'] . ' does not exist.');
                }
                $product_exsits->stock = $product_exsits->stock - $productData['quantity'];
                $product_exsits->save();

                $productId = $product_exsits->id;
                SalesItems::create([
                    'sale_id' => $saleMaster->id,
                    'product_id' => $productId,
                    'purchase_price'=>$product_exsits->purchase_withgst,
                    'quantity' => $productData['quantity'],
                    'price' => $productData['sale_rate'],
                    'warranty' => $productData['warranty'],
                    'sl_no' => $productData['sl_no'],
                    'discount' => $productData['discount'],
                    'gst' => $productData['gst'],
                ]);

                // stock maintain logic
                // $product=Product::find($productId);
            }

            $lastBalance = LedgerEntries::where('customer_id', $customerId)
                ->latest('id')
                ->value('balance_after') ?? 0;
            $newBalance = $lastBalance + $finalAmount;

            LedgerEntries::create([
                'entity_type' => 'customer',
                'customer_id' => $customerId,
                'invoice_id' => $invoiceNo,
                'transaction_type' => 'sale',
                'type' => 'debit',
                'amount' => $finalAmount,
                'balance_after' => $newBalance,
            ]);

            if (isset($request->cash_amount)) {
                $payment = PaymentMaster::create([
                    'flow_type' => 'inflow',
                    'invoice_no' => $invoiceNo,
                    'invoice_type' => 'sale',
                    'customer_id' => $customer->id,
                    'amount' => $request->cash_amount,
                    'method' => 'cash',
                    'payment_date' => Carbon::now()->format('Y-m-d'),
                ]);

                $lastBalance = LedgerEntries::where('customer_id', $customer->id)
                    ->latest('id')
                    ->value('balance_after') ?? 0;

                $newBalance = $lastBalance - $request->cash_amount;
                $method = 'cash';

                LedgerEntries::create([
                    'entity_type' => 'customer',
                    'customer_id' => $customer->id,
                    'invoice_id' => $invoiceNo,
                    'transaction_type' => 'payment',
                    'method' => $method,
                    'type' => 'credit',
                    'amount' => $request->cash_amount,
                    'balance_after' => $newBalance,
                ]);
            }
            if (isset($request->online_amount)) {
                $payment = PaymentMaster::create([
                    'flow_type' => 'inflow',
                    'invoice_no' => $invoiceNo,
                    'invoice_type' => 'sale',
                    'customer_id' => $customer->id,
                    'amount' => $request->online_amount,
                    'method' => 'online',
                    'payment_date' => Carbon::now()->format('Y-m-d'),
                    'bank_id'=>$request->bank,
                ]);

                $bank_data=Bank::find($request->bank);
                $bankBalance=$bank_data->amount;

                $payment->last_bank_balance = $bankBalance + $request->online_amount;
                $bank_data->amount=$bankBalance + $request->online_amount;
                $bank_data->save();
                $payment->save();


                $lastBalance = LedgerEntries::where('customer_id', $customer->id)
                    ->latest('id')
                    ->value('balance_after') ?? 0;

                $newBalance = $lastBalance - $request->online_amount;
                $method = 'online';

                LedgerEntries::create([
                    'entity_type' => 'customer',
                    'customer_id' => $customer->id,
                    'invoice_id' => $invoiceNo,
                    'transaction_type' => 'payment',
                    'method' => $method,
                    'type' => 'credit',
                    'amount' => $request->online_amount,
                    'balance_after' => $newBalance,
                ]);
            }
            $paidAmount = ($request->cash_amount ?? 0) + ($request->online_amount ?? 0);
            if (isset($request->finance)) {
                Emi::create([
                    'customer_id' => $saleMaster->customer_id,
                    'sales_id' => $saleMaster->id,
                    'amount' => $finalAmount - $paidAmount,
                    'down_payment' => $paidAmount,
                    'emi_id' => $request->finance,
                    'status' => 'pending',
                    'date' => Carbon::now()->format('Y-m-d'),
                ]);
            }
            
            // USED POINT DEDUCT

if($usedPoint > 0){

    $rewardAccount->balance -=
        $usedPoint;

    $rewardAccount->total_used +=
        $usedPoint;

    RewardPointTransaction::create([

        'reward_point_id' =>
            $rewardAccount->id,

        'sale_id' =>
            $saleMaster->id,

        'mobile' =>
            $rewardMobile,

        'transaction_type' =>
            'redeem',

        'points' =>
            $usedPoint,

        'note' =>
            'Reward point used in sale',
    ]);
}

// EARNED POINT ADD

if($earnedPoint > 0){

    $rewardAccount->balance +=
        $earnedPoint;

    $rewardAccount->total_earned +=
        $earnedPoint;

    RewardPointTransaction::create([

        'reward_point_id' =>
            $rewardAccount->id,

        'sale_id' =>
            $saleMaster->id,

        'mobile' =>
            $rewardMobile,

        'transaction_type' =>
            'earn',

        'points' =>
            $earnedPoint,

        'note' =>
            'Reward earned from sale',
    ]);
}

$rewardAccount->save();
            
            DB::commit();
            return redirect()->route('sale.invoice',$saleMaster->id)->with('success', 'Sale saved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('f', 'An error occurred while processing. Please try again.');
        }
    }
    public function saleShow($id)
    {
        $page_title = "Sale Details";
        $sales = Sale::with('customer','order','order.dealer')->findOrFail($id);
        $sale_item = SalesItems::with('product', 'product.category', 'product.brand')
        ->where('sale_id', $id)->get();
        $data = compact('page_title', 'sales', 'sale_item');
        return view('admin.sale.saleShow')->with($data);
    }

    public function saleUpdate(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:sales_items,id',
            'sl_no' => 'required|string',
            'warranty' => 'required|string',
        ]);

        $saleItem = SalesItems::find($request->id);
        $saleItem->sl_no = $request->sl_no;
        $saleItem->warranty = $request->warranty;
        $saleItem->save();
        return redirect()->back()->with('success', 'Sale item updated successfully!');
    }

    public function saleInvoice($id)
    {
        $page_title = "Sale Invoice";
        $shop=ShopMaster::first();
        $products=SalesItems::where('sale_id','=',$id)->with('product')->get();
        $invoice=Sale::find($id);
        $data=compact('shop','products','invoice','page_title');
        return view('admin.sale.invoice')->with($data);
    }


public function generateInvoiceNumber()
{
    $today = \Carbon\Carbon::now();

    if ($today->month >= 4) {
        $fyStart = \Carbon\Carbon::create($today->year, 4, 1);
    } else {
        $fyStart = \Carbon\Carbon::create($today->year - 1, 4, 1);
    }

    $fyEnd = (clone $fyStart)->addYear()->subDay();

    $lastSerial = \App\Models\Sale::whereBetween('created_at', [$fyStart, $fyEnd])
        ->max('invoice_serial');

    $nextSerial = $lastSerial ? $lastSerial + 1 : 1;

    // ✅ IMPORTANT FIX
    $datePart = $today->format('dmy');

    return [
        'number' => "IC/{$datePart}/" . str_pad($nextSerial, 4, '0', STR_PAD_LEFT),
        'serial' => $nextSerial
    ];
  }

public function store(Request $request)
{
    
    $customer = \App\Models\Customer::firstOrCreate(
        ['phone' => $request->phone],
        [
            'name'     => $request->name,
            'address'  => $request->address,
            'pin'      => $request->pincode,
            'whatsapp' => $request->whatsapp ?? $request->phone
        ]
    );

    
    $sale = new \App\Models\Sale();
    $sale->customer_id = $customer->id; 
    $sale->subtotal = $request->subtotal;
    $sale->total_amount = $request->grand_total ?? $request->subtotal;
    
    

    $sale->save();

    return redirect()->back()->with('success', 'বিল সফলভাবে তৈরি এবং কাস্টমার ডাটা সেভ হয়েছে!');

}
}