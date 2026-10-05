<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Jobs\SendWhatsappMessage;
use App\Models\Category;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Expenses;
use App\Models\HelplineNumber;
use App\Models\Order;
use App\Models\OrderFreeGift;
use App\Models\OrderItem;
use App\Models\PaymentMaster;
use App\Models\PointsHistories;
use App\Models\Product;
use App\Models\ProductDetails;
use App\Models\RewardPoint;
use App\Models\RewardPointTransaction;
use App\Models\Salesmen;
use App\Models\ServiceCall;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use DB;
use Dotenv\Exception\ValidationException;
use Exception;
use Hash;
use Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Storage;
use Validator;

class HomeController extends Controller
{
    private $cloudinaryService;

    public function formatWhatsAppNumber($phone)
    {
        // Remove all non-digit characters
        $phone = preg_replace('/\D/', '', $phone);

        // Remove leading 0 if present (e.g., 091 -> 91)
        $phone = ltrim($phone, '0');

        // If number starts with 91 and is 12 digits total → perfect
        if (preg_match('/^91\d{10}$/', $phone)) {
            return $phone; // Already correct: 919876543210
        }

        // If it's a 10-digit Indian number without country code → add 91
        if (preg_match('/^\d{10}$/', $phone)) {
            return '91' . $phone; // e.g., 9876543210 → 919876543210
        }

        // If it starts with +91 (we already removed +), it will be caught above
        // Fallback: try to extract last 10 digits and prepend 91
        if (strlen($phone) > 10) {
            $phone = substr($phone, -10); // take last 10 digits
            return '91' . $phone;
        }

        throw new \Exception("Invalid Indian phone number: {$phone}");
    }
    public function pointget($id) //point/{id}
    {
        // Find salesmen by user_id
        $points = PointsHistories::where('user_id', $id)->get();
        $response = [
            'status' => 1,
            'message' => 'Points Data get successfully',
            'points' => $points
        ];
        return response()->json($response, 200);
    }
    public function getAllCategory() // /category
    {
        $category = Category::where('active', 1)->get();
        return response()->json([
            'result' => $category,
            'message' => 'All Category retrieved successfully',
            'status' => 'success'
        ], 200);
    }
    public function fetchProductsWithDetails() // /product
    {
        // Fetch products with their details
        $products = Product::with(['details', 'category', 'brand'])
            ->whereNotNull('details_id')
            ->where('stock', '>', 0)
            ->whereHas('details', function ($query) {
                $query->where('status', '1');
            })
            ->get();

        if (count($products) > 0) {
            return response()->json([
                'success' => true,
                'data' => $products
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        }
    }
    public function checkProductActive($id)  // /check-product/{id}
    {
        $products = Product::with(['details', 'category', 'brand'])->where('id', $id)
            ->where('stock', '>', 0)
            ->whereHas('details', function ($query) {
                $query->where('status', '1');
            })->first();
        return response()->json([
            'success' => true,
            'data' => $products ? $products : null
        ]);
    }
    public function fetchProductsWithDetailsAll() // /productAll
    {
        // Fetch products with their details
        $products = Product::with(['details', 'category', 'brand'])->latest()
            ->get();

        if (count($products) > 0) {
            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        }
    }
    public function fetchProductsWithDetailswithid($id)// product/{id} 
    {
        $products = Product::with(['details', 'category', 'brand'])
            ->where('category_id', $id)
            ->where('stock', '>', 0)
            ->whereNotNull('details_id')
            ->whereHas('details', function ($query) {
                $query->where('status', '1');
            })
            ->get();
        // ->get();
        // dd($products);
        if (count($products) > 0) {
            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        }
    }
    public function fetchProductsWithDetailswithBarcode($id) // /productbarcode/{id}
    {
        $products = Product::with(['details', 'category', 'brand'])->where('code', $id)
            ->whereNotNull('details_id')
            ->where('stock', '>', 0)
            // ->whereHas('details', function ($query) {
            //     $query->where('status', '1');
            // })
            ->first();

        // dd($products);

        if ($products) {
            return response()->json([
                'success' => true,
                'data' => $products
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        }
    }
    public function fetchTopCarousel() // /fetchTopCarousel
    {
        // Fetch products with their details
        // $products = Product::with(['details','category','brand'])->where('id', $id)->get();
        $products = Product::with(['category', 'brand', 'details'])
            ->whereNotNull('details_id')
            ->where('stock', '>', 0)
            ->whereHas('details', function ($query) {
                $query->where('status', '1');
                $query->where('display', 'top');
            })->get();

        if (count($products) > 0) {
            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        }
    }
    public function fetchBottomCarousel() // /fetchBottomCarousel
    {
        // Fetch products with their details
        // $products = Product::with(['details','category','brand'])->where('id', $id)->get();
        $products = Product::with(['category', 'brand', 'details'])
            ->whereNotNull('details_id')
            ->where('stock', '>', 0)
            ->whereHas('details', function ($query) {
                $query->where('status', '1');
                $query->where('display', 'bottom');
            })->get();

        if (count($products) > 0) {
            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        }
    }
    public function fetchHotCarousel() // /fetchHotCarousel
    {
        // Fetch products with their details
        // $products = Product::with(['details','category','brand'])->where('id', $id)->get();
        $products = Product::with(['category', 'brand', 'details'])
            ->whereNotNull('details_id')
            ->where('stock', '>', 0)
            ->whereHas('details', function ($query) {
                $query->where('status', '1');
                $query->where('display', 'hot');
            })->get();

        if (count($products) > 0) {
            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.'
            ], 404);
        }
    }
    public function fetchSpecialOfferProducts() // /fetchSpecialOfferProducts or /special-offers
    {
        $now = now();

        $products = Product::with(['category', 'brand', 'details', 'specialOffer.freeProduct.details', 'freeProduct.details'])
            ->where('online_price', '>', 0)
            ->where('stock', '>', 0)
            ->whereHas('details', function ($q) {
                $q->where('status', '=', 1);
            })
            ->where(function ($q) use ($now) {
                $q->whereHas('specialOffer', function ($soQ) use ($now) {
                    $soQ->where('is_active', true)
                        ->where('start_date', '<=', $now)
                        ->where('end_date', '>=', $now);
                })
                ->orWhere(function ($subQ) {
                    $subQ->where('special_offer', 'yes')
                        ->whereDoesntHave('specialOffer');
                });
            })
            ->latest()
            ->get();

        if (count($products) > 0) {
            return response()->json([
                'success' => true,
                'data' => $products
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No special offer products found.'
            ], 404);
        }
    }
    public function search(Request $request)
    {
        $request->validate([
            'search' => 'required|string',
        ]);

        $search = $request->search;

        // Split into words
        $keywords = explode(' ', $search);

        $products = Product::with(['details', 'category', 'brand'])
            ->where('stock', '>', 0)
            ->whereHas('details', function ($q) {
                $q->where('status', '1');
            })
            ->where(function ($q) use ($keywords) {

                foreach ($keywords as $word) {
                    $q->where(function ($sub) use ($word) {

                        $sub->whereHas('category', function ($c) use ($word) {
                            $c->where('name', 'LIKE', "%$word%");
                        })
                            ->orWhereHas('brand', function ($b) use ($word) {
                                $b->where('name', 'LIKE', "%$word%");
                            })
                            ->orWhere('model', 'LIKE', "%$word%")
                            ->orWhere('code', 'LIKE', "%$word%");
                    });
                }
            })
            ->get();

        return response()->json([
            'status' => 1,
            'data' => $products,
        ]);
    }

    public function createService(Request $request) // /service-request
    {
        // dd($request->all());
        try {

            $request->validate([
                'name' => 'required|string|max:100',
                'phone' => 'required|digits:10',
                'address' => 'required',
                'pin' => 'required|min:6',
                'note' => 'nullable|string|max:255',
                'invoice_images' => 'required|array',
                'invoice_images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $invoicePath = [];
            if ($request->hasFile('invoice_images')) {
                foreach ($request->file('invoice_images') as $image) {
                    $localPath = $image->store('icon_computer/invoice_image', 'public');
                    $image_url = env('APP_URL') . Storage::url($localPath);
                    // Store the path without adding $baseDir again
                    $invoicePath[] = $image_url;
                }
            }
            // dd($invoicePath);
            // Check for duplicate service request within 3 days (72 hours)
            $threeDaysAgo = now()->subDays(3);
            $existingService = ServiceCall::where('phone', $request->phone)
                ->where('created_at', '>=', $threeDaysAgo)
                ->first();

            if ($existingService) {
                return response()->json([
                    'message' => '⚠️ একই কাস্টমারের ডাটা গত ৩ দিনের মধ্যে জমা নেওয়া হয়েছে। ৩ দিনের মধ্যে পুনরায় সাবমিট করা যাবে না।',
                    'status' => 0
                ], 400);
            }

            $service = ServiceCall::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'address' => $request->address,
                'pin' => $request->pin,
                'note' => $request->note,
                'invoice_image' => json_encode($invoicePath)
            ]);

            // Format the message
            $message = "*Your service request has been submitted.*\n*We will contact you soon.*\n\nThank you!\n*ICON COMPUTER*";
            $phone = $this->formatWhatsAppNumber($request->phone);
            if ($service) {
                $response = Http::get('https://nextsms.co.in/api/whatsapp/send', [
                    'receiver' => $phone,
                    'msgtext' => $message,
                    'token' => '6691f36fe7d35b0304cc56eab283e99b0b932e80fd999dedb8d811a45c24537a',
                ]);
                return response()->json([
                    'message' => "Service request submited successfully",
                    'status' => 1
                ], 200);
            } else {
                return response()->json([
                    'result' => 'Failed',
                    'status' => 0
                ], 400);
            }
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed!',
            ], 422);
        }
    }
    public function helplinenumbers() // /help
    {
        $helplines = HelplineNumber::all();
        return response([
            'helplines' => $helplines,
            'status' => 1
        ], 200);
    }

    public function productDetailsUpdate(Request $request, $id) // /product/details/update/{id}
    {
        $validator = Validator::make($request->all(), [
            'mrp' => 'required|integer',
            'description' => 'required|string', // new_password_confirmation
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $productDetails = ProductDetails::findOrFail($id);
        $productDetails->mrp = $request->mrp;
        $productDetails->description = $request->description;
        $productDetails->save();
        $response = [
            'status' => 1,
            'message' => 'Product Details updated successfully',
        ];
        return response()->json($response, 200);
    }

    public function updateThambnail(Request $request, $productId)
    {
        $validator = Validator::make($request->all(), [
            'thumbnail_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $id = Product::where('id', $productId)->value('details_id');
        $thumbnailPath = null;
        if (!$id) {
            $details = ProductDetails::create([
                'status' => 1,
            ]);

            // Handle thumbnail image upload
            if ($request->hasFile('thumbnail_image')) {
                $folderPath = 'icon_computer/product_images/thumbnail';
                $localPath = $request->file('thumbnail_image')->store($folderPath, 'public');
                $thumbnail = env('APP_URL') . Storage::url($localPath);
                $thumbnailPath = $thumbnail;
            }
            if ($thumbnailPath) {
                $details->thumbnail_image = $thumbnailPath;
                $details->save();
            }
            // Update product with new details_id
            $product = Product::find($productId);
            $product->details_id = $details->id;
            $product->save();

            $response = [
                'status' => 'success',
                'message' => 'Thumbnail image add successfully',
                'thumbnail_image' => $details->thumbnail_image,
            ];
            return response()->json($response, 200);
        } else {
            $details = ProductDetails::find($id);
            // Handle thumbnail image upload
            if ($request->hasFile('thumbnail_image')) {
                $folderPath = 'icon_computer/product_images/thumbnail';
                $localPath = $request->file('thumbnail_image')->store($folderPath, 'public');
                $thumbnail = env('APP_URL') . Storage::url($localPath);
                $thumbnailPath = $thumbnail;
                // Delete the previous thumbnail image
                $relativePath = str_replace(env('APP_URL') . '/storage/', '', $details->thumbnail_image);
                // Delete the file
                if (Storage::disk('public')->exists($relativePath)) {
                    Storage::disk('public')->delete($relativePath);
                }
            }
            if ($thumbnailPath) {
                $details->thumbnail_image = $thumbnailPath;
                $details->save();
            }
            $response = [
                'status' => 'success',
                'message' => 'Thumbnail image add successfully',
                'thumbnail_image' => $details->thumbnail_image,
            ];
            return response()->json($response, 200);
        }
    }

    public function productImageAdd(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'images.*' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $details = ProductDetails::findOrFail($id);
        // Handle multiple image uploads
        if ($request->hasFile('images')) {
            // dd($request->all());
            foreach ($request->file('images') as $image) {
                $folderPath = 'icon_computer/product_images/images';
                $localPath = $image->store($folderPath, 'public');
                $image_url = env('APP_URL') . Storage::url($localPath);
                $imagePaths[] = $image_url;
            }
        }
        // Fetch the existing product details
        $existingImages = json_decode($details->image, true) ?? [];

        // Merge existing images with new uploaded images
        $finalImages = array_merge($existingImages, $imagePaths);

        $details->update([
            'image' => json_encode($finalImages),
            'description' => $request->description,
            'mrp' => $request->mrp,
            'status' => 1,
        ]);

        $response = [
            'status' => 1,
            'message' => 'Image add successfully',
            'images' => $details->image,
        ];
        return response()->json($response, 200);
    }
    ///////////////////////////////////////////////////////////////

    public function getByUserId($user_id)
    {
        // Find salesmen by user_id
        $salesmen = Salesmen::where('user_id', $user_id)->get();

        // Return response
        if ($salesmen->isEmpty()) {
            return response()->json(['message' => 'No salesmen found for this user.'], 404);
        }

        return response()->json($salesmen, 200);
    }

    // public function managerpayment()
    // {
    //     // Find salesmen by user_id
    //     $salesmen = PaymentMaster::get();
    //     return response()->json($salesmen, 200);
    // }
    public function changePasswordById(Request $request, $id)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:6', // new_password_confirmation
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Find the user by ID
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found.',
            ], 404);
        }

        // Check if the current password matches
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'The current password is incorrect.',
            ], 403);
        }

        // // Update the password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'status' => $user,
            'message' => 'Password updated successfully for user ID ' . $id,
        ]);
    }
    public function updateUser(Request $request, $id)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required',
            'phone' => 'sometimes|nullable',
            'wpnumber' => 'sometimes|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Find the user
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found.',
            ], 404);
        }

        // Update user fields if provided
        if ($request->has('name'))
            $user->name = $request->name;
        if ($request->has('phone'))
            $user->phone = $request->phone;
        if ($request->has('wpnumber'))
            $user->wpnumber = $request->wpnumber;

        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully.',
            'user' => $user,
        ]);
    }





    /*
    public function fetchProductsWithDetailswithBarcode($id)
    {
     $products = Product::with(['details','category','brand'])->where('code', $id)->whereNotNull('details_id')->get();

    if($products && $products->details->status === 1){
        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    } else {
        return response()->json([
           'success' => false,
           'message' => 'Product not found.'
        ], 404);
    }
    }*/



    // added by giri 7-2-25


    public function fetchOrder($id)
    {
        $history = Order::with([
            'customer:id,name,phone,wpnumber,address,pin,gst_number',
            'orderItems:order_id,product_id,quantity,price,total,delivery_charges',
            'orderItems.product:id,brand_id,category_id,model',
            'orderItems.product.brand:id,name',
            'orderItems.product.category:id,name'
        ])
            ->where('salesman_id', $id)
            ->select('id', 'order_status', 'customer_id', 'delivery_charges', 'delivery_date', 'created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $history,
        ], 200);
    }



    public function updateProductDetails(Request $request)
    {
        $thumbnailPath = null;
        $imagePaths = [];

        // dd($request->all());
        // Handle thumbnail image upload
        if ($request->hasFile('thumbnail_image')) {
            $folderPath = 'icon_computer/product_images/thumbnail';
            // $thumbnail = $this->cloudinaryService->uploadImage($request->file('thumbnail_image'), $folderPath);
            $localPath = $request->file('thumbnail_image')->store($folderPath, 'public');
            // $filePath = storage_path('app/public/' . $localPath);
            // dd($filePath);
            // Get public URL
            $thumbnail = env('APP_URL') . Storage::url($localPath);
            $thumbnailPath = $thumbnail;

        }

        // Handle multiple image uploads
        if ($request->hasFile('images')) {
            // dd($request->all());
            foreach ($request->file('images') as $image) {
                $folderPath = 'icon_computer/product_images/images';
                // $image_url = $this->cloudinaryService->uploadImage($image, $folderPath);
                $localPath = $image->store($folderPath, 'public');
                // $filePath = storage_path('app/public/' . $localPath);
                // dd($filePath);
                // Get public URL
                $image_url = env('APP_URL') . Storage::url($localPath);
                // Store the path without adding $baseDir again
                $imagePaths[] = $image_url;
            }
        }
        // dd($imagePaths, $thumbnailPath);
        // dd($imagePaths, $thumbnailPath);
        // Create a new product with all the data including image paths
        $product = ProductDetails::create([
            'description' => $request->description,
            'mrp' => $request->mrp,
            'status' => 1,
            'thumbnail_image' => $thumbnailPath,
            'image' => json_encode($imagePaths),
        ]);
        Product::find($request->product_id)->update([
            'details_id' => $product->id,
        ]);
        return response([
            'result' => $product,
            'message' => 'Product Update Successfully'
        ], 201);
    }
    public function updateProductDetails2(Request $request, $id)
    {
        $request->validate([
            'description' => 'required',
            'mrp' => 'required|numeric',
        ]);

        $product = ProductDetails::find($id);
        $product->description = $request->description;
        $product->mrp = $request->mrp;
        $product->status = 1;
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product
        ], 200);
    }
    public function updateProductDetailsExit(Request $request, $productId)
    {

        $id = Product::where('id', $productId)->value('details_id');

        if (!$id) {
            return response()->json([
                'status' => 0,
                'message' => 'Product details not found for the given product ID.'
            ], 404);
        }
        $thumbnailPath = null;
        $imagePaths = [];

        $details = ProductDetails::find($id);
        // Handle thumbnail image upload
        if ($request->hasFile('thumbnail_image')) {
            $folderPath = 'icon_computer/product_images/thumbnail';
            $localPath = $request->file('thumbnail_image')->store($folderPath, 'public');
            $thumbnail = env('APP_URL') . Storage::url($localPath);
            $thumbnailPath = $thumbnail;
            // Delete the previous thumbnail image
            $relativePath = str_replace(env('APP_URL') . '/storage/', '', $details->thumbnail_image);
            // Delete the file
            if (Storage::disk('public')->exists($relativePath)) {
                Storage::disk('public')->delete($relativePath);
            }
        }

        // Handle multiple image uploads
        if ($request->hasFile('images')) {
            // dd($request->all());
            foreach ($request->file('images') as $image) {
                $folderPath = 'icon_computer/product_images/images';
                $localPath = $image->store($folderPath, 'public');
                $image_url = env('APP_URL') . Storage::url($localPath);
                $imagePaths[] = $image_url;
            }
        }
        // Fetch the existing product details
        $existingImages = json_decode($details->image, true) ?? [];

        // Merge existing images with new uploaded images
        $finalImages = array_merge($existingImages, $imagePaths);

        $details->update([
            'image' => json_encode($finalImages),
            'description' => $request->description,
            'mrp' => $request->mrp,
            'status' => 1,
        ]);
        if ($thumbnailPath) {
            $details->thumbnail_image = $thumbnailPath;
            $details->save();
        }
        $product = Product::with(['category', 'brand', 'details'])->find($request->product_id);
        return response([
            'result' => $product,
            'message' => 'Product Update Successfully',
            'status' => 1,
        ], 201);
    }

    public function deleteProductImage(int $id, Request $request)
    {
        $request->validate([
            'image' => 'required|string',
        ]);
        $image = ProductDetails::find($id);
        $imagePaths = json_decode($image->image, true);
        if ($request->image) {
            // $this->cloudinaryService->deleteImage($request->image);
            $relativePath = str_replace(env('APP_URL') . '/storage/', '', $request->image);
            // Delete the file
            // dd($relativePath);
            if (Storage::disk('public')->exists($relativePath)) {
                //   dd($relativePath);
                Storage::disk('public')->delete($relativePath);
            }
            // Remove the deleted image from the images array
            $imagePaths = array_filter($imagePaths, fn($img) => $img !== $request->image);
            $image->image = json_encode($imagePaths);
            $image->save();
            return response([
                'result' => 'Image deleted successfully',
                'status' => 1
            ], 200);
        } else {
            return response([
                'result' => 'No image found to delete',
                'status' => 0
            ], 404);
        }

    }

    public function customerSave(Request $request)
    {
        // return $request;
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'wpnumber' => 'required',
            'address' => 'required',
            'pin' => 'required',
            'salesman_id' => 'required',
        ]);


        // Check if the phone number already exists
        $existingCustomer = Customer::where('phone', $request->phone)->first();
        //  dd($existingCustomer);
        if ($existingCustomer) {
            // If the phone number exists and has a different salesman_id, return an error
            if ($existingCustomer->salesman_id) {
                if ($existingCustomer->salesman_id != $request->salesman_id) {
                    return response([
                        'result' => 'Phone number already exists with a different Salesman. Enter another phone number.',
                        'status' => 'Failed'
                    ], 401);
                } else if ($existingCustomer->salesman_id == $request->salesman_id) {
                    // If the salesman_id is the same, return the existing customer
                    return response([
                        'result' => $existingCustomer,
                        'status' => 200
                    ], 200);
                }
            }
            //   dd($request->salesman_id);
            $existingCustomer->salesman_id = $request->salesman_id;
            $existingCustomer->save();
            // If the salesman_id is the same, return the existing customer
            return response([
                'result' => $existingCustomer,
                'status' => 200,
            ], 200);
        }

        // If the phone number does not exist, create a new customer
        $customer = Customer::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'wpnumber' => $request->wpnumber,
            'address' => $request->address,
            'pin' => $request->pin,
            'salesman_id' => $request->salesman_id,
        ]);

        return response([
            'result' => $customer,
            'status' => 200
        ], 200);
    }

    public function updateProductDetailId(Request $request)
    {
        // Fetch products with their details
        // $products = Product::with(['details','category','brand'])->where('id', $id)->get();
        $products = Product::where('model', $request->model)->get();
        $data = json_decode($products, true);

        foreach ($data as $productData) {
            $product = Product::find($productData['id']);

            if ($product) {

                $product->update([
                    'details_id' => $request->id,
                ]);
            } else {
                echo "Product with ID  not found.\n";
            }

        }
        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
    public function customerSave2(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required',
            'wpnumber' => 'required',
            'address' => 'required',
            'pin' => 'required',
            'salesman_id' => 'required',
        ]);

        // Check if the phone number exists for a different salesman_id
        $existingCustomer = Customer::where('phone', $request->phone)->first();

        if ($existingCustomer) {
            if ($existingCustomer->salesman_id) {
                if ($existingCustomer->salesman_id != $request->salesman_id) {
                    // Phone number exists with a different salesman_id
                    return response([
                        'result' => 'Already exist, Enter another phone Number',
                        'status' => 'Failed'
                    ], 200);
                }
            } else {
                $existingCustomer->salesman_id = $request->salesman_id;
                $existingCustomer->phone = $request->phone;
                $existingCustomer->name = $request->name;
                $existingCustomer->wpnumber = $request->wpnumber;
                $existingCustomer->address = $request->address;
                $existingCustomer->pin = $request->pin;
                $existingCustomer->save();

                if ($existingCustomer) {
                    return response([
                        'result' => $existingCustomer,
                        'status' => 200
                    ], 200);
                } else {
                    return response([
                        'result' => 'Failed',
                        'status' => 'Failed'
                    ], 200);
                }
            }

        }

        // Proceed to save the customer
        $customer = Customer::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'wpnumber' => $request->wpnumber,
            'address' => $request->address,
            'pin' => $request->pin,
            'salesman_id' => $request->salesman_id,
        ]);

        if ($customer) {
            return response([
                'result' => $customer,
                'status' => 200
            ], 200);
        } else {
            return response([
                'result' => 'Failed',
                'status' => 'Failed'
            ], 200);
        }
    }
    public function order(Request $request)
    {
        $request->validate([
            'customer_id' => 'required',
            'shipping_address' => 'required',
            'shipping_pin' => 'required',
            'order_status' => 'required',
        ]);

        $order = Order::create([
            'customer_id' => $request->customer_id,
            'shipping_address' => $request->shipping_address,
            'shipping_pin' => $request->shipping_pin,
            'order_status' => $request->order_status,
            'delivery_date' => $request->delivery_date,
        ]);
        if ($order) {
            return response([
                'result' => $order,
                'status' => 200
            ], 200);
        } else {
            return response([
                'result' => 'Failed',
                'status' => 'Failed'
            ], 200);
        }
    }
    public function orderPlace(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required|integer|digits:10',
            'wpnumber' => 'nullable|integer|digits:10',
            'address' => 'required',
            'pin' => 'required|integer|digits:6',
            'salesman_id' => 'required|integer|exists:users,id',
            'items' => 'required|array',
            'items.*.product_id' => 'required',
            'items.*.quantity' => 'required',
            'delivery_date' => 'nullable|date',
            'delivery_charges' => 'required',
            'notes' => 'nullable|string',
        ]);

        $salesman = User::find($request->salesman_id);
        if ($salesman->status !== 'active') {
            return response()->json([
                'status' => 0,
                'message' => 'You have no order access and permission'
            ], 403);
        }


        if ($validator->fails()) {
            return response()->json([
                'status' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Auto-create user account if this is a first-time customer
            $generatedPassword = null;
            $isNewUser = false;
            $user = User::where('phone', $request->phone)->first();
            if (!$user) {
                $generatedPassword = rand(100000, 999999);
                $user = User::create([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'wpnumber' => $request->wpnumber ?? $request->phone,
                    'password' => Hash::make($generatedPassword),
                    'role' => 'customer',
                    'status' => 'active',
                ]);
                $isNewUser = true;
            }

            // Check if the phone number already exists in customers table
            $existingCustomer = Customer::where('phone', $request->phone)->first();
            if ($existingCustomer) {
                $existingCustomer->name = $request->name;
                $existingCustomer->address = $request->address;
                $existingCustomer->state = $request->state;
                $existingCustomer->pin = $request->pin;
                $existingCustomer->wpnumber = $request->wpnumber;
                $existingCustomer->save();
                $customerId = $existingCustomer->id;
            } else {
                $newCustomer = Customer::create([
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'wpnumber' => $request->wpnumber,
                    'address' => $request->address,
                    'pin' => $request->pin,
                    'salesman_id' => $request->salesman_id,
                ]);
                $customerId = $newCustomer->id;
            }
            $order = Order::create([
                'customer_id' => $customerId,
                'salesman_id' => $request->salesman_id,
                'order_status' => 'pending',
                'delivery_charges' => $request->delivery_charges,
                'delivery_date' => Carbon::parse($request->delivery_date)->format('Y-m-d'),
                'notes' => $request->notes,
                'source' => 'Subdealer',
            ]);
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                if (!$product)
                    continue;

                // Big e-commerce site standard: Check for active special offer discount
                $effectivePrice = (float) ($product->online_price ?: $product->sale_price ?: $product->price ?: 0);
                $so = $product->specialOffer;
                if ($so && $so->isCurrentlyActive()) {
                    if ($so->offer_type === 'flat' && $so->flat_discount) {
                        $effectivePrice = max(0, $effectivePrice - (float) $so->flat_discount);
                    } elseif ($so->offer_type === 'percentage' && $so->percentage_discount) {
                        $effectivePrice = max(0, $effectivePrice - ($effectivePrice * ((float) $so->percentage_discount / 100)));
                    }
                }

                $gstAmount = Category::find($product->category_id)->gst ?? 0;

                $orderItems = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $effectivePrice,
                    'gst' => $gstAmount,
                ]);

                $deliveryCharge = 0;
                if ($request->delivery_charges == '1') {
                    $deliveryCharge = ($product->delivery_charges_amount ?: 0) * $item['quantity'];
                }

                $orderItems->delivery_charges = $deliveryCharge;
                $orderItems->total = ($item['quantity'] * $effectivePrice) + $deliveryCharge;
                $orderItems->save();

                // If offer includes a free gift product, record in order_free_gifts table & add ₹0 item
                if ($so && $so->isCurrentlyActive() && $so->offer_type === 'free_product' && $so->free_product_id) {
                    OrderFreeGift::create([
                        'order_id' => $order->id,
                        'order_item_id' => $orderItems->id,
                        'main_product_id' => $item['product_id'],
                        'free_product_id' => $so->free_product_id,
                        'quantity' => $item['quantity'],
                        'status' => 'pending',
                    ]);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $so->free_product_id,
                        'quantity' => $item['quantity'],
                        'price' => 0,
                        'gst' => 0,
                        'delivery_charges' => 0,
                        'total' => 0,
                    ]);
                }
            }
            $salesman = User::findOrFail($request->salesman_id);
            if ($salesman && $salesman->role == 'salesman') {
                $order->direct_salesman = 1;
                $order->save();
            }
            DB::commit();
            $response = [
                'status' => 1,
                'message' => 'Order placed successfully',
                'order_id' => $order->id,
                'is_new_user' => $isNewUser,
            ];

            if ($isNewUser && $generatedPassword) {
                $response['credentials'] = [
                    'phone' => $request->phone,
                    'password' => $generatedPassword
                ];
            }

            $message = "*Hi {$request->name}!* 👋\n\n"
                . "Your order has been placed successfully.\n"
                . "*Order ID:* #{$order->id}\n\n";

            if ($isNewUser && $generatedPassword) {
                $message .= "🔑 *Your Account Login Credentials:*\n"
                    . "*Phone:* {$request->phone}\n"
                    . "*Password:* {$generatedPassword}\n"
                    . "_Use these credentials to log in for your future orders!_\n\n";
            }

            $message .= "We will contact you soon.\n"
                . "_Thank you for shopping with us!_\n\n"
                . "*Team IconComputer* 💻";

            $rawNumber = $request->wpnumber ?? $request->phone;
            $number = $this->formatWhatsAppNumber($rawNumber);

            try {
                Http::connectTimeout(3)->timeout(6)->get('https://nextsms.co.in/api/whatsapp/send', [
                    'receiver' => $number,
                    'msgtext' => $message,
                    'token' => config('services.whatsapp.token'),
                ]);
            } catch (\Throwable $e) {
                \Log::warning('Direct WhatsApp message send failed in orderPlace, fallback to queue: ' . $e->getMessage());
                SendWhatsappMessage::dispatch($number, $message);
            }

            return response()->json($response, 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => 0,
                'message' => 'Failed to place order',
                'error' => $th->getMessage()
            ], 500);
        }

    }
    public function directOrderPlace(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer|exists:users,id',
            'product_id' => 'required|integer', // new_password_confirmation
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }
        try {
            $product = Product::find($request->product_id);
            $order = Order::create([
                'salesman_id' => $request->id,
                'order_status' => 'pending',
                'direct_salesman' => 1,
                'source' => 'Salesman',
            ]);
            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $request->product_id,
                'quantity' => 1,
                'price' => $product->price,
                'total' => $product->price,
            ]);
            return response()->json([
                'status' => 1,
                'message' => 'Order item added successfully',
                'data' => $orderItem,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 0,
                'message' => 'Failed to add order item',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    public function orderDetails(Request $request)
    {
        try {
            $request->validate([
                'order_id' => 'required',
                'product_id' => 'required',
                'quantity' => 'required',
                'price' => 'required',
                'total' => 'required',
            ]);

            $order = OrderItem::create([
                'order_id' => $request->order_id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'price' => $request->price,
                'total' => $request->total,
            ]);

            return response([
                'result' => $order,
                'status' => 1
            ], 200);
        } catch (\Throwable $th) {
            return response([
                'result' => 'Failed',
                'status' => 0,
                'error' => $th->getMessage()
            ], 500);
        }


    }

    // public function salesmanpayment()
    // {
    //     $salesPayments = PaymentMaster::all();

    //     return response()->json($salesPayments, 200);
    // }
    public function customerget()
    {
        $salesPayments = Customer::all();

        return response()->json($salesPayments, 200);
    }





    public function uploadImage($id, Request $request)
    {
        $thumbnailPath = null;
        $imagePaths = [];
        $details = ProductDetails::find($id);
        // Handle thumbnail image upload
        if ($request->hasFile('thumbnail_image')) {
            $localPath = $request->file('thumbnail_image')->store('icon_computer/product_images/thumbnail', 'public');
            // dd($filePath);
            // Get public URL
            $fileUrl = env('APP_URL') . Storage::url($localPath);
            $thumbnailPath = $fileUrl;
        }
        // Handle multiple image uploads
        if ($request->hasFile('images')) {
            // dd($request->all());
            foreach ($request->file('images') as $image) {
                $localPath = $image->store('icon_computer/product_images/images', 'public');
                // dd($filePath);
                // Get public URL
                $image_url = env('APP_URL') . Storage::url($localPath);
                $imagePaths[] = $image_url;
            }
        }
        $details->update([
            'image' => json_encode($imagePaths),
            'thumbnail_image' => $thumbnailPath,
        ]);
        return response([
            'result' => 'Image uploaded successfully',
            'status' => 200
        ], 200);
    }

    public function downloadImage($id)
    {
        // Find the product details
        $details = ProductDetails::find($id);

        if (!$details) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Decode JSON image data
        $images = json_decode($details->image, true) ?? [];
        $thumbnail = $details->thumbnail_image;
        // dd($thumbnail, $images);
        $savedImages = [];
        $savedThumbnail = null;

        // ✅ Save the thumbnail image
        if ($thumbnail) {
            $thumbnailPath = $this->saveImageFromUrl($thumbnail, 'icon_computer/product_images/thumbnail/');
            if ($thumbnailPath) {
                $savedThumbnail = asset('storage/' . $thumbnailPath);
            }
        }

        // ✅ Save additional images (if any)
        if (count($images) > 0) {
            foreach ($images as $url) {
                $imagePath = $this->saveImageFromUrl($url, 'icon_computer/product_images/');
                if ($imagePath) {
                    $savedImages[] = asset('storage/' . $imagePath);
                }
            }
        }
        // foreach ($images as $url) {
        //     $imagePath = $this->saveImageFromUrl($url, 'icon_computer/product_images/');
        //     if ($imagePath) {
        //         $savedImages[] = asset('storage/' . $imagePath);
        //     }
        // }

        $details->update([
            'image' => json_encode($savedImages),
            'thumbnail_image' => $savedThumbnail,
        ]);
        return response()->json([
            'message' => 'Images downloaded and saved successfully!',
            'saved_images' => $savedImages
        ]);
    }

    /**
     * ✅ Helper function to download and save an image from a URL
     */
    private function saveImageFromUrl($url, $directory)
    {
        try {
            // Get image content
            $imageContents = file_get_contents($url);

            if (!$imageContents) {
                return null;
            }

            // Generate a unique filename
            $imageName = uniqid() . '.png'; // Change extension if needed

            // Define the storage path
            $localPath = $directory . $imageName;

            // Save file to storage
            Storage::disk('public')->put($localPath, $imageContents);
            $relativePath = str_replace(env('APP_URL') . '/storage/', '', $url);
            // Delete the file
            // dd($relativePath);
            if (Storage::disk('public')->exists($relativePath)) {
                //   dd($relativePath);
                Storage::disk('public')->delete($relativePath);
            }

            return $localPath;
        } catch (Exception $e) {
            return null;
        }
    }

    function convertAllJpgToPng()
    {
        $folderPath = public_path('storage/icon_computer/product_images/thumbnail');

        // Ensure folder exists
        if (!is_dir($folderPath)) {
            return 'Folder not found!';
        }

        // Scan for PNG files
        $files = glob($folderPath . '/*.png');

        if (empty($files)) {
            return 'No PNG files found!';
        }

        $convertedCount = 0;

        foreach ($files as $filePath) {
            // Free up memory every 50 images
            if ($convertedCount % 50 == 0) {
                gc_collect_cycles();
            }

            // Load the PNG image
            $pngImage = imagecreatefrompng($filePath);
            if (!$pngImage) {
                continue; // Skip if loading fails
            }

            // Generate JPG filename
            $newImagePath = preg_replace('/\.png$/i', '.jpg', $filePath);

            // Convert and save as JPG
            imagejpeg($pngImage, $newImagePath, 90); // 90 = high quality

            // Free up memory
            imagedestroy($pngImage);

            // Delete the original PNG file
            unlink($filePath);

            $convertedCount++;
        }

        return "Converted $convertedCount images to JPG!";
    }

    public function updateImageUrls()
    {
        // Fetch all records where URLs start with 'http://'
        $products = ProductDetails::where('thumbnail_image', 'LIKE', 'http://%')
            ->orWhere('image', 'LIKE', '%http://%')
            ->get();

        $updatedCount = 0;

        foreach ($products as $product) {
            $updated = false;

            // Update `thumbnail_image` if it starts with 'http://'
            if (str_starts_with($product->thumbnail_image, 'http://')) {
                $product->thumbnail_image = str_replace('http://', 'https://', $product->thumbnail_image);
                $updated = true;
            }

            // Decode the `image` field before updating
            $images = json_decode($product->image, true); // Decode JSON to an array

            if (is_array($images)) {
                $newImages = array_map(function ($img) {
                    return str_starts_with($img, 'http://') ? str_replace('http://', 'https://', $img) : $img;
                }, $images);

                // If any image was changed, update the product
                if ($newImages !== $images) {
                    $product->image = json_encode($newImages); // Re-encode to JSON
                    $updated = true;
                }
            }

            // Save changes only if updates were made
            if ($updated) {
                $product->save();
                $updatedCount++;
            }
        }

        return response()->json([
            'message' => "$updatedCount records updated with HTTPS URLs!",
        ]);
    }

    public function customerByMobile($mobile)
    {

        // CUSTOMER TABLE CHECK

        $customer = Customer::where(
            'phone',
            $mobile
        )->first();

        // CUSTOMER FOUND

        if ($customer) {

            return response()->json([

                'status' => 'success',

                'type' => 'customer',

                'customer' => $customer

            ]);
        }

        // REWARD POINT TABLE CHECK

        $rewardPoint = RewardPoint::where(
            'mobile',
            $mobile
        )->first();

        // REWARD CUSTOMER FOUND

        if ($rewardPoint) {

            return response()->json([

                'status' => 'success',

                'type' => 'reward',

                'reward_point' => $rewardPoint

            ]);
        }

        // NOT FOUND

        return response()->json([

            'status' => 'fail',

            'message' => 'Customer not found'

        ], 404);
    }

    public function getMembersByType($type)
    {
        switch ($type) {
            case 'customer':
                $data = Customer::select('id', 'name', 'phone')->orderBy('name')->get();
                break;
            case 'company':
                $data = Company::select('id', 'name', 'phone')->orderBy('name')->get();
                break;
            case 'manager':
                $data = User::where('role', 'manager')->select('id', 'name', 'phone')->orderBy('name')->get();
                break;
            case 'salesman':
                $data = User::where('role', 'salesman')->select('id', 'name', 'phone')->orderBy('name')->get();
                break;
            case 'subdealer':
                $data = User::where('role', 'subdealer')->select('id', 'name', 'phone')->orderBy('name')->get();
                break;
            case 'expenses':
                $data = Expenses::select('id', 'name')->orderBy('name')->get();
                break;
            default:
                $data = collect();
        }
        return response()->json($data);
    }

    public function checkPhone(Request $request, $phone = null)
    {
        $inputPhone = $phone ?? $request->input('phone') ?? $request->query('phone') ?? $request->query('mobile');

        if (!$inputPhone) {
            return response()->json([
                'status' => 0,
                'found' => false,
                'message' => 'Phone number is required.'
            ], 422);
        }

        $cleanPhone = preg_replace('/\D/', '', $inputPhone);
        if (strlen($cleanPhone) > 10) {
            $cleanPhone = substr($cleanPhone, -10);
        }

        $customer = Customer::where('phone', $cleanPhone)
            ->orWhere('phone', $inputPhone)
            ->orWhere('wpnumber', $cleanPhone)
            ->orWhere('wpnumber', $inputPhone)
            ->first();

        if (!$customer) {
            return response()->json([
                'status' => 0,
                'found' => false,
                'message' => 'No customer found.'
            ], 200);
        }

        return response()->json([
            'status' => 1,
            'found' => true,
            'data' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'phone' => $customer->phone,
                'whatsapp' => $customer->wpnumber,
                'district' => $customer->dist,
                'pincode' => $customer->pin,
                'address' => $customer->address,
                'state' => $customer->state ?? '',
            ]
        ], 200);
    }

    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'digits:10'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $phone = $request->phone;
        $otp = (string) rand(1000, 9999);

        Cache::put('otp_' . $phone, $otp, 600);

        $message = "*IconComputer* 💻\nYour verification code is: *{$otp}*\nValid for 10 minutes.";
        $number = '91' . $phone;

        try {
            $response = Http::connectTimeout(3)->timeout(6)->get(
                'https://nextsms.co.in/api/whatsapp/send',
                [
                    'receiver' => $number,
                    'msgtext' => $message,
                    'token' => config('services.whatsapp.token'),
                ]
            );

            $resData = $response->json();
            if (!$response->successful() || (is_array($resData) && isset($resData['status']) && $resData['status'] === 'error')) {
                $errorMsg = $resData['message'] ?? 'WhatsApp Gateway response error';
                \Log::error("WhatsApp OTP send failed for {$number}: {$errorMsg}");
                return response()->json([
                    'status' => 0,
                    'message' => 'WhatsApp Gateway Error: ' . $errorMsg
                ], 500);
            }
        } catch (\Throwable $e) {
            \Log::warning('Direct WhatsApp OTP send failed: ' . $e->getMessage());
            return response()->json([
                'status' => 0,
                'message' => 'Failed to send OTP via WhatsApp: ' . $e->getMessage()
            ], 500);
        }

        return response()->json([
            'status' => 1,
            'message' => 'OTP sent to your WhatsApp number successfully.'
        ], 200);
    }

    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => ['required', 'digits:10'],
            'otp' => ['required', 'digits:4'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $phone = $request->phone;
        $otp = $request->otp;

        $cachedOtp = Cache::get('otp_' . $phone);

        if ($cachedOtp && (string) $cachedOtp === (string) $otp) {
            Cache::put('verified_phone_' . $phone, true, 600);
            return response()->json([
                'status' => 1,
                'message' => 'WhatsApp number verified successfully.'
            ], 200);
        }

        return response()->json([
            'status' => 0,
            'message' => 'Invalid or expired OTP. Please try again.'
        ], 422);
    }

    public function guestOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required_without:mobile|nullable|digits:10',
            'mobile' => 'required_without:phone|nullable|digits:10',
            'whatsapp' => 'nullable|digits:10',
            'wpnumber' => 'nullable|digits:10',
            'address' => 'required|string|max:255',
            'district' => 'nullable|string|max:100',
            'dist' => 'nullable|string|max:100',
            'pincode' => 'nullable|digits:6',
            'pin' => 'nullable|digits:6',
            'cart' => 'required_without:items',
            'items' => 'required_without:cart',
            'delivery' => 'nullable',
            'otp' => 'nullable|digits:4',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userPhone = (string) ($request->phone ?? $request->mobile);
            $userWhatsapp = (string) ($request->whatsapp ?? $request->wpnumber ?? $userPhone);
            $userDistrict = (string) ($request->district ?? $request->dist ?? '');
            $userPincode = (string) ($request->pincode ?? $request->pin ?? '');

            $rawCart = $request->cart ?? $request->items;
            $cart = is_string($rawCart) ? json_decode($rawCart, true) : $rawCart;

            if (!is_array($cart) || empty($cart)) {
                return response()->json([
                    'status' => 0,
                    'success' => false,
                    'message' => 'Cart item format is invalid or empty.'
                ], 422);
            }

            // Customer record find or create
            $customer = Customer::where('phone', $userPhone)
                ->orWhere('phone', $userWhatsapp)
                ->orWhere('wpnumber', $userPhone)
                ->orWhere('wpnumber', $userWhatsapp)
                ->first();

            if (!$customer) {
                $isVerified = Cache::get('verified_phone_' . $userPhone, false);
                if (!$isVerified && $request->filled('otp')) {
                    $cachedOtp = Cache::get('otp_' . $userPhone);
                    if ($cachedOtp && (string) $cachedOtp === (string) $request->otp) {
                        $isVerified = true;
                    }
                }

                // If OTP not provided or skipped, auto-verify for mobile API order placement
                $customer = new Customer();
                $customer->name = $request->name;
                $customer->phone = $userPhone;
                $customer->wpnumber = $userWhatsapp;
                $customer->address = $request->address;
                $customer->dist = $userDistrict;
                $customer->pin = $userPincode;
                $customer->save();
            } else {
                $customer->name = $request->name;
                $customer->wpnumber = $userWhatsapp;
                $customer->address = $request->address;
                $customer->dist = $userDistrict;
                $customer->pin = $userPincode;
                $customer->save();
            }

            // Create or get user account for first-time customer
            $user = User::where('phone', $userPhone)
                ->orWhere('phone', $userWhatsapp)
                ->orWhere('wpnumber', $userPhone)
                ->orWhere('wpnumber', $userWhatsapp)
                ->first();

            $isNewUser = false;
            $generatedPassword = null;

            if (!$user) {
                $generatedPassword = (string) rand(100000, 999999);
                $user = User::create([
                    'name' => $request->name,
                    'phone' => $userPhone,
                    'wpnumber' => $userWhatsapp,
                    'password' => Hash::make($generatedPassword),
                    'role' => 'user',
                    'status' => 'active',
                ]);
                $isNewUser = true;
            }

            $isDelivery = in_array(strtolower((string)$request->delivery), ['yes', '1', 'true']);

            $order = new Order();
            $order->customer_id = $customer->id;
            $order->order_status = 'pending';
            if ($isDelivery) {
                $order->delivery_charges = '1';
            }
            $order->source = 'Mobile API';
            $order->save();

            foreach ($cart as $item) {
                $productId = $item['id'] ?? $item['product_id'] ?? null;
                $quantity = $item['qty'] ?? $item['quantity'] ?? 1;

                if ($productId) {
                    $product = Product::find($productId);
                    if ($product) {
                        $effectivePrice = (float) ($product->online_price ?: $product->price ?: 0);

                        $orderItem = new OrderItem();
                        $orderItem->order_id = $order->id;
                        $orderItem->product_id = $product->id;
                        $orderItem->quantity = $quantity;
                        $orderItem->price = $effectivePrice;
                        $orderItem->delivery_charges = $isDelivery ? (($product->delivery_charges_amount ?: 0) * $quantity) : 0;
                        $orderItem->total = ($effectivePrice * $quantity) + $orderItem->delivery_charges;
                        $orderItem->gst = $product->category->gst ?? 0;
                        $orderItem->save();
                    }
                }
            }

            Cache::forget('verified_phone_' . $userPhone);

            // Send WhatsApp Notification with Order Details & Account Credentials
            $message = "*Hi {$request->name}!* 👋\n\n"
                . "Your order has been placed successfully via IconComputer App.\n"
                . "*Order ID:* #{$order->id}\n\n";

            if ($isNewUser && $generatedPassword) {
                $message .= "🔑 *Your Account Credentials:*\n"
                    . "*User ID / Phone:* {$userPhone}\n"
                    . "*Password:* {$generatedPassword}\n"
                    . "_Use these details to log in to our Mobile App or Website!_\n\n";
            }

            $message .= "We will contact you soon.\n"
                . "এই অর্ডারটি ৩ দিনের (৭২ ঘন্টা) জন্য গ্রহণযোগ্য হবে।\n"
                . "_Thank you for shopping with us!_\n\n"
                . "*Team IconComputer* 💻";

            $cleanWhatsapp = preg_replace('/\D/', '', $userWhatsapp);
            if (strlen($cleanWhatsapp) > 10) {
                $cleanWhatsapp = substr($cleanWhatsapp, -10);
            }
            $number = '91' . $cleanWhatsapp;

            try {
                Http::connectTimeout(3)->timeout(6)->get(
                    'https://nextsms.co.in/api/whatsapp/send',
                    [
                        'receiver' => $number,
                        'msgtext' => $message,
                        'token' => config('services.whatsapp.token'),
                    ]
                );
            } catch (\Throwable $notificationException) {
                \Log::warning('Direct WhatsApp Order message failed, fallback to queue: ' . $notificationException->getMessage());
                SendWhatsappMessage::dispatch($number, $message);
            }

            $token = $user->createToken("API Token")->plainTextToken;

            return response()->json([
                'status' => 1,
                'success' => true,
                'message' => 'Order placed successfully.',
                'order_id' => $order->id,
                'is_new_user' => $isNewUser,
                'user_id' => $user->id,
                'user_phone' => $user->phone,
                'credentials' => $isNewUser ? [
                    'user_id' => $user->phone,
                    'password' => $generatedPassword
                ] : null,
                'token' => $token,
                'token_type' => 'bearer',
                'result' => $user,
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                'status' => 0,
                'success' => false,
                'message' => 'Failed to place order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper to resolve User, Customer and Phone number from route parameter (User ID or Phone), request inputs, or Sanctum Auth user.
     */
    private function resolveUserAndPhone(Request $request, $idOrPhone = null)
    {
        $param = $idOrPhone
            ?? $request->query('user_id')
            ?? $request->query('id')
            ?? $request->query('phone')
            ?? $request->query('mobile')
            ?? $request->input('user_id')
            ?? $request->input('id')
            ?? $request->input('phone')
            ?? $request->input('mobile');

        $user = null;
        $customer = null;
        $userPhone = null;

        if ($param) {
            // 1. Check if $param is numeric ID and User / Customer exists
            if (is_numeric($param) && strlen((string)$param) <= 8) {
                $user = User::find($param);
                if ($user) {
                    $userPhone = $user->phone ?? $user->wpnumber;
                }
                $customer = Customer::find($param);
                if (!$userPhone && $customer) {
                    $userPhone = $customer->phone ?? $customer->wpnumber;
                }
            }

            // 2. If phone/user not resolved yet, search User / Customer by phone number string
            if (!$userPhone) {
                $clean = preg_replace('/\D/', '', $param);
                if (strlen($clean) > 10) {
                    $clean = substr($clean, -10);
                }

                $user = User::where('phone', $param)
                    ->orWhere('phone', $clean)
                    ->orWhere('wpnumber', $param)
                    ->orWhere('wpnumber', $clean)
                    ->first();

                if ($user) {
                    $userPhone = $user->phone ?? $user->wpnumber;
                } else {
                    $userPhone = $param; // fallback to raw string as phone
                }
            }
        }

        // 3. Fallback to Authenticated Sanctum User
        if (!$userPhone) {
            $authUser = $request->user() ?? Auth::guard('sanctum')->user() ?? Auth::user();
            if ($authUser) {
                $user = $authUser;
                $userPhone = $user->phone ?? $user->wpnumber;
            }
        }

        // Resolve Customer if not resolved yet
        if ($userPhone && !$customer) {
            $cleanPhone = preg_replace('/\D/', '', $userPhone);
            if (strlen($cleanPhone) > 10) {
                $cleanPhone = substr($cleanPhone, -10);
            }
            $customer = Customer::where('phone', $userPhone)
                ->orWhere('phone', $cleanPhone)
                ->orWhere('wpnumber', $userPhone)
                ->orWhere('wpnumber', $cleanPhone)
                ->first();
        }

        return [
            'user' => $user,
            'customer' => $customer,
            'phone' => $userPhone
        ];
    }

    /**
     * Get Complete User Dashboard Data for Mobile App (User Info, Reward Points, Orders Summary)
     */
    public function getUserDashboard(Request $request, $idOrPhone = null)
    {
        $resolved = $this->resolveUserAndPhone($request, $idOrPhone);
        $user = $resolved['user'];
        $customer = $resolved['customer'];
        $userPhone = $resolved['phone'];

        if (!$userPhone && !$user && !$customer) {
            return response()->json([
                'status' => 0,
                'success' => false,
                'message' => 'User ID, phone number or authenticated user session is required.'
            ], 422);
        }

        $cleanPhone = $userPhone ? preg_replace('/\D/', '', $userPhone) : '';
        if (strlen($cleanPhone) > 10) {
            $cleanPhone = substr($cleanPhone, -10);
        }

        // 1. Fetch User / Customer Details
        $userData = [
            'id' => $user ? $user->id : ($customer ? $customer->id : null),
            'name' => $user ? $user->name : ($customer ? $customer->name : 'Customer'),
            'phone' => $userPhone ?? ($user->phone ?? $customer->phone ?? null),
            'whatsapp' => $customer->wpnumber ?? $user->wpnumber ?? $userPhone,
            'role' => $user->role ?? 'customer',
            'address' => $customer->address ?? null,
            'district' => $customer->dist ?? null,
            'pincode' => $customer->pin ?? $customer->pincode ?? null,
        ];

        // 2. Fetch Reward Point & Recent Transactions
        $reward = null;
        $recentTransactions = collect();

        if ($userPhone || $cleanPhone) {
            $reward = RewardPoint::where('mobile', $cleanPhone)
                ->orWhere('mobile', $userPhone)
                ->first();

            $recentTransactions = RewardPointTransaction::where('mobile', $cleanPhone)
                ->orWhere('mobile', $userPhone)
                ->latest()
                ->take(5)
                ->get();
        }

        $rewardData = [
            'balance' => $reward ? (float)$reward->balance : 0,
            'total_earned' => $reward ? (float)$reward->total_earned : 0,
            'total_used' => $reward ? (float)$reward->total_used : 0,
            'recent_transactions' => $recentTransactions,
        ];

        // 3. Fetch Orders & Summary
        $ordersQuery = Order::with([
            'customer',
            'orderItems.product.category',
            'orderItems.product.brand',
            'orderItems.product.details',
            'orderItems.product.specialOffer',
            'freeGifts.freeProduct.brand'
        ])
            ->where(function ($query) use ($customer, $userPhone, $cleanPhone) {
                if ($customer) {
                    $query->where('customer_id', $customer->id);
                }
                if ($userPhone) {
                    $query->orWhereHas('customer', function ($q) use ($userPhone, $cleanPhone) {
                        $q->where('phone', $userPhone)
                            ->orWhere('phone', $cleanPhone)
                            ->orWhere('wpnumber', $userPhone)
                            ->orWhere('wpnumber', $cleanPhone);
                    });
                }
            });

        $totalOrders = (clone $ordersQuery)->count();
        $pendingOrders = (clone $ordersQuery)->whereIn('order_status', ['pending', 'processing'])->count();
        $completedOrders = (clone $ordersQuery)->whereIn('order_status', ['delivered', 'completed'])->count();
        $canceledOrders = (clone $ordersQuery)->whereIn('order_status', ['canceled', 'cancelled'])->count();

        $recentOrders = (clone $ordersQuery)->latest()->take(5)->get();

        $ordersSummary = [
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'completed_orders' => $completedOrders,
            'canceled_orders' => $canceledOrders,
            'recent_orders' => $recentOrders,
        ];

        return response()->json([
            'status' => 1,
            'success' => true,
            'message' => 'User dashboard data retrieved successfully.',
            'data' => [
                'user' => $userData,
                'reward_points' => $rewardData,
                'orders_summary' => $ordersSummary,
            ]
        ], 200);
    }

    /**
     * Get Customer Reward Points & Transaction History (By User ID or Phone)
     */
    public function getCustomerRewardPoints(Request $request, $idOrPhone = null)
    {
        $resolved = $this->resolveUserAndPhone($request, $idOrPhone);
        $userPhone = $resolved['phone'];

        if (!$userPhone) {
            return response()->json([
                'status' => 0,
                'success' => false,
                'message' => 'User ID, phone number or authenticated user session is required.'
            ], 422);
        }

        $cleanPhone = preg_replace('/\D/', '', $userPhone);
        if (strlen($cleanPhone) > 10) {
            $cleanPhone = substr($cleanPhone, -10);
        }

        $reward = RewardPoint::where('mobile', $cleanPhone)
            ->orWhere('mobile', $userPhone)
            ->first();

        $transactions = RewardPointTransaction::where('mobile', $cleanPhone)
            ->orWhere('mobile', $userPhone)
            ->latest()
            ->get();

        return response()->json([
            'status' => 1,
            'success' => true,
            'message' => 'Reward points data retrieved successfully.',
            'data' => [
                'phone' => $userPhone,
                'reward' => $reward,
                'balance' => $reward ? (float)$reward->balance : 0,
                'total_earned' => $reward ? (float)$reward->total_earned : 0,
                'total_used' => $reward ? (float)$reward->total_used : 0,
                'transactions' => $transactions,
            ]
        ], 200);
    }

    /**
     * Get Customer Order History (By User ID or Phone)
     */
    public function getCustomerOrders(Request $request, $idOrPhone = null)
    {
        $resolved = $this->resolveUserAndPhone($request, $idOrPhone);
        $customer = $resolved['customer'];
        $userPhone = $resolved['phone'];

        if (!$userPhone && !$customer) {
            return response()->json([
                'status' => 0,
                'success' => false,
                'message' => 'User ID, phone number or authenticated user session is required.'
            ], 422);
        }

        $cleanPhone = $userPhone ? preg_replace('/\D/', '', $userPhone) : '';
        if (strlen($cleanPhone) > 10) {
            $cleanPhone = substr($cleanPhone, -10);
        }

        $query = Order::with([
            'customer',
            'orderItems.product.category',
            'orderItems.product.brand',
            'orderItems.product.details',
            'orderItems.product.specialOffer',
            'freeGifts.freeProduct.brand'
        ])
            ->where(function ($q) use ($customer, $userPhone, $cleanPhone) {
                if ($customer) {
                    $q->where('customer_id', $customer->id);
                }
                if ($userPhone) {
                    $q->orWhereHas('customer', function ($subQ) use ($userPhone, $cleanPhone) {
                        $subQ->where('phone', $userPhone)
                            ->orWhere('phone', $cleanPhone)
                            ->orWhere('wpnumber', $userPhone)
                            ->orWhere('wpnumber', $cleanPhone);
                    });
                }
            });

        if ($request->filled('status')) {
            $status = strtolower($request->input('status'));
            if ($status === 'pending') {
                $query->whereIn('order_status', ['pending', 'processing']);
            } elseif ($status === 'delivered' || $status === 'completed') {
                $query->whereIn('order_status', ['delivered', 'completed']);
            } elseif ($status === 'canceled' || $status === 'cancelled') {
                $query->whereIn('order_status', ['canceled', 'cancelled']);
            }
        }

        $orders = $query->latest()->get();

        return response()->json([
            'status' => 1,
            'success' => true,
            'message' => 'Orders retrieved successfully.',
            'phone' => $userPhone,
            'total' => count($orders),
            'data' => $orders
        ], 200);
    }
}
