<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Company;
use App\Models\LedgerEntries;
use App\Models\Product;
use App\Models\ProductDetails;
use App\Models\Purchase;
use App\Models\PurchaseItems;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function purchaseList(Request $request)
{
    $page_title = "Purchase List";
    $search = $request->input('search', '');
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    // Base query with company relationship
    $query = Purchase::with('company');

    // 🔍 Search by invoice number or company name or phone
    if (!empty($search)) {
        $query->where(function ($q) use ($search) {
            $q->where('purchase_invoice_no', 'LIKE', "%{$search}%")
                ->orWhereHas('company', function ($companyQuery) use ($search) {
                    $companyQuery->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('phone', 'LIKE', "%{$search}%");
                });
        });
    }

    // 📅 Filter by date range (if both start & end are provided)
    $flag = false;
    $totalPurchaseAmount = null;

    if (!empty($startDate) && !empty($endDate)) {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        $query->whereBetween('purchase_date', [$start, $end]);
        $flag = true;
    }

    // 🧾 Fetch records with pagination
    // 🔥 ONLY ADDITION
    $purchace_products = $query->latest()
        ->paginate(25)
        ->withQueryString();

    // 💰 Calculate total purchase amount for current filtered results
    if ($flag) {
        $totalPurchaseAmount = $purchace_products->sum('total_amount');
    }

    $data = compact(
        'purchace_products',
        'page_title',
        'search',
        'totalPurchaseAmount',
        'startDate',
        'endDate'
    );

    return view('admin.purchase.purchaseList')->with($data);
}


    public function purchaseShow($id)
    {
        $page_title = "Purchase Details";
        $purchase = Purchase::with('company')->findOrFail($id);
        $purchase_item = PurchaseItems::with('product', 'product.category', 'product.brand')->where('master_id', $id)->get();
        $data = compact('page_title', 'purchase', 'purchase_item');

        return view('admin.purchase.purchaseShow')->with($data);
    }
    public function purchaseCreate()
    {
        $page_title = "Create Purchase";
        $categories = Category::orderBy('name', 'ASC')->get();
        $brands = Brand::orderBy('name', 'ASC')->get();
        $company = Company::orderBy('name', 'ASC')->get();
        $data = compact('page_title', 'categories', 'company', 'brands');

        return view('admin.purchase.create')->with($data);
    }

    public function purchaseStore(Request $request)
    {
        // Validate the request
        $request->validate([
            'vendor_id' => 'required|integer',
            'total_amount' => 'required|numeric',
            'total_wout_discount' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'date' => 'required|date',
            'invoice_number' => 'required|string|unique:purchases,purchase_invoice_no',
            'gst_applicable' => 'required|in:0,1',
            'products' => 'required|json',
        ]);

        // Decode products JSON
        $products = json_decode($request->products, true);

        // Start a database transaction
        $stock_weight = 0;
        try {
            DB::beginTransaction();
            $withGstTotal = 0;
            // Create purchase master record
            $purchaseMaster = Purchase::create([
                'company_id' => $request->vendor_id,
                'total_amount' => $request->total_amount,
                'total_wout_discount' => $request->total_wout_discount,
                'discount' => $request->discount ?? 0,
                'gst' => $request->gst_applicable,
                'purchase_invoice_no' => $request->invoice_number,
                'purchase_date' => $request->date,
                // 'total_amount' => array_sum(array_column($products, 'amount')),
                // 'net_amount' => array_sum(array_column($products, 'amount')) + $request->round_off,
            ]);

            // Save purchase details
            foreach ($products as $productData) {
                $product_exsits = Product::where('category_id', $productData['category'])
                    ->where('brand_id', $productData['brand'])
                    ->where('model', $productData['model'])
                    ->first();

                if ($product_exsits) {
                    $gst = $productData['gst'];
                    // $online_price = $request->purchase_price + ($request->purchase_price * ($gst / 100))
                    //     + ($request->purchase_price * ($request->dealer_point / 100));
                    $product_exsits->update([
                        'stock' => $product_exsits->stock + $productData['quantity'],

                        'purchase_price' => $productData['purchase_price'],
                        'purchase_withgst' => round($productData['purchase_price'] + ($productData['purchase_price'] * ($gst / 100))),

                        'sale_rate' => $productData['sale_price'], // %
                        'sale_price' => round($productData['purchase_price']
                            + ($productData['purchase_price'] * ($productData['sale_price'] / 100))
                            + ($productData['purchase_price'] * ($gst / 100))), //rs
                        'price' => round($productData['purchase_price']
                            + ($productData['purchase_price'] * ($gst / 100))
                            + ($productData['purchase_price'] * ($productData['online_price'] / 100))
                            + ($productData['purchase_price'] * ($productData['dealer_point'] / 100))), //rs internal use

                        'online_rate' => $productData['online_price'], // %
                        'online_price' => round($productData['purchase_price']
                            + ($productData['purchase_price'] * ($gst / 100))
                            + ($productData['purchase_price'] * ($productData['online_price'] / 100))), //rs

                        'dealer_point' => $productData['dealer_point'], // %
                        'point' => round($productData['purchase_price'] * ($productData['dealer_point'] / 100)), //rs

                        'salesmen_point' => $productData['salesmen_point'], // %
                        'salesmen_point_price' => round($productData['purchase_price'] * ($productData['salesmen_point'] / 100)), //rs

                        'delivery_charges' => $productData['delivery_charges'], // %
                        'delivery_charges_amount' => round($productData['purchase_price'] * ($productData['delivery_charges'] / 100)), //rs
                    ]);
                    $productId = $product_exsits->id;
                    $withGstTotal = $withGstTotal + (($productData['purchase_price'] + ($productData['purchase_price'] * ($gst / 100))) * $productData['quantity']);
                } else {

                    $product_details = ProductDetails::create([
                        'status' => 0,
                    ]);
                    $gst = $productData['gst'];
                    $product = Product::create([
                        'details_id' => $product_details->id,
                        'brand_id' => $productData['brand'],
                        'category_id' => $productData['category'],
                        'model' => $productData['model'],
                        'code' => $this->generateUniqueProductCode(),
                        'purchase_id' => $purchaseMaster->id,
                        'stock' => $productData['quantity'],
                        /////////////////////
                        'purchase_price' => $productData['purchase_price'],
                        'purchase_withgst' => round($productData['purchase_price'] + ($productData['purchase_price'] * ($gst / 100))),

                        'sale_rate' => $productData['sale_price'], // %
                        'sale_price' => round($productData['purchase_price']
                            + ($productData['purchase_price'] * ($productData['sale_price'] / 100))
                            + ($productData['purchase_price'] * ($gst / 100))), //rs
                        'price' => round($productData['purchase_price']
                            + ($productData['purchase_price'] * ($gst / 100))
                            + ($productData['purchase_price'] * ($productData['online_price'] / 100))
                            + ($productData['purchase_price'] * ($productData['dealer_point'] / 100))), //rs internal use

                        'online_rate' => $productData['online_price'], // %
                        'online_price' => round($productData['purchase_price']
                            + ($productData['purchase_price'] * ($gst / 100))
                            + ($productData['purchase_price'] * ($productData['online_price'] / 100))), //rs

                        'dealer_point' => $productData['dealer_point'], // %
                        'point' => round($productData['purchase_price'] * ($productData['dealer_point'] / 100)), //rs

                        'salesmen_point' => $productData['salesmen_point'], // %
                        'salesmen_point_price' => round($productData['purchase_price'] * ($productData['salesmen_point'] / 100)), //rs

                        'delivery_charges' => $productData['delivery_charges'], // %
                        'delivery_charges_amount' => round($productData['purchase_price'] * ($productData['delivery_charges'] / 100)), //rs
                    ]);
                    $productId = $product->id;
                    $withGstTotal = $withGstTotal + ($productData['quantity'] * ($productData['purchase_price'] + ($productData['purchase_price'] * ($gst / 100))));
                }

                PurchaseItems::create([
                    'master_id' => $purchaseMaster->id,
                    'product_id' => $productId,
                    'quantity' => $productData['quantity'],
                    'price' => $productData['purchase_price'],
                    'gst' => $productData['gst'],
                    'discount' => $productData['discount'],
                    'gst_applicable' => $request->gst_applicable,
                ]);


            }
            // Subtract discount from the calculated GST total
            $withGstTotal = $withGstTotal - ($request->discount ?? 0);
            
            $purchaseMaster->with_gst_total = $withGstTotal;
            $purchaseMaster->save();

            $lastBalance = LedgerEntries::where('company_id', $request->vendor_id)
                ->latest('id')
                ->value('balance_after') ?? 0;
            $newBalance = $lastBalance + $withGstTotal;

            LedgerEntries::create([
                'entity_type' => 'company',
                'company_id' => $request->vendor_id,
                'invoice_id' => $request->invoice_number,
                'transaction_type' => 'purchase',
                'type' => 'credit',
                'amount' => $withGstTotal,
                'balance_after' => $newBalance,
            ]);
            DB::commit();
            return redirect()->back()->with('success', 'Purchase saved successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('fail', 'An error occurred while processing. Please try again.');
        }
    }
    public function purchaseBarcode($id)
    {
        $page_title = "Purchase Barcode";
        $purchase = Purchase::with('company')->findOrFail($id);
        $purchase_item = PurchaseItems::with('product', 'product.category', 'product.brand')->where('master_id', $id)->get();
        $data = compact('page_title', 'purchase', 'purchase_item');

        return view('admin.purchase.purchaseBarcode')->with($data);
    }



    private function generateUniqueProductCode($length = 10)
    {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        do {
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[random_int(0, $charactersLength - 1)];
            }
            $code = $randomString;
            $exists = Product::where('code', $code)->exists();
        } while ($exists); // Keep generating until it's unique
        return $code;
    }
}
