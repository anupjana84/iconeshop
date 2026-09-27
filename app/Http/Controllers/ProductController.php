<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductDetails;
use App\Models\PurchaseItems;
use App\Models\SpecialOffer;
use DB;
use Illuminate\Http\Request;
use Storage;

class ProductController extends Controller
{
    public function fixLongProductCodes()
    {
        // Get all products where code length > 10
        $products = Product::whereRaw('LENGTH(code) > 10')->get();

        $updatedCount = 0;

        foreach ($products as $product) {

            // Generate NEW unique code
            $newCode = $this->generateUniqueProductCode(10);

            // Update
            $product->code = $newCode;
            $product->save();

            // Delay to avoid server load (0.1 seconds)
            usleep(100000); // 100 ms

            $updatedCount++;
        }

        return response()->json([
            'status' => 1,
            'message' => 'Product codes updated successfully',
            'total_updated' => $updatedCount
        ]);
    }








    public function productList(Request $request)
{
    $page_title = 'Product List';

    // Collect all filter inputs
    $search = $request->input('search');
    $category_id = $request->input('category_id');
    $brand_id = $request->input('brand_id');
    $model = $request->input('model');
    $search_type = $request->input('search_type');
    $purchase_company = null;

    // Base query
    $query = Product::with(['category', 'brand', 'details', 'purchase', 'specialOffer.freeProduct', 'freeProduct'])->orderBy('stock', 'desc')->latest();

    if (!empty($search)) {
        $query->where('code', 'LIKE', "%{$search}%");

        $productId = Product::where('code', $search)->first();
        if (empty($productId)) {
            return redirect()->back()->with('fail', 'Product not found with the provided code.');
        }

        $purchase_company = PurchaseItems::where('product_id', $productId->id)->get();

    } elseif (!empty($category_id) || !empty($brand_id) || !empty($model)) {

        if (!empty($category_id)) {
            $query->where('category_id', $category_id);
        }

        if (!empty($brand_id)) {
            $query->where('brand_id', $brand_id);
        }

        if (!empty($model)) {
            $query->where('model', $model);
        }
    }

    // 🔥 ONLY ADDITION (IMPORTANT)
    $products = $query
    ->paginate(10)
    ->withQueryString();

    $totalStock = (clone $query)->sum('stock');

    $categories = Category::select('id', 'name')->get();
    $brands = Brand::select('id', 'name')->get();
    $allProducts = Product::with(['brand'])->where('stock', '>', 0)->latest()->get();

    // Compact all values (unchanged)
    $data = compact(
        'page_title',
        'products',
        'search',
        'categories',
        'brands',
        'category_id',
        'brand_id',
        'model',
        'search_type',
        'purchase_company',
        'totalStock',
        'allProducts'
    );

    return view('admin.product.productList')->with($data);
}



public function productCode(Request $request)
{
    $page_title = 'Product Code';

    // Collect all filter inputs
    $search = $request->input('search');
    $category_id = $request->input('category_id');
    $brand_id = $request->input('brand_id');
    $model = $request->input('model');
    $search_type = $request->input('search_type');
    $purchase_company = null;

    // Base query
    $query = Product::with(['category', 'brand', 'details', 'purchase'])->latest();

    if (!empty($search)) {
        $query->where('code', 'LIKE', "%{$search}%");

        $productId = Product::where('code', $search)->first();
        if (empty($productId)) {
            return redirect()->back()->with('fail', 'Product not found with the provided code.');
        }

        $purchase_company = PurchaseItems::where('product_id', $productId->id)->get();

    } elseif (!empty($category_id) || !empty($brand_id) || !empty($model)) {

        if (!empty($category_id)) {
            $query->where('category_id', $category_id);
        }

        if (!empty($brand_id)) {
            $query->where('brand_id', $brand_id);
        }

        if (!empty($model)) {
            $query->where('model', $model);
        }
    }

    // 🔥 ONLY ADDITION
    $products = $query->paginate(10)->withQueryString();

    $categories = Category::select('id', 'name')->get();
    $brands = Brand::select('id', 'name')->get();

    // Compact all values (unchanged)
    $data = compact(
        'page_title',
        'products',
        'search',
        'categories',
        'brands',
        'category_id',
        'brand_id',
        'model',
        // 'search_type',
        'purchase_company'
    );

    return view('admin.product.productCode')->with($data);
}


    public function productCreate()
    {
        $page_title = 'Product Create';
        $brand = Brand::orderBy('name', 'asc')->get();
        $url = route('products.store');
        $category = Category::orderBy('name', 'asc')->get();
        $allProducts = Product::with(['brand', 'category'])->orderBy('model', 'asc')->get();
        $data = compact('page_title', 'brand', 'category', 'allProducts', 'url');
        return view('admin.product.addProduct')->with($data);
    }

    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'brand' => 'required|integer|min:0',
            'category' => 'required|integer|min:0',
            'model' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'online_price' => 'required|numeric|min:0',
            'dealer_point' => 'required|numeric|min:0|max:100',
            'salesmen_point' => 'required|numeric|min:0|max:100',
            'discount' => 'nullable|numeric|min:0|max:100',
            'delivery_charges' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'mrp' => 'required|numeric|min:1',
            'special_offer' => 'required|string|max:11',
            'free_gift' => 'nullable|string|max:255',
            'free_product_id' => 'nullable|integer|exists:products,id',
            'offer_discount_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        $is_exist = Product::where('brand_id', $request->brand)
            ->where('category_id', $request->category)
            ->where('model', $request->model)
            ->first();

        try {
            if ($is_exist) {
                $message = 'This product already exsit';
                return redirect()->back()->withInput()->with('fail', $message);
            } else {
                DB::beginTransaction();
                $details = ProductDetails::create([
                    'description' => $request->description,
                    'status' => 0,
                    'mrp' => $request->mrp,
                ]);
                $gst = Category::where('id', $request->category)->value('gst');
                $online_price = $request->purchase_price + ($request->purchase_price * ($gst / 100))
                    + ($request->purchase_price * ($request->online_price / 100));

                $product = Product::create([
                    'brand_id' => $request->brand,
                    'category_id' => $request->category,
                    'model' => $request->model,
                    'discount' => $request->discount,
                    'stock' => $request->quantity,
                    'code' => $this->generateUniqueProductCode(),
                    'details_id' => $details->id,
                    // 'quantity' => $request->quantity,
                    'special_offer'=>$request->special_offer,
                    'free_gift' => $request->free_gift,
                    'free_product_id' => $request->free_product_id,
                    'offer_discount_percent' => $request->offer_discount_percent,

                    'purchase_price' => $request->purchase_price,
                    'purchase_withgst' => round($request->purchase_price + ($request->purchase_price * ($gst / 100))),

                    'sale_rate' => $request->sale_price, // %
                    'sale_price' => round($request->purchase_price
                        + ($request->purchase_price * ($request->sale_price / 100))
                        + ($request->purchase_price * ($gst / 100))), //rs
                    'price' => round($request->purchase_price
                        + ($request->purchase_price * ($gst / 100))
                        + ($request->purchase_price * ($request->online_price / 100))
                        + ($request->purchase_price * ($request->dealer_point / 100))), //rs internal use

                    'online_rate' => $request->online_price, // %
                    'online_price' => round($request->purchase_price
                        + ($request->purchase_price * ($gst / 100))
                        + ($request->purchase_price * ($request->online_price / 100))), //rs

                    'dealer_point' => $request->dealer_point, // %
                    'point' => round($request->purchase_price * ($request->dealer_point / 100)), //rs

                    'salesmen_point' => $request->salesmen_point, // %
                    'salesmen_point_price' => round($request->purchase_price * ($request->salesmen_point / 100)), //rs

                    'delivery_charges' => $request->delivery_charges, // %
                    'delivery_charges_amount' => round($request->purchase_price * ($request->delivery_charges / 100)), //rs
                ]);
                $message = 'Product created successfully!';
                DB::commit();
            }

            return redirect()->route('product.list')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('fail', 'Failed to create product: ' . $e->getMessage());
        }
    }

    public function emptyStockProducts()
    {
        $page_title = 'Empty Stock Products';
        $empty_stocks = true;
        $products = Product::with(['category', 'brand', 'details'])
            ->where('stock', '<=', 0)
            ->orWhere('stock', '=', null)
            ->latest()
            ->paginate(10);
        $data = compact('page_title', 'products','empty_stocks');
        return view('admin.product.productList')->with($data);
    }

    public function oldStockProducts()
    {
        $page_title = 'Old Stock Products';
        $empty_stocks = true;

        $products = Product::with(['category', 'brand', 'details'])
            ->where('stock', '>', 0) // only products with some stock
            ->where('updated_at', '<=', now()->subMonths(8)) // older than 8 months
            ->latest()
            ->paginate(10);

        $data = compact('page_title', 'products', 'empty_stocks');

        return view('admin.product.productList')->with($data);
    }


    public function productShow($id)
    {
        $product = Product::with('details')->findOrFail($id);
        // Decode image JSON safely
        $images = [];
        if ($product->details && $product->details->image) {
            $images = json_decode($product->details->image, true) ?? [];
        }

        $page_title = 'Product Details';
        $data = compact('page_title', 'product', 'images');
        return view('admin.product.productShow')->with($data);
    }

    public function productEdit($id)
    {
        $page_title = 'Edit Product';
        $url = route('product.update', ['id' => $id]);
        $product = Product::findOrFail($id);
        $brand = Brand::all();
        $category = Category::all();
        $allProducts = Product::where('id', '!=', $id)->with(['brand', 'category'])->orderBy('model', 'asc')->get();
        $data = compact('page_title', 'product', 'brand', 'category', 'allProducts', 'url');
        return view('admin.product.addProduct')->with($data);
    }

    public function updateDisplay(Request $request, $id)
    {
        $request->validate([
            'display' => 'nullable|string|in:top,hot,bottom',
        ]);

        $product = ProductDetails::findOrFail($id);

        $product->display = $request->display;
        $product->save();

        return back()->with('success', 'Product display updated successfully.');
    }


    public function productUpdate(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // Validate the request
        $request->validate([
            'brand' => 'required|integer|min:0',
            'category' => 'required|integer|min:0',
            'model' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'online_price' => 'required|numeric|min:0',
            'dealer_point' => 'required|numeric|min:0|max:100',
            'salesmen_point' => 'required|numeric|min:0|max:100',
            'discount' => 'nullable|numeric|min:0|max:100',
            'delivery_charges' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'mrp' => 'nullable|numeric|min:0',
            'special_offer'=>'required| max:11',
            'free_gift' => 'nullable|string|max:255',
            'free_product_id' => 'nullable|integer|exists:products,id',
            'offer_discount_percent' => 'nullable|numeric|min:0|max:100',
        ]);

        $description = $request->description ?? $request->deacription ?? null;

        try {
            DB::beginTransaction();
            $gst = Category::where('id', $request->category)->value('gst');
            // // Update or create product_details
            $details = null;
            if ($product->details_id) {
                $details = ProductDetails::find($product->details_id);
            }
            if (!$details) {
                $details = new ProductDetails();
            }
            // fill details (add additional fields if needed)
            $details->mrp = $request->mrp;
            $details->description = $description;
            $details->save();
            // Update product fields
            $product->brand_id = $request->brand;
            $product->category_id = $request->category;
            $product->details_id = $details->id;
            $product->model = $request->model;
            $product->discount = $request->discount ?? 0;
            $product->stock = $request->quantity;
            $product->special_offer = $request->special_offer;
            $product->free_gift = $request->free_gift;
            $product->free_product_id = $request->free_product_id;
            $product->offer_discount_percent = $request->offer_discount_percent;



            // Prices & rates
            $product->purchase_price = $request->purchase_price;
            $product->purchase_withgst = round($request->purchase_price + ($request->purchase_price * ($gst / 100)));

            $product->sale_rate = $request->sale_price;          // percent (as in your original)
            $product->sale_price = round($request->purchase_price
                + ($request->purchase_price * ($request->sale_price / 100))
                + ($request->purchase_price * ($gst / 100)));      // calculated in ₹

            $product->price = round($request->purchase_price
                + ($request->purchase_price * ($gst / 100))
                + ($request->purchase_price * ($request->online_price / 100))
                + ($request->purchase_price * ($request->dealer_point / 100)));         // internal price (₹)

            $product->online_rate = $request->online_price;      // %
            $product->online_price = round($request->purchase_price
                + ($request->purchase_price * ($gst / 100))
                + ($request->purchase_price * ($request->online_price / 100)));  // ₹

            $product->dealer_point = $request->dealer_point;     // %
            $product->point = round($request->purchase_price * ($request->dealer_point / 100));      // ₹

            $product->salesmen_point = $request->salesmen_point;          // %
            $product->salesmen_point_price = round($request->purchase_price * ($request->salesmen_point / 100)); // ₹

            $product->delivery_charges = $request->delivery_charges;              // %
            $product->delivery_charges_amount = round($request->purchase_price * ($request->delivery_charges / 100)); // ₹
            $product->save();

            DB::commit();
            return redirect()->route('product.show', ['id' => $product->id])
                ->with('success', 'Product updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('fail', 'Failed to update product.');
        }
    }

    public function productDestroy($id)
    {
        try {
            DB::beginTransaction();
            $product = Product::findOrFail($id);
            $product->delete();

            DB::commit();
             return redirect()->route('product.list')
                ->with('success', 'Product deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('fail', 'Failed to delete product: ' . $e->getMessage());
        }
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

    public function productByCode($code)
    {
        $product = Product::where('code', $code)->with(['category', 'brand', 'details'])->first();
        if ($product) {
            return redirect()->route('product.show', ['id' => $product->id]);
        } else {
            return redirect()->back()->with('fail', 'Product not found with the provided code.');
        }
    }
    public function getDependentData(Request $request)
    {
        $categoryId = $request->category_id;
        $brandId = $request->brand_id;

        // Default empty arrays
        $categories = [];
        $brands = [];
        $models = [];

        // 1️⃣ Nothing selected → show all categories & brands
        if (!$categoryId && !$brandId) {
            $categories = Category::select('id', 'name')->get();
            $brands = Brand::select('id', 'name')->get();
        }

        // 2️⃣ Brand selected → find categories & models linked to that brand
        elseif ($brandId && !$categoryId) {
            $categories = Category::whereIn('id', Product::where('brand_id', $brandId)->pluck('category_id'))
                ->select('id', 'name')->get();
            $models = Product::where('brand_id', $brandId)
                ->pluck('model')->unique()->values();
        }

        // 3️⃣ Category selected → find brands & models linked to that category
        elseif ($categoryId && !$brandId) {
            $brands = Brand::whereIn('id', Product::where('category_id', $categoryId)->pluck('brand_id'))
                ->select('id', 'name')->get();
            $models = Product::where('category_id', $categoryId)
                ->pluck('model')->unique()->values();
        }

        // 4️⃣ Both selected → filter models based on both
        elseif ($categoryId && $brandId) {
            $models = Product::where('category_id', $categoryId)
                ->where('brand_id', $brandId)
                ->pluck('model')->unique()->values();
        }

        return response()->json([
            'categories' => $categories,
            'brands' => $brands,
            'models' => $models,
        ]);
    }

    public function productThambnail(Request $request, $productId)
    {
        $request->validate([
            'thumbnail_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
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
                // $thumbnail = env('APP_URL') . Storage::url($localPath);
                $thumbnail = url(Storage::url($localPath));
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
            return redirect()->back()->with('success', 'Thumbnail image added successfully.');
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
            return redirect()->back()->with('success', 'Thumbnail image updated successfully.');
        }
    }

    public function productImageAdd(Request $request, int $productId)
    {
        $request->validate([
            'images' => 'required|array|max:10', // Limit to 5 images
            'images.*' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'description' => 'nullable|string|max:255',
            'mrp' => 'nullable|numeric|min:0',
        ]);
        $id = Product::where('id', $productId)->value('details_id');
        if (!$id) {
            $details = ProductDetails::create([
                'status' => 1,
            ]);
        } else {
            $details = ProductDetails::find($id);
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

        return redirect()->back()->with('success', 'Product images added successfully.');
    }

    public function productChangeStatus(int $id)
    {
        try {
            $product = Product::findOrFail($id);
            $details = ProductDetails::findOrFail($product->details_id);
            
            // Toggle status
            if ($details->status == 0) {
                $details->status = 1;
            } else {
                $details->status = 0;
            }
            $details->save();

            return response()->json([
                'success' => true,
                'message' => 'Product status changed successfully.',
                'status' => $details->status
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to change status: ' . $e->getMessage()
            ], 500);
        }
    }
    public function productBarcode($code)
    {
        $products = Product::with(['details', 'category', 'brand'])->where('code', $code)
            // ->whereNotNull('details_id')
            // ->where('quantity', '>', 0)
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


public function bulkPriceUpdate(Request $request)
{

    $request->validate([

        'change_type' => 'required',

        'sale_change_percent' => 'nullable|numeric',

        'online_change_percent' => 'nullable|numeric',

    ]);

    try{

        DB::beginTransaction();

        $query = Product::query();

        // CATEGORY FILTER
        if($request->category_id){

            $query->where(
                'category_id',
                $request->category_id
            );
        }

        // BRAND FILTER
        if($request->brand_id){

            $query->where(
                'brand_id',
                $request->brand_id
            );
        }

        $products = $query->get();

        foreach($products as $product){

            $purchasePrice =
                $product->purchase_withgst;

            // CURRENT %
            $saleRate =
                $product->sale_rate ?? 0;

            $onlineRate =
                $product->online_rate ?? 0;

            // CHANGE %
            $saleChange =
                $request->sale_change_percent ?? 0;

            $onlineChange =
                $request->online_change_percent ?? 0;

            // INCREASE
            if($request->change_type == 'increase'){

                $saleRate =
                    $saleRate + $saleChange;

                $onlineRate =
                    $onlineRate + $onlineChange;

            }

            // DECREASE
            else{

                $saleRate =
                    $saleRate - $saleChange;

                $onlineRate =
                    $onlineRate - $onlineChange;

            }

            // PREVENT NEGATIVE
            if($saleRate < 0){

                $saleRate = 0;
            }

            if($onlineRate < 0){

                $onlineRate = 0;
            }

            // NEW SALE PRICE

            $salePrice =
                $purchasePrice +
                (($purchasePrice * $saleRate)/100);

            // NEW ONLINE PRICE

            $onlinePrice =
                $purchasePrice +
                (($purchasePrice * $onlineRate)/100);

            // SAVE

            $product->sale_rate =
                round($saleRate,2);

            $product->online_rate =
                round($onlineRate,2);

            $product->sale_price =
                round($salePrice);

            $product->online_price =
                round($onlinePrice);

            $product->save();
        }

        DB::commit();

        return response()->json([

            'status' => true,
            'message' => 'Price Updated Successfully'

        ]);

    }catch(\Exception $e){

        DB::rollback();

        return response()->json([

            'status' => false,
            'message' => $e->getMessage()

        ]);
    }
 }
    public function updateSpecialOffer(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'special_offer' => 'required|in:yes,no'
            ]);

            $product = Product::find($request->product_id);
            
            if ($product) {
                $product->special_offer = $request->special_offer;
                if ($request->special_offer === 'no') {
                    $product->offer_discount_percent = null;
                    $product->free_product_id = null;
                }
                $product->save();
            }

            if ($request->special_offer === 'no') {
                SpecialOffer::where('product_id', $request->product_id)->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Special offer updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update special offer: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSpecialOffer($productId)
    {
        $offer = SpecialOffer::with('freeProduct.brand')->where('product_id', $productId)->first();
        return response()->json([
            'success' => true,
            'offer' => $offer
        ]);
    }

    public function saveSpecialOffer(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'offer_id' => 'nullable|exists:special_offers,id',
            'offer_type' => 'required|in:flat,percentage,free_product',
            'flat_discount' => 'nullable|numeric|min:0',
            'percentage_discount' => 'nullable|numeric|min:0|max:100',
            'free_product_id' => 'nullable|exists:products,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'required|boolean',
        ]);

        try {
            $product = Product::find($request->product_id);
            if (!$product) {
                return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
            }

            // Check if end_date is in the past
            if (\Carbon\Carbon::parse($request->end_date)->isPast()) {
                return response()->json([
                    'success' => false,
                    'message' => 'অফারের শেষ তারিখ (End Date) বর্তমান সময়ের পরে হতে হবে!'
                ], 422);
            }

            // Check if an existing offer exists for this product
            $existingOffer = SpecialOffer::where('product_id', $request->product_id)->first();

            if ($existingOffer) {
                $isExistingActive = $existingOffer->is_active && \Carbon\Carbon::parse($existingOffer->end_date)->gte(now());
                $isUpdatingSameOffer = $request->filled('offer_id') && (int)$request->offer_id === (int)$existingOffer->id;

                // If an active & non-expired offer already exists and we are NOT updating that exact offer:
                if ($isExistingActive && !$isUpdatingSameOffer) {
                    return response()->json([
                        'success' => false,
                        'message' => 'এই প্রোডাক্টটির জন্য ইতিমধ্যে একটি সক্রিয় স্পেশাল অফার বিদ্যমান রয়েছে! আগের অফারের মোট মেয়াদ শেষ (Expire) হলে অথবা ডিলিট করলে নতুন অফার দেওয়া যাবে।'
                    ], 422);
                }
            }

            $sellingPrice = (float) ($product->online_price ?: $product->sale_price ?: 0);

            if ($request->offer_type === 'flat') {
                if ($request->flat_discount === null || (float)$request->flat_discount <= 0) {
                    return response()->json(['success' => false, 'message' => 'Please enter a valid flat discount amount.'], 422);
                }
                if ((float)$request->flat_discount >= $sellingPrice) {
                    return response()->json([
                        'success' => false,
                        'message' => "Flat discount (₹{$request->flat_discount}) selling price (₹{$sellingPrice}) er সমান বা বেশি হতে পারবে না!"
                    ], 422);
                }
            } elseif ($request->offer_type === 'percentage') {
                if ($request->percentage_discount === null || (float)$request->percentage_discount <= 0) {
                    return response()->json(['success' => false, 'message' => 'Please enter a valid percentage discount.'], 422);
                }
                if ((float)$request->percentage_discount >= 100) {
                    return response()->json([
                        'success' => false,
                        'message' => "Percentage discount 100% বা তার বেশি হতে পারবে না!"
                    ], 422);
                }
            } elseif ($request->offer_type === 'free_product') {
                if (!$request->free_product_id) {
                    return response()->json(['success' => false, 'message' => 'Please choose a valid free gift product.'], 422);
                }
            }

            // Update existing or create single offer record per product
            $offer = SpecialOffer::updateOrCreate(
                ['product_id' => $request->product_id],
                [
                    'offer_type' => $request->offer_type,
                    'flat_discount' => $request->offer_type === 'flat' ? $request->flat_discount : null,
                    'percentage_discount' => $request->offer_type === 'percentage' ? $request->percentage_discount : null,
                    'free_product_id' => $request->offer_type === 'free_product' ? $request->free_product_id : null,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'is_active' => $request->is_active ? 1 : 0,
                ]
            );

            // Also keep legacy special_offer flag in product sync for backwards compatibility
            $isOfferCurrentlyValid = $offer->is_active && \Carbon\Carbon::parse($offer->end_date)->gte(now());
            if ($product) {
                $product->special_offer = $isOfferCurrentlyValid ? 'yes' : 'no';
                if ($request->offer_type === 'percentage') {
                    $product->offer_discount_percent = $request->percentage_discount;
                }
                if ($request->offer_type === 'free_product') {
                    $product->free_product_id = $request->free_product_id;
                }
                $product->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Special offer saved successfully!',
                'offer' => $offer->load('freeProduct.brand')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving special offer: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteSpecialOffer($id)
    {
        try {
            $offer = SpecialOffer::find($id);
            if (!$offer) {
                // If not found by offer ID, try finding by product_id
                $offer = SpecialOffer::where('product_id', $id)->first();
            }

            if ($offer) {
                $product = Product::find($offer->product_id);
                if ($product) {
                    $product->special_offer = 'no';
                    $product->offer_discount_percent = null;
                    $product->free_product_id = null;
                    $product->save();
                }
                $offer->delete();
            } else {
                // Even if no special_offers record exists, reset product flag if product exists
                $product = Product::find($id);
                if ($product) {
                    $product->special_offer = 'no';
                    $product->offer_discount_percent = null;
                    $product->free_product_id = null;
                    $product->save();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Special offer removed successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing offer: ' . $e->getMessage()
            ], 500);
        }
    }
}