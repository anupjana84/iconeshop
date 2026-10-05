<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Emi;
use App\Models\Finance;
use App\Models\LedgerEntries;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\PaymentMaster;
use App\Models\PointsHistories;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Bank;
use App\Models\SalesItems;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function orderView(Request $request)
    {
        $page_title = 'Order List';
        $filter = null;
        $search = $request['search'] ?? "";

        $relations = ['customer', 'dealer', 'orderItems.product.category', 'orderItems.product.brand', 'orderItems.product.specialOffer.freeProduct', 'orderItems.product.freeProduct'];

        if ($search) {
            $order = Order::with($relations)
                ->where(function ($query) use ($search) {
                    $query->whereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%")
                            ->orWhere('phone', 'LIKE', "%$search%")
                            ->orWhere('wpnumber', 'LIKE', "%$search%")
                            ->orWhere('address', 'LIKE', "%$search%")
                            ->orWhere('state', 'LIKE', "%$search%")
                            ->orWhere('pin', 'LIKE', "%$search%");
                    })
                    ->orWhereHas('dealer', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%")
                            ->orWhere('phone', 'LIKE', "%$search%")
                            ->orWhere('wpnumber', 'LIKE', "%$search%");
                    })
                    ->orWhereHas('orderItems.product', function ($q) use ($search) {
                        $q->where('code', 'LIKE', "%$search%")
                            ->orWhere('model', 'LIKE', "%$search%");
                    });
                })
                ->latest()
                ->paginate(20)
                ->withQueryString();

            $data = compact('order', 'search', 'page_title', 'filter');

        } else {

            $order = Order::with($relations)
                ->latest()
                ->paginate(15)
                ->withQueryString();

            $data = compact('order', 'page_title', 'filter');
        }

        return view('admin.order.orderView')->with($data);
    }

    public function pendingOrders(Request $request)
    {
        $page_title = 'Pending Orders';
        $filter = "Pending";
        $search = $request['search'] ?? "";

        $relations = ['customer', 'dealer', 'orderItems.product.category', 'orderItems.product.brand', 'orderItems.product.specialOffer.freeProduct', 'orderItems.product.freeProduct'];

        if ($search) {

            $order = Order::with($relations)
                ->where('order_status', 'pending')
                ->where(function ($query) use ($search) {
                    $query->whereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%")
                            ->orWhere('phone', 'LIKE', "%$search%")
                            ->orWhere('wpnumber', 'LIKE', "%$search%")
                            ->orWhere('address', 'LIKE', "%$search%")
                            ->orWhere('state', 'LIKE', "%$search%")
                            ->orWhere('pin', 'LIKE', "%$search%");
                    })
                    ->orWhereHas('dealer', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%")
                            ->orWhere('phone', 'LIKE', "%$search%")
                            ->orWhere('wpnumber', 'LIKE', "%$search%");
                    })
                    ->orWhereHas('orderItems.product', function ($q) use ($search) {
                        $q->where('code', 'LIKE', "%$search%")
                            ->orWhere('model', 'LIKE', "%$search%");
                    });
                })
                ->latest()
                ->paginate(20)
                ->withQueryString();

            $data = compact('order', 'search', 'page_title', 'filter');

        } else {

            $order = Order::with($relations)
                ->where('order_status', 'pending')
                ->latest()
                ->paginate(15)
                ->withQueryString();

            $data = compact('order', 'page_title', 'filter');
        }

        return view('admin.order.orderView')->with($data);
    }

    public function deliveredOrders(Request $request)
    {
        $page_title = 'Delivered Orders';
        $filter = "Delivered";
        $search = $request['search'] ?? "";

        $relations = ['customer', 'dealer', 'orderItems.product.category', 'orderItems.product.brand', 'orderItems.product.specialOffer.freeProduct', 'orderItems.product.freeProduct'];

        if ($search) {

            $order = Order::with($relations)
                ->where('order_status', 'delivered')
                ->where(function ($query) use ($search) {
                    $query->whereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%")
                            ->orWhere('phone', 'LIKE', "%$search%")
                            ->orWhere('wpnumber', 'LIKE', "%$search%")
                            ->orWhere('address', 'LIKE', "%$search%")
                            ->orWhere('state', 'LIKE', "%$search%")
                            ->orWhere('pin', 'LIKE', "%$search%");
                    })
                    ->orWhereHas('dealer', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%")
                            ->orWhere('phone', 'LIKE', "%$search%")
                            ->orWhere('wpnumber', 'LIKE', "%$search%");
                    })
                    ->orWhereHas('orderItems.product', function ($q) use ($search) {
                        $q->where('code', 'LIKE', "%$search%")
                            ->orWhere('model', 'LIKE', "%$search%");
                    });
                })
                ->latest()
                ->paginate(20)
                ->withQueryString();

            $data = compact('order', 'search', 'page_title', 'filter');

        } else {

            $order = Order::with($relations)
                ->where('order_status', 'delivered')
                ->latest()
                ->paginate(15)
                ->withQueryString();

            $data = compact('order', 'page_title', 'filter');
        }

        return view('admin.order.orderView')->with($data);
    }

    public function canceledOrders(Request $request)
    {
        $page_title = 'Canceled Orders';
        $filter = "Canceled";
        $search = $request['search'] ?? "";

        $relations = ['customer', 'dealer', 'orderItems.product.category', 'orderItems.product.brand', 'orderItems.product.specialOffer.freeProduct', 'orderItems.product.freeProduct'];

        if ($search) {

            $order = Order::with($relations)
                ->where('order_status', 'canceled')
                ->where(function ($query) use ($search) {
                    $query->whereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%")
                            ->orWhere('phone', 'LIKE', "%$search%")
                            ->orWhere('wpnumber', 'LIKE', "%$search%")
                            ->orWhere('address', 'LIKE', "%$search%")
                            ->orWhere('state', 'LIKE', "%$search%")
                            ->orWhere('pin', 'LIKE', "%$search%");
                    })
                    ->orWhereHas('dealer', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%")
                            ->orWhere('phone', 'LIKE', "%$search%")
                            ->orWhere('wpnumber', 'LIKE', "%$search%");
                    })
                    ->orWhereHas('orderItems.product', function ($q) use ($search) {
                        $q->where('code', 'LIKE', "%$search%")
                            ->orWhere('model', 'LIKE', "%$search%");
                    });
                })
                ->latest()
                ->paginate(20)
                ->withQueryString();

            $data = compact('order', 'search', 'page_title', 'filter');

        } else {

            $order = Order::with($relations)
                ->where('order_status', 'canceled')
                ->latest()
                ->paginate(15)
                ->withQueryString();

            $data = compact('order', 'page_title', 'filter');
        }

        return view('admin.order.orderView')->with($data);
    }

    public function cashOrders(Request $request)
    {
        $page_title = 'Cash Orders';
        $filter = "Cash Order";
        $search = $request['search'] ?? "";

        $relations = ['dealer', 'orderItems.product.category', 'orderItems.product.brand', 'orderItems.product.specialOffer.freeProduct', 'orderItems.product.freeProduct'];

        if ($search) {

            $order = Order::with($relations)
                ->whereNull('customer_id')
                ->where(function ($query) use ($search) {
                    $query->whereHas('dealer', function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%$search%")
                            ->orWhere('phone', 'LIKE', "%$search%")
                            ->orWhere('wpnumber', 'LIKE', "%$search%");
                    })
                    ->orWhereHas('orderItems.product', function ($q) use ($search) {
                        $q->where('code', 'LIKE', "%$search%")
                            ->orWhere('model', 'LIKE', "%$search%");
                    });
                })
                ->latest()
                ->paginate(20)
                ->withQueryString();

            $data = compact('order', 'search', 'page_title', 'filter');

        } else {

            $order = Order::with($relations)
                ->whereNull('customer_id')
                ->orderBy('order_status', 'desc')
                ->latest()
                ->paginate(15)
                ->withQueryString();

            $data = compact('order', 'page_title', 'filter');
        }

        return view('admin.order.orderView')->with($data);
    }


    public function orderProcess($id)
    {
        $order = Order::findOrFail($id);
        $finances = Finance::orderBy('name')->get();
        if ($order->order_status == 'pending') {
            $page_title = 'Process Order';

            /*
|--------------------------------------------------------------------------
| REWARD POINT BALANCE
|--------------------------------------------------------------------------
*/

            $rewardBalance = 0;

            $rewardMobile = null;

            /*
            |--------------------------------------------------------------------------
            | REFERRAL MOBILE FIRST
            |--------------------------------------------------------------------------
            */

            if (!empty($order->referral_phone)) {

                $rewardMobile = $order->referral_phone;

            } else {

                $rewardMobile = $order->customer->phone ?? null;
            }

            if ($rewardMobile) {

                $reward =
                    \App\Models\RewardPoint::where(
                        'mobile',
                        $rewardMobile
                    )->first();

                if ($reward) {

                    $rewardBalance =
                        $reward->balance;
                }
            }

            $banks = Bank::orderBy('name')->get();
            $order_item = OrderItem::with(['product.specialOffer.freeProduct', 'product.category', 'product.brand'])->where('order_id', $id)->get();
            $data = compact('order', 'order_item', 'page_title', 'finances', 'banks', 'rewardBalance', 'rewardMobile');
            return view('admin.order.orderPlace')->with($data);
        } else {
            return redirect()->back()->with('fail', 'Order status already ' . $order->order_status . '.');
        }
    }
    public function directOrderProcess($id)
    {
        $order = Order::findOrFail($id);
        if ($order->order_status == 'pending') {
            $page_title = 'Process Direct Order';
            $banks = Bank::orderBy('name')->get();
            $order_item = OrderItem::with(['product.specialOffer.freeProduct', 'product.category', 'product.brand'])->where('order_id', $id)->get();
            $data = compact('order', 'order_item', 'page_title', 'banks');
            return view('admin.order.directOrderPlace')->with($data);
        } else {
            return redirect()->back()->with('fail', 'Order status already ' . $order->order_status . '.');
        }
    }



    public function orderPlace(Request $request, $id)
    {
        $request->validate([
            'warranty.*' => 'required|string',
            'slno.*' => 'required|string',
            'quantity.*' => 'required|numeric|min:1',
            'total_amount' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'final_amount' => 'required|numeric|min:0',
            'cash_payment' => 'nullable|numeric|min:0',
            'online_payment' => 'nullable|numeric|min:0',
            'finance' => 'nullable|integer|exists:finance,id',
            'gst_applicable' => 'required|in:yes,no',
        ]);
        if (isset($request->online_payment)) {
            $request->validate(['bank_id' => 'required|exists:banks,id']);
        }
        // dd($request->all());
        try {
            DB::beginTransaction();
            $order = Order::find($id);
            $order->order_status = 'delivered';
            $order->save();

            $customer = Customer::find($order->customer_id);

            $invoiceData = app(\App\Http\Controllers\SaleController::class)->generateInvoiceNumber();

            $invoiceNo = $invoiceData['number'];
            $serial = $invoiceData['serial'];

            $sales = Sale::create([
                'order_id' => $order->id,
                'customer_id' => $order->customer_id,
                'discount' => $request->discount ?? 0,
                'shipping_address' => $customer->address,
                'shipping_pin' => $customer->pin,
                'invoice_number' => $invoiceNo,
                'invoice_serial' => $serial,
                'gst' => $request->gst_applicable,
                'total' => $request->final_amount,
                'sale_by' => auth()->user()->id,
            ]);
            $orderItems = OrderItem::where('order_id', $order->id)->get();
            $earnPoints = 0;
            $salesmanBillPoint = 0;
            $total_delivery_charges = 0;
            foreach ($orderItems as $key => $item) {
                if (isset($request->quantity[$key])) {
                    $item->quantity = max(1, (int) $request->quantity[$key]);
                    $item->total = $item->price * $item->quantity;
                    $item->save();
                }
                $dealerPoint = Product::where('id', $item->product_id)->value('point');
                $salesmanPoint = Product::where('id', $item->product_id)->value('salesmen_point_price');
                $earnPoints = $earnPoints + $dealerPoint * $item->quantity;
                $salesmanBillPoint = $salesmanBillPoint + $salesmanPoint * $item->quantity;
                $product = Product::find($item->product_id);

                SalesItems::create([
                    'sale_id' => $sales->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'purchase_price' => $product->purchase_price,
                    'price' => $item->price,
                    'warranty' => $request->warranty[$key] ?? '',
                    'sl_no' => $request->slno[$key] ?? '',
                    'gst' => $item->product->category->gst,
                ]);

                $product->stock = $product->stock - $item->quantity;
                $product->save();
                $total_delivery_charges = $total_delivery_charges + ($item->delivery_charges * $item->quantity);
            }

            /*
|--------------------------------------------------------------------------
| REWARD POINT SYSTEM
|--------------------------------------------------------------------------
*/

            $rewardMobile = $order->referral_phone
                ? $order->referral_phone
                : $customer->phone;

            $rewardCustomerName = $order->referral_phone
                ? 'Referral Customer'
                : $customer->name;

            /*
            |--------------------------------------------------------------------------
            | PURCHASE AMOUNT CALCULATION
            |--------------------------------------------------------------------------
            */

            $purchaseTotal = 0;

            foreach ($orderItems as $item) {

                $purchaseTotal += (
                    $item->product->purchase_price * $item->quantity
                );
            }

            /*
            |--------------------------------------------------------------------------
            | POINT CALCULATION
            |--------------------------------------------------------------------------
            */

            if ($purchaseTotal <= 5000) {

                $earnPoint = ($purchaseTotal * 1) / 100;

            } else {

                $earnPoint = ($purchaseTotal * 0.5) / 100;
            }

            /*
            |--------------------------------------------------------------------------
            | CREATE / UPDATE REWARD BANK
            |--------------------------------------------------------------------------
            */

            $rewardPoint = \App\Models\RewardPoint::firstOrCreate(
                ['mobile' => $rewardMobile],
                [
                    'customer_name' => $rewardCustomerName,
                    'total_earned' => 0,
                    'total_used' => 0,
                    'total_refunded' => 0,
                    'balance' => 0,
                ]
            );

            $rewardPoint->total_earned += $earnPoint;
            $rewardPoint->balance += $earnPoint;
            $rewardPoint->save();

            /*
            |--------------------------------------------------------------------------
            | REDEEM REWARD POINT
            |--------------------------------------------------------------------------
            */

            $usedRewardPoint =
                $request->reward_point_used ?? 0;

            if ($usedRewardPoint > 0) {

                if ($rewardPoint->balance >= $usedRewardPoint) {

                    $rewardPoint->total_used += $usedRewardPoint;

                    $rewardPoint->balance -= $usedRewardPoint;

                    $rewardPoint->save();

                    \App\Models\RewardPointTransaction::create([

                        'reward_point_id' => $rewardPoint->id,

                        'sale_id' => $sales->id,

                        'mobile' => $rewardMobile,

                        'transaction_type' => 'redeem',

                        'points' => $usedRewardPoint,

                        'note' => 'Reward Point Used In Order Sale',
                    ]);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | TRANSACTION ENTRY
            |--------------------------------------------------------------------------
            */

            \App\Models\RewardPointTransaction::create([
                'reward_point_id' => $rewardPoint->id,
                'sale_id' => $sales->id,
                'mobile' => $rewardMobile,
                'transaction_type' => 'earn',
                'points' => $earnPoint,
                'note' => 'Order Sale Reward Point',
            ]);

            /*
            |--------------------------------------------------------------------------
            | SAVE REWARD DATA IN SALE
            |--------------------------------------------------------------------------
            */

            $sales->total_delivery_charges = $total_delivery_charges;

            $sales->earned_reward_points = round($earnPoint, 2);

            $sales->reward_mobile = $rewardMobile;

            $sales->used_reward_points =
                $request->reward_point_used ?? 0;

            $sales->save();

            $lastBalance = LedgerEntries::where('customer_id', $customer->id)
                ->latest('id')
                ->value('balance_after') ?? 0;
            $newBalance = $lastBalance + $request->cash_payment;
            LedgerEntries::create([
                'entity_type' => 'customer',
                'customer_id' => $customer->id,
                'invoice_id' => $invoiceNo,
                'transaction_type' => 'sale',
                'type' => 'debit',
                'amount' => $request->total_amount,
                'balance_after' => $newBalance,
            ]);
            if (isset($request->cash_payment)) {
                $payment = PaymentMaster::create([
                    'flow_type' => 'inflow',
                    'invoice_no' => $invoiceNo,
                    'invoice_type' => 'sale',
                    'customer_id' => $customer->id,
                    'amount' => $request->cash_payment,
                    'method' => 'cash',
                    'payment_date' => Carbon::now()->format('Y-m-d'),
                ]);

                $lastBalance = LedgerEntries::where('customer_id', $customer->id)
                    ->latest('id')
                    ->value('balance_after') ?? 0;
                $newBalance = $lastBalance - $request->cash_payment;

                LedgerEntries::create([
                    'entity_type' => 'customer',
                    'customer_id' => $customer->id,
                    'invoice_id' => $invoiceNo,
                    'transaction_type' => 'sale',
                    'method' => 'cash',
                    'type' => 'credit',
                    'amount' => $request->cash_payment,
                    'balance_after' => $newBalance,
                ]);
            }
            if (isset($request->online_payment)) {
                $payment = PaymentMaster::create([
                    'flow_type' => 'inflow',
                    'invoice_no' => $invoiceNo,
                    'invoice_type' => 'sale',
                    'customer_id' => $customer->id,
                    'amount' => $request->online_payment,
                    'method' => 'online',
                    'bank_id' => $request->bank_id,
                    'payment_date' => Carbon::now()->format('Y-m-d'),
                ]);

                $bank_data = Bank::find($request->bank_id);
                $bankBalance = $bank_data->amount;

                $payment->last_bank_balance = $bankBalance + $request->online_payment;
                $bank_data->amount = $bankBalance + $request->online_payment;
                $bank_data->save();
                $payment->save();

                $lastBalance = LedgerEntries::where('customer_id', $customer->id)
                    ->latest('id')
                    ->value('balance_after') ?? 0;
                $newBalance = $lastBalance - $request->online_payment;

                LedgerEntries::create([
                    'entity_type' => 'customer',
                    'customer_id' => $customer->id,
                    'invoice_id' => $invoiceNo,
                    'transaction_type' => 'sale',
                    'method' => 'online',
                    'type' => 'credit',
                    'amount' => $request->online_payment,
                    'balance_after' => $newBalance,
                ]);
            }
            $paidAmount = ($request->cash_payment ?? 0) + ($request->online_payment ?? 0);
            if (isset($request->finance)) {
                Emi::create([
                    'customer_id' => $customer->id,
                    'sales_id' => $sales->id,
                    'amount' => $request->total_amount - $paidAmount,
                    'down_payment' => $paidAmount,
                    'emi_id' => $request->finance,
                    'status' => 'pending',
                    'date' => Carbon::now()->format('Y-m-d'),
                ]);
            }
            if ($order->salesman_id) {
                PointsHistories::create([
                    'user_id' => $order->salesman_id,
                    'points' => $earnPoints,
                    'sales_id' => $sales->id,
                    'date' => Carbon::now()->format('Y-m-d'),
                ]);

                $lastBalance = LedgerEntries::where('user_id', $order->salesman_id)
                    ->latest('id')
                    ->value('balance_after') ?? 0;
                $newBalance = $lastBalance + $earnPoints;

                LedgerEntries::create([
                    'entity_type' => 'user',
                    'user_id' => $order->salesman_id,
                    'invoice_id' => $invoiceNo,
                    'transaction_type' => 'commission',
                    // 'method' => 'online',
                    'type' => 'credit',
                    'amount' => $earnPoints,
                    'balance_after' => $newBalance,
                ]);

            }
            DB::commit();
            return redirect()->route('sale.list')->with('success', 'Order process to sale success');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('fail', 'Somthing error occurs while Processing!');
        }
    }
    public function directOrderPlace(Request $request, $id)
    {

        $request->validate([
            'warranty.*' => 'required|string',
            'slno.*' => 'required|string',
            'price.*' => 'required|numeric',
            'quantity.*' => 'required|numeric|min:1',
            'total_amount' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'final_amount' => 'required|numeric|min:0',
            'cash_payment' => 'nullable|numeric|min:0',
            'online_payment' => 'nullable|numeric|min:0',
            'gst_applicable' => 'required|in:yes,no',
        ]);

        if (isset($request->online_payment)) {
            $request->validate(['bank_id' => 'required|exists:banks,id']);
        }
        // dd($request->all());

        if ($request->cash_payment + $request->online_payment != $request->final_amount) {
            return redirect()->back()->with('fail', 'Cash payment and Online payment total must be equal to Final amount.');
        }

        try {
            DB::beginTransaction();
            $order = Order::find($id);
            $order->order_status = 'delivered';
            $order->save();

            $invoiceData = app(\App\Http\Controllers\SaleController::class)->generateInvoiceNumber();

            $invoiceNo = $invoiceData['number'];
            $serial = $invoiceData['serial'];

            $sales = Sale::create([
                'order_id' => $order->id,
                'discount' => $request->discount ?? 0,
                'invoice_number' => $invoiceNo,
                'invoice_serial' => $serial,
                'gst' => $request->gst_applicable,
                'total' => $request->final_amount,
                'cash_order' => 1,
                'sale_by' => auth()->user()->id,
            ]);
            if ($order->customer_id) {
                $sales->customer_id = $order->customer_id;
                $sales->save();
            }
            $orderItems = OrderItem::where('order_id', $order->id)->get();

            $salesmanBillPoint = 0;
            foreach ($orderItems as $key => $item) {
                if (isset($request->quantity[$key])) {
                    $item->quantity = max(1, (int) $request->quantity[$key]);
                    $item->price = $request->price[$key];
                    $item->total = $item->price * $item->quantity;
                    $item->save();
                }
                $salesmanPoint = Product::where('id', $item->product_id)->value('salesmen_point_price');
                $salesmanBillPoint = $salesmanBillPoint + $salesmanPoint * $item->quantity;
                $product = Product::find($item->product_id);

                SalesItems::create([
                    'sale_id' => $sales->id,
                    'product_id' => $item->product_id,
                    'purchase_price' => $product->purchase_withgst,
                    'quantity' => $item->quantity,
                    'price' => $request->price[$key],
                    'warranty' => $request->warranty[$key],
                    'sl_no' => $request->slno[$key],
                    'gst' => $item->product->category->gst,
                ]);

                $product->stock = $product->stock - $item->quantity;
                $product->save();
            }
            if ($order->direct_salesman) {
                PointsHistories::create([
                    'user_id' => $order->salesman_id,
                    'points' => $salesmanBillPoint,
                    'sales_id' => $sales->id,
                    'date' => Carbon::now()->format('Y-m-d'),
                ]);

                $lastBalance = LedgerEntries::where('user_id', $order->salesman_id)
                    ->latest('id')
                    ->value('balance_after') ?? 0;
                $newBalance = $lastBalance + $salesmanBillPoint;

                LedgerEntries::create([
                    'entity_type' => 'user',
                    'user_id' => $order->salesman_id,
                    'invoice_id' => $invoiceNo,
                    'transaction_type' => 'commission',
                    // 'method' => 'online',
                    'type' => 'credit',
                    'amount' => $salesmanBillPoint,
                    'balance_after' => $newBalance,
                ]);
            }
            if (isset($request->cash_payment)) {
                $cashPayment = PaymentMaster::create([
                    'flow_type' => 'inflow',
                    'invoice_no' => $invoiceNo,
                    'invoice_type' => 'sale',
                    // 'customer_id' => $customer->id,
                    'amount' => $request->cash_payment,
                    'method' => 'cash',
                    'payment_date' => Carbon::now()->format('Y-m-d'),
                ]);
                if ($order->customer_id) {
                    $cashPayment->customer_id = $order->customer_id;
                    $sales->save();
                }
            }
            if (isset($request->online_payment)) {
                $onlinePayment = PaymentMaster::create([
                    'flow_type' => 'inflow',
                    'invoice_no' => $invoiceNo,
                    'invoice_type' => 'sale',
                    // 'customer_id' => $customer->id,
                    'amount' => $request->online_payment,
                    'method' => 'online',
                    'bank_id' => $request->bank_id,
                    'payment_date' => Carbon::now()->format('Y-m-d'),
                ]);

                $bank_data = Bank::find($request->bank_id);
                $bankBalance = $bank_data->amount;


                $onlinePayment->last_bank_balance = $bankBalance + $request->online_payment;
                $bank_data->amount = $bankBalance + $request->online_payment;
                $bank_data->save();
                $onlinePayment->save();


                if ($order->customer_id) {
                    $onlinePayment->customer_id = $order->customer_id;
                    $sales->save();
                }
            }
            DB::commit();
            return redirect()->route('sale.list')->with('success', 'Order process to sale success');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('fail', 'Somthing error occurs while Processing!');
        }
    }

    public function orderCancel($id)
    {
        $order = Order::find($id);
        if ($order->order_status == 'pending') {
            $order->order_status = 'canceled';
            $order->save();
            return redirect()->route('order.view')->with('success', 'Order canceled successfully.');
        } else {
            return redirect()->back()->with('fail', 'Order status already ' . $order->order_status . '.');
        }
    }

    public function orderSaleView($id)
    {
        $sale = Sale::where('order_id', $id)->first();
        if ($sale) {
            return redirect()->route('sale.show', $sale->id);
        } else {
            return redirect()->back()->with('fail', 'No sale record found for this order.');
        }
    }

}
