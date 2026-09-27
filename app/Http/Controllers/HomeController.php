<?php

namespace App\Http\Controllers;

use App\Jobs\SendWhatsappMessage;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\RewardPoint;
use App\Models\RewardPointTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Http;
use Cache;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function userdashboard()
    {
        $page_title = 'User Dashboard';
        $userPhone = auth()->user()->phone;

        $orders = Order::with('orderItems.product')
            ->whereHas('customer', function ($query) use ($userPhone) {
                $query->where('phone', $userPhone);
            })
            ->latest()
            ->get();

        $reward = RewardPoint::where('mobile', $userPhone)->first();
        $transactions = RewardPointTransaction::where('mobile', $userPhone)
            ->latest()
            ->paginate(50);

        return view('user.userdashboard', compact('page_title', 'orders', 'reward', 'transactions'));
    }

    public function salesmandashboard()
    {
        return view('salesmandashboard', ['page_title' => 'Salesman Dashboard']);
    }

    public function sellerDashboard()
    {
        return view('sellerdashboard', ['page_title' => 'Seller Dashboard']);
    }
    public function dashboard()
    {
        $page_title = 'Dashboard';
        $totalStockAmount = Cache::remember('total_stock_amount', 60, function () {
            return Product::selectRaw('SUM(COALESCE(stock, 0) * COALESCE(purchase_withgst, 0)) as totalStockAmount')
                ->value('totalStockAmount');
        });

        $data = compact('page_title', 'totalStockAmount');
        return view('dashboard')->with($data);
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'digits:10'],
        ]);

        $phone = $request->phone;
        $otp = (string) rand(1000, 9999);

        Cache::put('otp_' . $phone, $otp, 600);

        $message = "*IconComputer* 💻\nYour verification code is: *{$otp}*\nValid for 10 minutes.";
        $number = '91' . $phone;

        try {
            Http::connectTimeout(2)->timeout(5)->get(
                'https://nextsms.co.in/api/whatsapp/send',
                [
                    'receiver' => $number,
                    'msgtext' => $message,
                    'token' => config('services.whatsapp.token'),
                ]
            );
        } catch (\Throwable $e) {
            \Log::warning('WhatsApp OTP send failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send OTP via WhatsApp. Please check number and try again.'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'OTP sent to your WhatsApp number successfully.'
        ]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'digits:10'],
            'otp' => ['required', 'digits:4'],
        ]);

        $phone = $request->phone;
        $otp = $request->otp;

        $cachedOtp = Cache::get('otp_' . $phone);

        if ($cachedOtp && (string) $cachedOtp === (string) $otp) {
            session(['verified_phone_' . $phone => true]);
            return response()->json([
                'success' => true,
                'message' => 'WhatsApp number verified successfully.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid or expired OTP. Please try again.'
        ], 422);
    }

    public function guestOrder(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|integer|digits:10',
            'whatsapp' => 'required|integer|digits:10',
            'address' => 'required|string|max:255',
            'district' => 'required|string|max:50',
            'pincode' => 'required|digits:6',
            'cart' => 'required|json',
            'delivery' => 'required|in:Yes,No',
        ]);

        try {
            $cart = json_decode($request->cart, true);
            $customer = Customer::where('phone', $request->phone)->first();

            if (!isset($customer)) {
                $isVerified = session('verified_phone_' . $request->phone, false);
                if (!$isVerified && $request->filled('otp')) {
                    $cachedOtp = Cache::get('otp_' . $request->phone);
                    if ($cachedOtp && (string) $cachedOtp === (string) $request->otp) {
                        $isVerified = true;
                    }
                }

                if (!$isVerified) {
                    return response()->json([
                        'error' => 'Please verify your WhatsApp number with OTP first.'
                    ], 422);
                }

                $customer = new Customer();
                $customer->name = $request->name;
                $customer->phone = $request->phone;
                $customer->wpnumber = $request->whatsapp;
                $customer->address = $request->address;
                $customer->dist = $request->district;
                $customer->pin = $request->pincode;
                $customer->save();
            } else {
                $customer->name = $request->name;
                $customer->wpnumber = $request->whatsapp;
                $customer->address = $request->address;
                $customer->dist = $request->district;
                $customer->pin = $request->pincode;
                $customer->save();
            }

            // Create or get user account for user dashboard order access
            $user = User::where('phone', $request->phone)->first();
            $isNewUser = false;
            $generatedPassword = null;

            if (!$user) {
                $generatedPassword = (string) rand(100000, 999999);
                $user = User::create([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'wpnumber' => $request->whatsapp,
                    'password' => Hash::make($generatedPassword),
                    'role' => 'user',
                ]);
                $isNewUser = true;
            }
            if (!Auth::check()) {
                Auth::login($user);
            }

            $order = new Order();
            $order->customer_id = $customer->id;
            $order->order_status = 'pending';
            if ($request->delivery == 'Yes') {
                $order->delivery_charges = '1';
            }
            $order->source = 'Web';
            $order->save();

            foreach ($cart as $item) {
                $productId = $item['id'];
                $quantity = $item['qty'];

                $product = Product::find($productId);
                if ($product) {
                    $orderItem = new OrderItem();
                    $orderItem->order_id = $order->id;
                    $orderItem->product_id = $product->id;
                    $orderItem->quantity = $quantity;
                    $orderItem->price = $product->online_price ?: $product->price ?: 0;
                    $orderItem->delivery_charges = ($request->delivery == 'Yes') ? ($product->delivery_charges_amount * $quantity) : 0;
                    $orderItem->total = $orderItem->price * $quantity;
                    $orderItem->gst = $product->category->gst ?? 0;
                    $orderItem->save();
                }
            }

            session()->forget('verified_phone_' . $request->phone);

            $message = "*Hi {$request->name}!* 👋\n\n"
                . "Your order has been placed successfully.\n"
                . "*Order ID:* #{$order->id}\n\n";

            if ($isNewUser && $generatedPassword) {
                $message .= "🔑 *Your Account Login Credentials:*\n"
                    . "*User ID / Phone:* {$request->phone}\n"
                    . "*Password:* {$generatedPassword}\n"
                    . "_Use these details to log in for future orders!_\n\n";
            }

            $message .= "We will contact you soon.\n"
                . "এই অর্ডারটি ৩ দিনের(৭২ ঘন্টা ) জন্য গ্রহণযোগ্য হবে।\n"
                . "_Thank you for shopping with us!_\n\n"
                . "*Team IconComputer* 💻";
            $number = '91' . $request->whatsapp;

            try {
                Http::connectTimeout(2)->timeout(5)->get(
                    'https://nextsms.co.in/api/whatsapp/send',
                    [
                        'receiver' => $number,
                        'msgtext' => $message,
                        'token' => config('services.whatsapp.token'),
                    ]
                );
            } catch (\Throwable $notificationException) {
                \Log::warning('Order created but WhatsApp notification failed.', [
                    'order_id' => $order->id,
                    'error' => $notificationException->getMessage(),
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ], 500);
        }

        $responseData = [
            'message' => 'Order placed successfully. Please check your WhatsApp.',
            'is_new_user' => $isNewUser
        ];

        if ($isNewUser && $generatedPassword) {
            $responseData['credentials'] = [
                'user_id' => $request->phone,
                'password' => $generatedPassword
            ];
        }

        return response()->json($responseData, 200);
    }


    // public function home(Request $request)
    // {
    //     // Lazy loading with pagination
    //     $products = Product::with(['details', 'category', 'brand'])
    //         ->latest()
    //         ->paginate(20); // load 20 items at once


    //         $catagories=Category::orderBy('name','asc')->get();
    //     // Detect AJAX request for lazy loading
    //     if ($request->ajax()) {
    //         $view = view('partials._products', compact('products'))->render();
    //         return response()->json([
    //             'html' => $view,
    //             'next_page' => $products->nextPageUrl()
    //         ]);
    //     }

    //     return view('home', compact('products','catagories'));
    // }

    public function home(Request $request)
    {
        $topProducts = Product::with(['category', 'brand', 'details', 'specialOffer.freeProduct', 'freeProduct'])
            ->whereNotNull('details_id')
            ->where('online_price', '>', 0)
            ->where('stock', '>', 0)
            ->whereHas('details', function ($query) {
                $query->where('status', '1');
                $query->where('display', 'top');
            })->get();

        // Base query
        $query = Product::with(['details', 'category', 'brand', 'specialOffer.freeProduct', 'freeProduct'])
            ->where('online_price', '>', 0)
            ->where('stock', '>', 0)
            ->whereHas('details', function ($q) {
                $q->where('status', '=', 1);
            })->latest(); // Default sorting

        // 1️⃣ Category filter (from category bar)
        if ($request->filled('category')) {
            $categoryId = $request->category;
            $query->where(function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId)
                    ->orWhereHas('category', function ($subQ) use ($categoryId) {
                        $subQ->where('name', 'like', "%{$categoryId}%");
                    });
            });
        }

        // 2️⃣ Search filter (smart multi-term search)
        if ($request->filled('search')) {
            $searchTerms = explode(' ', $request->search); // Split by space

            $query->where(function ($q) use ($searchTerms) {
                foreach ($searchTerms as $term) {
                    $term = trim($term);
                    if (empty($term))
                        continue;

                    $q->where(function ($subQ) use ($term) {
                        $subQ->where('model', 'like', "%{$term}%")
                            ->orWhereHas('brand', function ($brandQ) use ($term) {
                                $brandQ->where('name', 'like', "%{$term}%");
                            })
                            ->orWhereHas('details', function ($detailsQ) use ($term) {
                                $detailsQ->where('description', 'like', "%{$term}%");
                            })
                            ->orWhereHas('category', function ($catQ) use ($term) {
                                $catQ->where('name', 'like', "%{$term}%");
                            });
                    });
                }
            });
        }

        // 3️⃣ Paginate (ALWAYS lazy load 20 items)
        $products = $query->paginate(20)->withQueryString();

        // 4️⃣ Category list (for top category bar)
        $catagories = Cache::remember('product_categories', 1440, function () {
            return Category::orderBy('name', 'asc')->get();
        });

        // 5️⃣ Lazy load AJAX response
        if ($request->ajax()) {
            $view = view('partials._products', compact('products'))->render();
            return response()->json([
                'html' => $view,
                'next_page' => $products->nextPageUrl()
            ]);
        }

        // 6️⃣ Normal page load
        return view('home', compact('products', 'catagories', 'topProducts'));
    }

    public function contact()
    {
        return view('contact');
    }
    public function support()
    {
        return view('support');
    }

    public function checkPhone(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'digits:10'],
        ]);
        //dd($request);

        $user = Customer::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json([
                'found' => false,
                'message' => 'No customer found.'
            ]);
        }

        return response()->json([
            'found' => true,
            'data' => [
                'name' => $user->name,
                'phone' => $user->phone,
                'whatsapp' => $user->wpnumber,

                'district' => $user->dist,
                'pincode' => $user->pin,
                'address' => $user->address,

            ]
        ]);
    }


}
