@extends('layouts.main')

@push('page_title')
    <title>{{ $page_title }}</title>
@endpush

@section('content_page')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">
                {{ isset($product) ? 'Edit Product' : 'Add New Product' }}
            </h1>
            <p class="mt-2 text-gray-600">
                {{ isset($product) ? 'Update the product details below.' : 'Fill in the details below to add a new product.' }}
            </p>
        </div>

        <!-- Form Card -->
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">
            <form action="{{ $url }}" method="POST" class="p-6 space-y-6">
                @csrf
                @if(isset($product))
                    @method('PUT')
                @endif

                <!-- Basic Information Section -->
                <div class="border-b border-gray-200 pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Basic Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Brand -->
                        <div>
                            <label for="brand" class="block text-sm font-medium text-gray-700 mb-2">
                                Brand <span class="text-red-500">*</span>
                            </label>
                            <select id="brand" name="brand" required @if(isset($product)) disabled @endif
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('brand') border-red-500 @enderror">
                                <option value="">--Select Brand--</option>
                                @foreach ($brand as $item)
                                    <option value="{{ $item->id }}" 
                                        {{ old('brand', $product->brand_id ?? '') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('brand')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        @if(isset($product))
                            <!-- keep value during form submit -->
                            <input type="hidden" name="brand" value="{{ $product->brand_id }}">
                        @endif

                        <!-- Category -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                                Category <span class="text-red-500">*</span>
                            </label>
                            <select id="category" name="category" required @if(isset($product)) disabled @endif
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('category') border-red-500 @enderror">
                                <option value="">--Select Category--</option>
                                @foreach ($category as $item)
                                    <option value="{{ $item->id }}" 
                                        {{ old('category', $product->category_id ?? '') == $item->id ? 'selected' : '' }}>
                                        {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        @if(isset($product))
                            <!-- keep value during form submit -->
                            <input type="hidden" name="category" value="{{ $product->category_id }}">
                        @endif

                        <!-- Model -->
                        <div class="md:col-span-2">
                            <label for="model" class="block text-sm font-medium text-gray-700 mb-2">
                                Model <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="model" name="model" required @if(isset($product)) readonly @endif
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('model') border-red-500 @enderror"
                                placeholder="Enter product model" 
                                value="{{ old('model', $product->model ?? '') }}">
                            @error('model')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label for="deacription" class="block text-sm font-medium text-gray-700 mb-2">
                                Description <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="deacription" name="deacription"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('deacription') border-red-500 @enderror"
                                placeholder="Enter product description" 
                                value="{{ old('deacription', $product->details->description ?? '') }}">
                            @error('deacription')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Pricing Information Section -->
                <div class="border-b border-gray-200 pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Pricing Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                        <!-- Stock -->
                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">
                                Stock <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="quantity" name="quantity" required min="0"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('quantity') border-red-500 @enderror"
                                placeholder="0" value="{{ old('quantity', $product->stock ?? '') }}">
                            @error('quantity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Purchase Price -->
                        <div>
                            <label for="purchase_price" class="block text-sm font-medium text-gray-700 mb-2">
                                Purchase Price  without GST(₹)<span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₹</span>
                                <input type="number" id="purchase_price" name="purchase_price" required min="0" step="0.01"
                                    class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('purchase_price') border-red-500 @enderror"
                                    placeholder="0.00" value="{{ old('purchase_price', $product->purchase_price ?? '') }}">
                            </div>
                            @error('purchase_price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Sale Price -->
                        <div>
                            <label for="sale_price" class="block text-sm font-medium text-gray-700 mb-2">
                                Sale Price(%)<span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                {{-- <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₹</span> --}}
                                <input type="number" id="sale_price" name="sale_price" required min="0" step="1"
                                    class="w-full pl-4 pr-8 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('sale_price') border-red-500 @enderror"
                                    placeholder="0.00" value="{{ old('sale_price', $product->sale_rate ?? '') }}">
                                    <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">%</span>
                            </div>
                            @error('sale_price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Online Price -->
                        <div>
                            <label for="online_price" class="block text-sm font-medium text-gray-700 mb-2">
                                Online Price (₹)
                            </label>
                            <div class="relative">
                                {{-- <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₹</span> --}}
                                <input type="number" id="online_price" name="online_price" min="0" step="0.01"
                                    class="w-full pl-4 pr-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('online_price') border-red-500 @enderror"
                                    placeholder="0.00" value="{{ old('online_price', $product->online_rate ?? '') }}">
                                    <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">%</span>
                                    
                            </div>
                            @error('online_price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Discount -->
                        <div>
                            <label for="discount" class="block text-sm font-medium text-gray-700 mb-2">
                                Discount (Rs)
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 ">₹</span>
                                <input type="number" id="discount" name="discount" min="0" max="100" step="0.01"
                                    class="w-full px-8 py-3 pr-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('discount') border-red-500 @enderror"
                                    placeholder="0.00" value="{{ old('discount', $product->discount ?? '') }}">
                                {{-- <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">%</span> --}}
                            </div>
                            @error('discount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Delivery Charges -->
                        <div>
                            <label for="delivery_charges" class="block text-sm font-medium text-gray-700 mb-2">
                                Delivery Charges (%)<span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                {{-- <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₹</span> --}}
                                <input type="number" id="delivery_charges" name="delivery_charges" min="0" step="1"
                                    class="w-full pl-4 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('delivery_charges') border-red-500 @enderror"
                                    placeholder="0.00" value="{{ old('delivery_charges', $product->delivery_charges ?? '') }}">
                                    <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">%</span>
                            </div>
                            @error('delivery_charges')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- Mrp -->
                        <div>
                            <label for="delivery_charges" class="block text-sm font-medium text-gray-700 mb-2">
                                MRP (₹)<span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">₹</span>
                                <input type="number" id="mrp" name="mrp" min="0" step="1"
                                    class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('mrp') border-red-500 @enderror"
                                    placeholder="0.00" value="{{ old('mrp', $product->details->mrp ?? '') }}">
                                    {{-- <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">%</span> --}}
                            </div>
                            @error('delivery_charges')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                         <!-- Special Offer -->
                        <div>
                            <label for="special_offer" class="block text-sm font-medium text-gray-700 mb-2">
                                Special Offer 
                            </label>
                          
                            <div class="relative">
                                <select id="special_offer" name="special_offer"
                                    class="w-full pl-4 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 appearance-none bg-white">
                                    <option value="">Select</option>
                                    <option value="yes" {{ old('special_offer', $product->special_offer ?? '') == 'yes' ? 'selected' : '' }}>Yes</option>
                                    <option value="no" {{ old('special_offer', $product->special_offer ?? '') == 'no' ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                        </div>

                        <!-- Free Gift / Combo Item -->
                        <div>
                            <label for="free_gift" class="block text-sm font-medium text-gray-700 mb-2">
                                Free Gift / Combo Note 🎁
                            </label>
                            <input type="text" id="free_gift" name="free_gift"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                placeholder="e.g. Free Mouse & Keyboard"
                                value="{{ old('free_gift', $product->free_gift ?? '') }}">
                            @error('free_gift')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Linked Free Product Gift -->
                        <div>
                            <label for="free_product_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Linked Free Gift Product 🛒
                            </label>
                            <select id="free_product_id" name="free_product_id"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200">
                                <option value="">-- No Linked Free Product --</option>
                                @if(isset($allProducts))
                                    @foreach($allProducts as $pItem)
                                        <option value="{{ $pItem->id }}" {{ old('free_product_id', $product->free_product_id ?? '') == $pItem->id ? 'selected' : '' }}>
                                            {{ ($pItem->brand->name ?? '') . ' ' . $pItem->model }} (Stock: {{ $pItem->stock }})
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                            @error('free_product_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Special Offer Extra Discount (%) -->
                        <div>
                            <label for="offer_discount_percent" class="block text-sm font-medium text-gray-700 mb-2">
                                Special Offer Extra Discount (%) ⚡
                            </label>
                            <div class="relative">
                                <input type="number" id="offer_discount_percent" name="offer_discount_percent" min="0" max="100" step="0.1"
                                    class="w-full pl-4 pr-8 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                    placeholder="0.0" value="{{ old('offer_discount_percent', $product->offer_discount_percent ?? '') }}">
                                <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">%</span>
                            </div>
                            @error('offer_discount_percent')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Commission Information Section -->
                <div class="pb-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Commission Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Dealer Point -->
                        <div>
                            <label for="dealer_point" class="block text-sm font-medium text-gray-700 mb-2">
                                Dealer Point (%)
                            </label>
                            <div class="relative">
                                <input type="number" id="dealer_point" name="dealer_point" min="0" step="0.01"
                                    class="w-full px-4 py-3 pr-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                    placeholder="0.00" value="{{ old('dealer_point', $product->dealer_point ?? '') }}">
                                <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">%</span>
                            </div>
                        </div>

                        <!-- Salesmen Point -->
                        <div>
                            <label for="salesmen_point" class="block text-sm font-medium text-gray-700 mb-2">
                                Salesmen Point (%)
                            </label>
                            <div class="relative">
                                <input type="number" id="salesmen_point" name="salesmen_point" min="0" step="0.01"
                                    class="w-full px-4 py-3 pr-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                                    placeholder="0.00" value="{{ old('salesmen_point', $product->salesmen_point ?? '') }}">
                                <span class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500">%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="{{ isset($product) ? 'M5 13l4 4L19 7' : 'M12 6v6m0 0v6m0-6h6m-6 0H6' }}">
                            </path>
                        </svg>
                        {{ isset($product) ? 'Update Product' : 'Add Product' }}
                    </button>
                    <button type="button" onclick="window.history.back()"
                        class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JS Validation -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('border-red-500');
                isValid = false;
            } else {
                field.classList.remove('border-red-500');
            }
        });
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields.');
        }
    });
    const inputs = form.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            this.classList.remove('border-red-500');
        });
    });
});
</script>
@endsection
