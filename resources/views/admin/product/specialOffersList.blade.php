@extends('layouts.main')

@push('page_title')
    <title>Special Offer Products</title>
@endpush

@section('content_page')
    <div class="p-4 md:p-6 space-y-6">

        {{-- PAGE HEADER & STATS --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gradient-to-r from-amber-500 via-rose-500 to-red-600 p-6 rounded-2xl text-black shadow-lg">
    <div>
        <div class="flex items-center gap-2">
            <span class="text-3xl">🔥</span>
            <h1 class="text-2xl font-black tracking-wide">
                Special Offer Products
            </h1>
        </div>

        <p class="text-sm mt-1 text-black">
            Manage all products currently having special offers, discounts, or free gift promotions.
        </p>
    </div>

    <div class="flex items-center gap-3">
        <button type="button" onclick="openOfferModal(null)"
            class="bg-white hover:bg-rose-50 text-rose-700 font-extrabold text-xs px-4 py-2.5 rounded-xl shadow-md transition-all flex items-center gap-2 cursor-pointer border border-white/50 hover:scale-105">
            <span class="text-sm">➕</span> Add Special Offer
        </button>
        <div class="bg-white/20 backdrop-blur-md px-4 py-2 rounded-xl text-center border border-white/30">
            <span class="block text-2xl font-extrabold text-black">
                {{ $products->total() }}
            </span>

            <span class="text-xs font-medium text-black">
                Total Offers
            </span>
        </div>
    </div>
</div>

        {{-- SEARCH & FILTER BAR --}}
        <form id="filterForm" action="{{ route('product.special.offers') }}" method="GET"
            class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-white p-5 rounded-2xl shadow-sm border border-gray-100">

            {{-- Category Filter --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Category</label>
                <select name="category_id" onchange="this.form.submit()"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2 text-sm focus:ring-2 focus:ring-rose-500 focus:bg-white transition-all">
                    <option value="">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Brand Filter --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Brand</label>
                <select name="brand_id" onchange="this.form.submit()"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2 text-sm focus:ring-2 focus:ring-rose-500 focus:bg-white transition-all">
                    <option value="">All Brands</option>
                    @foreach ($brands as $brand)
                        <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                            {{ $brand->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Search Keyword --}}
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Search Product</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Model, Brand, Category..."
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl pl-9 pr-3.5 py-2 text-sm focus:ring-2 focus:ring-rose-500 focus:bg-white transition-all">
                    <i class="fas fa-search absolute left-3 top-2.5 text-gray-400 text-sm"></i>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-end gap-2">
                <button type="submit"
                    class="flex-1 bg-rose-600 hover:bg-rose-700 text-white font-semibold py-2 px-4 rounded-xl text-sm transition-all shadow-sm flex items-center justify-center gap-1.5">
                    <i class="fas fa-filter text-xs"></i> Filter
                </button>
                @if(request()->hasAny(['category_id', 'brand_id', 'search']))
                    <a href="{{ route('product.special.offers') }}"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold py-2 px-4 rounded-xl text-sm transition-all flex items-center justify-center">
                        Clear
                    </a>
                @endif
            </div>
        </form>

        {{-- PRODUCTS TABLE --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 text-xs font-bold uppercase tracking-wider border-b border-gray-100">
                            <th class="py-4 px-4">Product Info</th>
                            <th class="py-4 px-4">Selling Price</th>
                            <th class="py-4 px-4">Offer Type & Benefit</th>
                            <th class="py-4 px-4">Validity Period</th>
                            <th class="py-4 px-4 text-center">Status</th>
                            <th class="py-4 px-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse ($products as $product)
                            @php
                                $pd = $product->details;
                                $so = $product->specialOffer;
                                $sellingPrice = $product->online_price ?: $product->sale_price ?: 0;
                                $isOfferValid = false;
                                if ($so) {
                                    $isOfferValid = $so->is_active && \Carbon\Carbon::parse($so->end_date)->gte(now()) && \Carbon\Carbon::parse($so->start_date)->lte(now());
                                } elseif ($product->special_offer === 'yes') {
                                    $isOfferValid = true;
                                }
                            @endphp
                            <tr class="hover:bg-rose-50/40 transition-colors">
                                {{-- Product Info --}}
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-14 h-14 rounded-xl bg-gray-50 border border-gray-100 p-1 flex-shrink-0 flex items-center justify-center overflow-hidden">
                                            @if(!empty($pd->thumbnail_image))
                                                <img src="{{ asset($pd->thumbnail_image) }}" alt="{{ $product->model }}" class="w-full h-full object-contain">
                                            @else
                                                <span class="text-2xl">📦</span>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 line-clamp-1">{{ $product->model }}</div>
                                            <div class="text-xs text-gray-500 flex items-center gap-2 mt-0.5">
                                                <span class="bg-gray-100 px-2 py-0.5 rounded text-gray-600 font-medium">{{ $product->category->name ?? 'Uncategorized' }}</span>
                                                <span class="text-gray-400">•</span>
                                                <span class="font-semibold text-rose-600">{{ $product->brand->name ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Selling Price --}}
                                <td class="py-3 px-4">
                                    <div class="font-black text-emerald-600 text-base">₹{{ number_format($sellingPrice, 2) }}</div>
                                    <div class="text-xs text-gray-500">Stock: <strong class="{{ $product->stock > 0 ? 'text-gray-700' : 'text-red-500' }}">{{ $product->stock }}</strong></div>
                                </td>

                                {{-- Offer Details --}}
                                <td class="py-3 px-4">
                                    @if($so)
                                        @if($so->offer_type === 'flat')
                                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-bold">
                                                <i class="fas fa-tag"></i> Flat ₹{{ number_format($so->flat_discount, 2) }} OFF
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">Final: ₹{{ number_format(max(0, $sellingPrice - $so->flat_discount), 2) }}</div>
                                        @elseif($so->offer_type === 'percentage')
                                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-purple-100 text-purple-800 text-xs font-bold">
                                                <i class="fas fa-percent"></i> {{ $so->percentage_discount }}% OFF
                                            </div>
                                            @php $discAmt = ($sellingPrice * $so->percentage_discount) / 100; @endphp
                                            <div class="text-xs text-gray-500 mt-1">Saves: ₹{{ number_format($discAmt, 2) }}</div>
                                        @elseif($so->offer_type === 'free_product')
                                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100 text-amber-900 text-xs font-bold">
                                                <i class="fas fa-gift text-rose-600"></i> Free Gift
                                            </div>
                                            @if($so->freeProduct)
                                                <div class="text-xs text-gray-700 font-semibold mt-1 flex items-center gap-1">
                                                    <span>🎁 {{ $so->freeProduct->brand->name ?? '' }} {{ $so->freeProduct->model }}</span>
                                                </div>
                                            @endif
                                        @endif
                                    @elseif($product->special_offer === 'yes')
                                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-100 text-rose-800 text-xs font-bold">
                                            <i class="fas fa-fire"></i> Special Offer Enabled
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs italic">No active configuration</span>
                                    @endif
                                </td>

                                {{-- Validity --}}
                                <td class="py-3 px-4">
                                    @if($so && $so->start_date && $so->end_date)
                                        <div class="text-xs font-medium text-gray-700">
                                            <div>📅 {{ \Carbon\Carbon::parse($so->start_date)->format('d M Y') }}</div>
                                            <div class="text-gray-400">to {{ \Carbon\Carbon::parse($so->end_date)->format('d M Y') }}</div>
                                        </div>
                                        @if($isOfferValid)
                                            <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-700">ACTIVE NOW</span>
                                        @else
                                            <span class="inline-block mt-1 px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700">EXPIRED / INACTIVE</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>

                                {{-- Active Switch --}}
                                <td class="py-3 px-4 text-center">
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" class="sr-only peer"
                                            onchange="toggleSpecialOfferStatus({{ $product->id }}, this.checked)"
                                            {{ $product->special_offer === 'yes' ? 'checked' : '' }}>
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rose-600"></div>
                                    </label>
                                </td>

                                {{-- Actions --}}
                                <td class="py-3 px-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button onclick="openOfferModal({{ $product->id }}, '{{ addslashes($product->model) }}', {{ $sellingPrice }})"
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-xl transition-colors" title="Edit Special Offer">
                                            <i class="fas fa-edit text-base"></i>
                                        </button>
                                        <button onclick="deleteOffer({{ $so->id ?? $product->id }})"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-xl transition-colors" title="Remove Offer">
                                            <i class="fas fa-trash text-base"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-500">
                                    <div class="text-4xl mb-2">🎁</div>
                                    <div class="font-bold text-gray-700">No Special Offer Products Found</div>
                                    <p class="text-xs text-gray-400 mt-1">You can add special offers to any product from Product List or click below.</p>
                                    <a href="{{ route('product.list') }}" class="inline-block mt-4 px-4 py-2 bg-rose-600 text-white font-semibold rounded-xl text-xs hover:bg-rose-700 transition-all">
                                        Go to Product List
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($products->hasPages())
                <div class="p-4 border-t border-gray-100 bg-gray-50">
                    {{ $products->links() }}
                </div>
            @endif
        </div>

    </div>

    {{-- EDIT SPECIAL OFFER MODAL --}}
    {{-- EDIT / ADD SPECIAL OFFER MODAL --}}
    <div id="specialOfferModal" class="fixed inset-0 z-50 hidden bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden transform transition-all">
            <div class="bg-gradient-to-r from-amber-500 to-rose-600 p-5 text-white flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">🔥</span>
                    <h3 class="font-extrabold text-lg" id="modalProductName">Configure Special Offer</h3>
                </div>
                <button type="button" onclick="closeOfferModal()" class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/40 text-white flex items-center justify-center font-bold text-2xl leading-none transition-colors shadow-sm cursor-pointer" title="Close Modal">&times;</button>
            </div>

            <form id="offerForm" onsubmit="saveSpecialOffer(event)" class="p-6 space-y-4">
                <input type="hidden" id="modal_product_id" name="product_id">
                <input type="hidden" id="modal_offer_id" name="offer_id">

                {{-- Selected Main Product Display Bar --}}
                <div id="modal_selected_product_bar" class="hidden bg-gradient-to-r from-amber-50 to-rose-50 p-3.5 rounded-xl border border-rose-200/80 text-xs font-bold text-gray-800 flex items-center gap-2.5 shadow-sm">
                    <span class="text-xl text-rose-600 flex-shrink-0">📦</span>
                    <div class="min-w-0 flex-1">
                        <div class="text-[10px] text-rose-800 uppercase tracking-wider font-extrabold">Main Product / মূল প্রোডাক্ট:</div>
                        <div id="modal_main_product_title" class="text-xs md:text-sm text-gray-900 font-extrabold truncate"></div>
                    </div>
                </div>

                {{-- Category & Product Selection (In Front - Hidden when product is pre-selected) --}}
                <div id="modal_top_select_block" class="bg-gradient-to-r from-amber-50 to-rose-50 p-4 rounded-xl border border-rose-200/60 space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-800 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                            <span class="text-rose-600">📁</span> Select Category / ক্যাটাগরি <span class="text-red-500">*</span>
                        </label>
                        <select id="modal_category_id" onchange="onModalCategoryChange(this.value)"
                            class="w-full bg-white border border-gray-300 rounded-xl px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition-all font-semibold">
                            <option value="">-- All Categories / ক্যাটাগরি বেছে নিন --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-800 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                            <span class="text-rose-600">📦</span> Select Product / প্রোডাক্ট <span class="text-red-500">*</span>
                        </label>
                        <select id="modal_product_select" onchange="onModalProductChange(this.value)"
                            class="w-full bg-white border border-gray-300 rounded-xl px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-rose-500 focus:border-rose-500 transition-all font-semibold">
                            <option value="">-- Select Product / প্রোডাক্ট বেছে নিন --</option>
                            @foreach ($allProducts as $pItem)
                                <option value="{{ $pItem->id }}" data-category="{{ $pItem->category_id }}" data-model="{{ $pItem->model }}" data-brand="{{ $pItem->brand->name ?? '' }}" data-price="{{ $pItem->online_price ?: $pItem->sale_price ?: 0 }}">
                                    {{ $pItem->brand->name ?? '' }} - {{ $pItem->model }} (₹{{ number_format($pItem->online_price ?: $pItem->sale_price ?: 0, 0) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Offer Type Selector --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">Select Offer Type</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="cursor-pointer border-2 border-gray-200 rounded-xl p-3 text-center hover:border-rose-500 transition-all has-[:checked]:border-rose-600 has-[:checked]:bg-rose-50">
                            <input type="radio" name="offer_type" value="flat" onchange="toggleOfferFields('flat')" class="sr-only" checked>
                            <span class="block text-lg">🏷️</span>
                            <span class="text-xs font-bold text-gray-800">Flat Discount</span>
                        </label>
                        <label class="cursor-pointer border-2 border-gray-200 rounded-xl p-3 text-center hover:border-rose-500 transition-all has-[:checked]:border-rose-600 has-[:checked]:bg-rose-50">
                            <input type="radio" name="offer_type" value="percentage" onchange="toggleOfferFields('percentage')" class="sr-only">
                            <span class="block text-lg">%</span>
                            <span class="text-xs font-bold text-gray-800">Percentage</span>
                        </label>
                        <label class="cursor-pointer border-2 border-gray-200 rounded-xl p-3 text-center hover:border-rose-500 transition-all has-[:checked]:border-rose-600 has-[:checked]:bg-rose-50">
                            <input type="radio" name="offer_type" value="free_product" onchange="toggleOfferFields('free_product')" class="sr-only">
                            <span class="block text-lg">🎁</span>
                            <span class="text-xs font-bold text-gray-800">Free Gift</span>
                        </label>
                    </div>
                </div>

                {{-- Flat Discount Field --}}
                <div id="flat_discount_field">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Flat Discount Amount (₹)</label>
                    <input type="number" step="0.01" min="0" id="flat_discount" name="flat_discount" placeholder="e.g. 500"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-rose-500">
                </div>

                {{-- Percentage Discount Field --}}
                <div id="percentage_discount_field" class="hidden">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Percentage Discount (%)</label>
                    <input type="number" step="0.01" min="0" max="100" id="percentage_discount" name="percentage_discount" placeholder="e.g. 10"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 text-sm focus:ring-2 focus:ring-rose-500">
                </div>

                {{-- Free Gift Select --}}
                <div id="free_product_field" class="hidden bg-rose-50/50 p-3.5 rounded-xl border border-rose-200/60 space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-800 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                            <span class="text-rose-600">📁</span> Select Gift Category / গিফটের ক্যাটাগরি
                        </label>
                        <select id="free_gift_category_id" onchange="filterFreeGiftProductsByCategory(this.value)"
                            class="w-full bg-white border border-gray-300 rounded-xl px-3.5 py-2 text-xs font-semibold focus:ring-2 focus:ring-rose-500">
                            <option value="">-- All Gift Categories / ক্যাটাগরি বেছে নিন --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-800 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                            <span class="text-rose-600">🎁</span> Select Free Gift Product / ফ্রি গিফট প্রোডাক্ট
                        </label>
                        <select id="free_product_id" name="free_product_id" class="w-full bg-white border border-gray-300 rounded-xl px-3.5 py-2.5 text-xs font-semibold focus:ring-2 focus:ring-rose-500">
                            <option value="">-- Choose Free Gift Product / গিফট আইটেম বেছে নিন --</option>
                            @foreach($allProducts as $pItem)
                                <option value="{{ $pItem->id }}" data-category="{{ $pItem->category_id }}">
                                    {{ $pItem->category->name ?? '' }} • {{ $pItem->brand->name ?? '' }} - {{ $pItem->model }} (₹{{ number_format($pItem->online_price ?: $pItem->sale_price ?: 0, 0) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Date Validity --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Start Date</label>
                        <input type="date" id="start_date" name="start_date" required
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-rose-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">End Date</label>
                        <input type="date" id="end_date" name="end_date" required
                            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-rose-500">
                    </div>
                </div>

                {{-- Status --}}
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="is_active" name="is_active" value="1" checked class="w-4 h-4 text-rose-600 rounded">
                    <label for="is_active" class="text-xs font-bold text-gray-700">Activate Special Offer Immediately</label>
                </div>

                {{-- Buttons --}}
                <div class="flex justify-end gap-3 pt-3 border-t">
                    <button type="button" onclick="closeOfferModal()" class="px-4 py-2 text-xs font-bold text-gray-600 hover:bg-gray-100 rounded-xl">Cancel</button>
                    <button type="submit" class="px-5 py-2 text-xs font-extrabold bg-rose-600 hover:bg-rose-700 text-white rounded-xl shadow-md transition-all">Save Special Offer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function filterFreeGiftProductsByCategory(categoryId) {
            const freeProductSelect = document.getElementById('free_product_id');
            if (!freeProductSelect) return;
            const options = freeProductSelect.querySelectorAll('option');
            options.forEach(opt => {
                if (!opt.value) return;
                const cat = opt.getAttribute('data-category');
                if (!categoryId || cat == categoryId) {
                    opt.style.display = '';
                } else {
                    opt.style.display = 'none';
                }
            });
            const currentSelected = freeProductSelect.options[freeProductSelect.selectedIndex];
            if (currentSelected && currentSelected.value && currentSelected.style.display === 'none') {
                freeProductSelect.value = '';
            }
        }

        function filterProductDropdownByCategory(categoryId) {
            const productSelect = document.getElementById('modal_product_select');
            const options = productSelect.querySelectorAll('option');
            
            options.forEach(opt => {
                if (!opt.value) return;
                const cat = opt.getAttribute('data-category');
                if (!categoryId || cat == categoryId) {
                    opt.style.display = '';
                } else {
                    opt.style.display = 'none';
                }
            });

            const currentSelected = productSelect.options[productSelect.selectedIndex];
            if (currentSelected && currentSelected.value && currentSelected.style.display === 'none') {
                productSelect.value = '';
            }
        }

        function onModalCategoryChange(categoryId) {
            filterProductDropdownByCategory(categoryId);
        }

        function onModalProductChange(productId) {
            if (!productId) {
                document.getElementById('modal_product_id').value = '';
                document.getElementById('modalProductName').innerText = 'Configure Special Offer';
                resetOfferFields();
                return;
            }

            document.getElementById('modal_product_id').value = productId;
            const productSelect = document.getElementById('modal_product_select');
            const selectedOpt = productSelect.options[productSelect.selectedIndex];
            
            if (selectedOpt) {
                const modelName = selectedOpt.getAttribute('data-model') || '';
                const brandName = selectedOpt.getAttribute('data-brand') || '';
                const catId = selectedOpt.getAttribute('data-category');
                
                document.getElementById('modalProductName').innerText = `${brandName} ${modelName}`;
                if (catId && !document.getElementById('modal_category_id').value) {
                    document.getElementById('modal_category_id').value = catId;
                }
            }

            fetchOfferDetails(productId);
        }

        function fetchOfferDetails(productId) {
            if (!productId) return;
            fetch(`/product/${productId}/special-offer`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.offer) {
                        const o = data.offer;
                        document.getElementById('modal_offer_id').value = o.id || '';
                        const type = o.offer_type || 'flat';
                        const radio = document.querySelector(`input[name="offer_type"][value="${type}"]`);
                        if (radio) radio.checked = true;
                        toggleOfferFields(type);

                        if (type === 'flat') document.getElementById('flat_discount').value = o.flat_discount || '';
                        if (type === 'percentage') document.getElementById('percentage_discount').value = o.percentage_discount || '';
                        if (type === 'free_product') document.getElementById('free_product_id').value = o.free_product_id || '';

                        if (o.start_date) document.getElementById('start_date').value = o.start_date.split('T')[0];
                        if (o.end_date) document.getElementById('end_date').value = o.end_date.split('T')[0];
                        document.getElementById('is_active').checked = o.is_active == 1;
                    } else {
                        resetOfferFields();
                    }
                });
        }

        function resetOfferFields() {
            document.getElementById('modal_offer_id').value = '';
            document.getElementById('flat_discount').value = '';
            document.getElementById('percentage_discount').value = '';
            document.getElementById('free_product_id').value = '';
            const today = new Date().toISOString().split('T')[0];
            const nextMonth = new Date(Date.now() + 30 * 86400000).toISOString().split('T')[0];
            document.getElementById('start_date').value = today;
            document.getElementById('end_date').value = nextMonth;
            document.getElementById('is_active').checked = true;
            const flatRadio = document.querySelector('input[name="offer_type"][value="flat"]');
            if (flatRadio) flatRadio.checked = true;
            toggleOfferFields('flat');
        }

        function toggleOfferFields(type) {
            document.getElementById('flat_discount_field').classList.toggle('hidden', type !== 'flat');
            document.getElementById('percentage_discount_field').classList.toggle('hidden', type !== 'percentage');
            document.getElementById('free_product_field').classList.toggle('hidden', type !== 'free_product');
        }

        function openOfferModal(productId, modelName, sellingPrice) {
            document.getElementById('modal_category_id').value = '';
            filterProductDropdownByCategory('');

            const selectedBar = document.getElementById('modal_selected_product_bar');
            const topSelectBlock = document.getElementById('modal_top_select_block');
            const productSelect = document.getElementById('modal_product_select');

            if (productId) {
                if (selectedBar) selectedBar.classList.remove('hidden');
                if (topSelectBlock) topSelectBlock.classList.add('hidden');
                if (productSelect) {
                    productSelect.required = false;
                    productSelect.value = productId;
                }
                const mainTitleElem = document.getElementById('modal_main_product_title');
                if (mainTitleElem) mainTitleElem.innerText = modelName || 'Selected Product';
                onModalProductChange(productId);
            } else {
                if (selectedBar) selectedBar.classList.add('hidden');
                if (topSelectBlock) topSelectBlock.classList.remove('hidden');
                if (productSelect) {
                    productSelect.required = true;
                    productSelect.value = '';
                }
                document.getElementById('modal_product_id').value = '';
                document.getElementById('modalProductName').innerText = 'Add Special Offer';
                resetOfferFields();
            }

            document.getElementById('specialOfferModal').classList.remove('hidden');
        }

        function closeOfferModal() {
            document.getElementById('specialOfferModal').classList.add('hidden');
        }

        function saveSpecialOffer(e) {
            e.preventDefault();
            const form = document.getElementById('offerForm');
            const formData = new FormData(form);

            fetch("{{ route('product.special.offer.save') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json"
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Success!', data.message, 'success').then(() => location.reload());
                } else {
                    Swal.fire('Error!', data.message || 'Failed to save offer', 'error');
                }
            })
            .catch(err => {
                Swal.fire('Error!', 'Something went wrong.', 'error');
            });
        }

        function toggleSpecialOfferStatus(productId, isChecked) {
            fetch("{{ route('product.update.special.offer') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    product_id: productId,
                    special_offer: isChecked ? 'yes' : 'no'
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2000 });
                    toast.fire({ icon: 'success', title: data.message });
                } else {
                    Swal.fire('Error!', data.message, 'error');
                }
            });
        }

        function deleteOffer(id) {
            Swal.fire({
                title: 'Remove Special Offer?',
                text: 'This will remove the special offer configuration for this product.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                confirmButtonText: 'Yes, Remove It'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/product/special-offer/${id}`, {
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Accept": "application/json"
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Removed!', data.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Error!', data.message, 'error');
                        }
                    });
                }
            });
        }
    </script>
@endsection
