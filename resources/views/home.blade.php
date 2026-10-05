@extends('layouts.guestLayout.main')

@push('page_title')
    <title>Home</title>
@endpush

@section('content_page')

    {{-- CATEGORY SCROLLER --}}
    <div class="w-full bg-white shadow-sm border-b sticky top-0 z-40 relative group/scroller overflow-hidden">

        {{-- LEFT ARROW --}}
        <button id="catScrollLeft"
            class="absolute ml-3 left-0 top-1/2 -translate-y-1/2 z-50 bg-gradient-to-r from-white via-white to-transparent pl-1 pr-6 hidden items-center justify-center group-hover/scroller:flex transition-all duration-300 h-full rounded-full">

            <div
                class="bg-white border border-green-500 rounded-full w-10 h-10 flex items-center justify-center shadow-[0_0_15px_rgba(34,197,94,0.4)] animate-slide-left mt-14">

                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-12 text-green-600" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M15 19l-7-7 7-7" />

                </svg>

            </div>
        </button>

        <div id="categoryContainer"
            class="flex gap-2 overflow-x-auto whitespace-nowrap p-6 scroll-smooth no-scrollbar mt-14 " style="background-color:#FFFFF;  border-bottom: 1px solid rgba(0,0,0,0.08);

                                                box-shadow:
                                                    0 3px 8px rgba(0,0,0,0.12);">

            {{-- ALL PRODUCTS CHIP --}}
            <a href="{{ url('/') }}" class="inline-block group">
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-medium transition-all duration-300 shadow-sm"
                    style="background: {{ !request()->filled('category') && !request()->filled('special_offer') && !request()->filled('free_gift') ? 'linear-gradient(145deg, #053A40, #1C4E55)' : 'linear-gradient(145deg, #ffffff, #f1f5f9)' }};
                               color: {{ !request()->filled('category') && !request()->filled('special_offer') && !request()->filled('free_gift') ? '#ffffff' : '#334155' }};
                               border: 1px solid rgba(0,0,0,0.1); border-radius: 30px;">
                    <div class="w-8 h-8 rounded-full bg-white/20 flex-shrink-0 flex items-center justify-center">
                        <span class="text-xs">🏪</span>
                    </div>
                    <span class="pr-2 font-semibold">All Products</span>
                </div>
            </a>

            {{-- SPECIAL OFFERS CHIP --}}
            @php
                $soUrlParams = ['special_offer' => 1];
                if (request()->filled('category'))
                    $soUrlParams['category'] = request('category');
                if (request()->filled('search'))
                    $soUrlParams['search'] = request('search');
            @endphp
            <a href="{{ url('/?' . http_build_query($soUrlParams)) }}" class="inline-block group">
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-bold transition-all duration-300 shadow-md transform hover:scale-105"
                    style="background: {{ request('special_offer') == '1' ? 'linear-gradient(145deg, #e11d48, #be123c)' : 'linear-gradient(145deg, #ffe98b, #f8ce45)' }};
                               color: {{ request('special_offer') == '1' ? '#ffffff' : '#171717' }};
                               border: 1px solid rgba(255,255,255,0.7); border-radius: 30px; box-shadow: 0 3px 8px rgba(225, 29, 72, 0.25);">
                    <div
                        class="w-8 h-8 rounded-full bg-white/30 flex-shrink-0 flex items-center justify-center overflow-hidden">
                        <span class="text-sm animate-pulse">⚡</span>
                    </div>
                    <span class="pr-2 flex items-center gap-1">
                        🔥 Special Offers
                        @if(isset($specialOfferProducts) && count($specialOfferProducts) > 0)
                            <span
                                class="px-1.5 py-0.5 text-[10px] rounded-full {{ request('special_offer') == '1' ? 'bg-white text-rose-700' : 'bg-rose-600 text-white' }}">
                                {{ count($specialOfferProducts) }}
                            </span>
                        @endif
                    </span>
                </div>
            </a>

            {{-- FREE GIFTS CHIP --}}
            @php
                $fgUrlParams = ['free_gift' => 1];
                if (request()->filled('category'))
                    $fgUrlParams['category'] = request('category');
                if (request()->filled('search'))
                    $fgUrlParams['search'] = request('search');
            @endphp
            <a href="{{ url('/?' . http_build_query($fgUrlParams)) }}" class="inline-block group">
                <div class="flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-bold transition-all duration-300 shadow-md transform hover:scale-105"
                    style="background: {{ request('free_gift') == '1' ? 'linear-gradient(145deg, #10b981, #047857)' : 'linear-gradient(145deg, #a7f3d0, #34d399)' }};
                               color: {{ request('free_gift') == '1' ? '#ffffff' : '#064e3b' }};
                               border: 1px solid rgba(255,255,255,0.7); border-radius: 30px; box-shadow: 0 3px 8px rgba(16, 185, 129, 0.25);">
                    <div
                        class="w-8 h-8 rounded-full bg-white/30 flex-shrink-0 flex items-center justify-center overflow-hidden">
                        <span class="text-sm animate-bounce">🎁</span>
                    </div>
                    <span class="pr-2 flex items-center gap-1">
                        🎁 Free Gifts
                        @if(isset($freeGiftProducts) && count($freeGiftProducts) > 0)
                            <span
                                class="px-1.5 py-0.5 text-[10px] rounded-full {{ request('free_gift') == '1' ? 'bg-white text-emerald-800' : 'bg-emerald-700 text-white' }}">
                                {{ count($freeGiftProducts) }}
                            </span>
                        @endif
                    </span>
                </div>
            </a>

            {{-- CATEGORIES --}}
            @foreach ($catagories as $category)
                @php
                    $catParams = ['category' => $category->id];
                    if (request()->filled('special_offer'))
                        $catParams['special_offer'] = request('special_offer');
                    if (request()->filled('free_gift'))
                        $catParams['free_gift'] = request('free_gift');
                    if (request()->filled('search'))
                        $catParams['search'] = request('search');
                    $isSelectedCategory = request('category') == $category->id;
                @endphp
                <a href="{{ url('/?' . http_build_query($catParams)) }}" class="inline-block group">
                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-medium transition-all duration-300 shadow-sm"
                        style="
                                    background: {{ $isSelectedCategory ? 'linear-gradient(145deg, #0284c7, #0369a1)' : 'linear-gradient(145deg, #ffe98b, #f8ce45)' }};
                                    border-radius: 30px;
                                    border: 1px solid rgba(255,255,255,0.65);
                                    box-shadow: 0 3px 7px rgba(0,0,0,0.15);
                                    text-decoration: none;
                                    color: {{ $isSelectedCategory ? '#ffffff' : '#171717' }};">

                        <div
                            class="w-8 h-8 rounded-full bg-gray-100 flex-shrink-0 flex items-center justify-center overflow-hidden border border-gray-100 group-hover:border-green-200 transition-colors">
                            @if ($category->image)
                                <img src="{{ $category->image }}" alt="{{ $category->name }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-xs text-gray-400">📦</span>
                            @endif
                        </div>

                        <span class="pr-2 font-semibold">
                            {{ $category->name }}
                        </span>
                    </div>
                </a>
            @endforeach

        </div>

        {{-- RIGHT ARROW --}}
        <button id="catScrollRight"
            class="absolute mr-3 right-0 top-1/2 -translate-y-1/2 z-50 bg-gradient-to-l from-white via-white to-transparent pr-1 pl-6 flex items-center justify-center group-hover/scroller:flex transition-all duration-300 h-full">

            <div style="margin-top: 56px !important;"
                class="bg-white border border-green-500 rounded-full w-10 h-10 flex items-center justify-center shadow-[0_0_15px_rgba(34,197,94,0.4)] animate-slide-right">

                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-12 text-green-600" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M9 5l7 7-7 7" />

                </svg>

            </div>

        </button>

    </div>


    {{-- STYLE --}}
    <style>
        html,
        body {
            overflow-x: hidden !important;
            width: 100% !important;
            position: relative;
            background-color: #CBC2B9;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }

        .no-scrollbar {
            -ms-overflow-style: none !important;
            scrollbar-width: none !important;
        }

        @keyframes slide-left {

            0%,
            100% {
                transform: translateX(0);
            }

            50% {
                transform: translateX(-10px);
            }

        }

        @keyframes slide-right {

            0%,
            100% {
                transform: translateX(0);
            }

            50% {
                transform: translateX(10px);
            }

        }

        .animate-slide-left {
            animation: slide-left 1.5s ease-in-out infinite;
        }

        .animate-slide-right {
            animation: slide-right 1.5s ease-in-out infinite;
        }
    </style>


    {{-- SEARCH BAR --}}
    <div class="w-full bg-white px-3 py-3 shadow-sm border-b relative z-30" style="background-color:#1C4E55;">

        <form method="GET" action="{{ url('/') }}" class="flex gap-2 max-w-4xl mx-auto">

            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search products..." class="flex-1 border border-gray-300 p-2.5 rounded-lg
                                                   shadow-inner bg-white text-black placeholder-gray-500
                                                   focus:outline-none focus:ring-2 focus:ring-green-500" />

            <button
                class="bg-green-600 px-5 text-white rounded-lg hover:bg-green-700 shadow-sm transition-colors flex items-center justify-center">

                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">

                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />

                </svg>

            </button>

        </form>

    </div>


    {{-- TOP PRODUCTS SLIDESHOW --}}
    @if(isset($topProducts) && count($topProducts) > 0)

        <div class="w-full py-4 shadow-sm mb-2 flex justify-center border-b border-gray-100 relative"
            style="background: linear-gradient(135deg, #053A40 0%, #1C4E55 55%, #E2B746 100%);">

            <div class="relative w-full max-w-sm overflow-hidden rounded-xl bg-white" style="height: 200px;">

                @foreach ($topProducts as $index => $product)

                    @php

                        $pd = $product->details ?? null;

                        $thumbnail = $pd->thumbnail_image ?? null;

                        $images = $pd && $pd->image
                            ? json_decode($pd->image, true)
                            : [];

                        $images = is_array($images) ? $images : [];

                        $productData = [

                            'id' => $product->id,

                            'name' =>
                                $product->category->name .
                                ' ' .
                                $product->brand->name .
                                ' ' .
                                $product->model,

                            'price' => (float) $product->online_price,

                            'mrp' => $product->details->mrp ?? null,

                            'thumbnail' => $thumbnail,

                            'delivery_charges' =>
                                $product->delivery_charges_amount ?? 0,

                            'images' => $images,

                            'description' => $pd->description ?? '-',

                        ];

                    @endphp

                    <div class="top-product-slide absolute inset-0 cursor-pointer openProductCard group shadow-md"
                        style="
                                                                                                                                                                transition: transform 0.6s ease-in-out, opacity 0.6s ease-in-out;
                                                                                                                                                                transform: translateX({{ $index == 0 ? '0' : '100%' }});
                                                                                                                                                                opacity: {{ $index == 0 ? '1' : '0' }};
                                                                                                                                                                z-index: {{ $index == 0 ? '10' : '0' }};

                                                                                                                                                            " data-product='@json($productData)'>

                        <div class="w-full h-full bg-white flex justify-center items-center overflow-hidden p-2 pb-10">

                            @if ($thumbnail)

                                <img src="{{ $thumbnail }}" alt="{{ $product->model }}"
                                    class="w-full h-full object-contain drop-shadow transition-transform duration-700 scale-110 group-hover:scale-125">

                            @else

                                <span class="text-xs text-gray-400">
                                    No Image
                                </span>

                            @endif

                        </div>


                        {{-- PRICE --}}
                        <div
                            class="absolute top-4 left-4 flex flex-col items-center justify-center w-20 h-20 bg-white rounded-full shadow-[0_0_20px_rgba(34,197,94,0.3)] border border-green-100 z-50">

                            <span class="text-lg font-bold text-green-700 leading-tight">
                                ₹{{ number_format($product->online_price, 0) }}
                            </span>

                            @if(
                                    $product->details &&
                                    $product->details->mrp &&
                                    $product->details->mrp > $product->online_price
                                )

                                @php

                                    $off = round(
                                        (
                                            ($product->details->mrp -
                                                $product->online_price)
                                            /
                                            $product->details->mrp
                                        ) * 100
                                    );

                                @endphp

                                <span class="text-sm font-extrabold text-red-600 leading-tight">
                                    {{ $off }}% OFF
                                </span>

                            @endif

                        </div>


                        {{-- TITLE --}}
                        <div
                            class="absolute bottom-0 left-0 w-full bg-gradient-to-t from-black/70 via-black/40 to-transparent pt-8 pb-3 px-4 z-20">

                            <p class="text-sm font-medium text-white truncate drop-shadow-md w-full">

                                {{ $product->category->name }}
                                {{ $product->model }}

                            </p>

                        </div>

                    </div>

                @endforeach

            </div>

        </div>


        <script>

            document.addEventListener('DOMContentLoaded', function () {

                const slides =
                    document.querySelectorAll('.top-product-slide');

                if (slides.length > 1) {

                    let currentSlide = 0;

                    setInterval(() => {

                        slides[currentSlide].style.transform =
                            'translateX(-100%)';

                        slides[currentSlide].style.opacity = '0';

                        slides[currentSlide].style.zIndex = '0';

                        const prevSlide = currentSlide;

                        setTimeout(() => {

                            slides[prevSlide].style.transition = 'none';

                            slides[prevSlide].style.transform =
                                'translateX(100%)';

                            setTimeout(() => {

                                slides[prevSlide].style.transition =
                                    'transform 0.6s ease-in-out, opacity 0.6s ease-in-out';

                            }, 50);

                        }, 600);

                        currentSlide =
                            (currentSlide + 1) % slides.length;

                        slides[currentSlide].style.transform =
                            'translateX(0)';

                        slides[currentSlide].style.opacity = '1';

                        slides[currentSlide].style.zIndex = '10';

                    }, 2000);

                }

            });

        </script>

    @endif


    {{-- DEDICATED SPECIAL OFFERS SHOWCASE SECTION --}}
    @if(isset($specialOfferProducts) && count($specialOfferProducts) > 0 && !request()->filled('special_offer') && !request()->filled('free_gift'))
        <div class="w-full py-2 mb-4 mt-2">
            <div class="container mx-auto px-3">
                {{-- Section Header --}}
                <div class="mb-4 p-4 rounded-2xl bg-black text-white border border-gray-800 shadow-lg flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl animate-bounce">🔥</span>
                        <div>
                            <h2 class="text-lg font-bold flex items-center gap-2 text-white">
                                <span>Special Offer Products (বিশেষ অফার প্রোডাক্টস)</span>
                            </h2>
                            <p class="text-xs text-gray-300">
                                Showing items with active special discounts and price cuts
                            </p>
                        </div>
                    </div>
                    <a href="{{ url('/?special_offer=1') }}"
                        class="text-white hover:text-amber-300 font-bold text-xs transition-colors flex items-center gap-1.5 hover:underline">
                        <span>সব অফার দেখুন</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>

                {{-- Horizontal Scrollable Cards --}}
                <div class="flex gap-4 overflow-x-auto pb-4 pt-1 scroll-smooth no-scrollbar [&>*]:flex-shrink-0 [&>*]:w-56 [&>*]:md:w-64">
                    @include('partials._products', ['products' => $specialOfferProducts])
                </div>
            </div>
        </div>
    @endif

    {{-- PRODUCTS CONTAINER --}}
    <div class="container mx-auto px-3 pb-10 mt-3">

        @if(request()->filled('special_offer') && request('special_offer') == '1')
            <div
                class="mb-5 p-4 rounded-2xl bg-black text-white border border-gray-800 shadow-lg flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="text-2xl animate-bounce">🔥</span>
                    <div>
                        <h2 class="text-lg font-bold flex items-center gap-2 text-white">
                            <span>Special Offer Products (বিশেষ অফার প্রোডাক্টস)</span>
                            @if(isset($selectedCategory))
                                <span
                                    class="bg-gray-800 px-2.5 py-0.5 rounded-full text-xs font-bold text-amber-300 border border-amber-500/30">&bull;
                                    {{ $selectedCategory->name }}</span>
                            @endif
                        </h2>
                        <p class="text-xs text-gray-300">
                            Showing items with active special discounts and price cuts
                            @if(isset($selectedCategory))
                                in <strong class="text-amber-300">{{ $selectedCategory->name }}</strong> category
                            @endif
                        </p>
                    </div>
                </div>
                <a href="{{ url('/') }}"
                    class="text-white hover:text-amber-300 font-bold text-xs transition-colors flex items-center gap-1.5 hover:underline">
                    <i class="fas fa-times"></i>
                    <span>Show All Products</span>
                </a>
            </div>
        @elseif(request()->filled('free_gift') && request('free_gift') == '1')
            <div
                class="mb-5 p-4 rounded-2xl bg-gradient-to-r from-emerald-600 via-teal-600 to-emerald-700 text-white shadow-lg flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="text-2xl animate-bounce">🎁</span>
                    <div>
                        <h2 class="text-lg font-bold flex items-center gap-2">
                            <span>Free Gift Offers (ফ্রি গিফট অফার প্রোডাক্টস)</span>
                            @if(isset($selectedCategory))
                                <span class="bg-black/20 px-2.5 py-0.5 rounded-full text-xs font-bold text-emerald-100">&bull;
                                    {{ $selectedCategory->name }}</span>
                            @endif
                        </h2>
                        <p class="text-xs text-emerald-100">
                            Showing products with free gift promotions and combo items
                            @if(isset($selectedCategory))
                                in <strong>{{ $selectedCategory->name }}</strong> category
                            @endif
                        </p>
                    </div>
                </div>
                <a href="{{ url('/') }}"
                    class="bg-white text-emerald-800 font-bold text-xs px-4 py-2 rounded-xl shadow hover:bg-emerald-50 transition-colors flex items-center gap-1.5">
                    <i class="fas fa-times"></i>
                    <span>Show All Products</span>
                </a>
            </div>
        @elseif(request()->filled('category') && isset($selectedCategory))
            <div
                class="mb-5 p-4 rounded-2xl bg-gradient-to-r from-sky-600 via-blue-600 to-indigo-700 text-white shadow-lg flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">📁</span>
                    <div>
                        <h2 class="text-lg font-bold">{{ $selectedCategory->name }} Category Products</h2>
                        <p class="text-xs text-sky-100">Showing all items under <strong>{{ $selectedCategory->name }}</strong>
                            category</p>
                    </div>
                </div>
                <a href="{{ url('/') }}"
                    class="bg-white text-blue-800 font-bold text-xs px-4 py-2 rounded-xl shadow hover:bg-sky-50 transition-colors flex items-center gap-1.5">
                    <i class="fas fa-times"></i>
                    <span>Show All Products</span>
                </a>
            </div>
        @endif

        <div id="productsGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">

            @include('partials._products', [
                'products' => $products
            ])

        </div>


        {{-- LAZY LOADER --}}
        <div id="lazyLoader" class="text-center my-6 hidden">

            <div class="flex justify-center items-center gap-3 text-gray-600">

                <svg class="animate-spin h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">

                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                    </circle>

                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z">
                    </path>

                </svg>

                <span class="font-medium">
                    Loading more...
                </span>

            </div>

        </div>

    </div>


    {{-- PRODUCT DETAILS MODAL --}}
    <div id="productModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black bg-opacity-50 p-4">

        <div class="bg-white rounded-lg max-w-3xl w-full shadow-2xl">

            <div class="flex items-center justify-between p-4 border-b bg-gray-50">

                <h2 id="pmName" class="text-xl font-semibold">
                    Product
                </h2>

                <button id="pmClose" class="text-gray-600 hover:text-gray-900 text-3xl">
                    &times;
                </button>

            </div>


            <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">

                <div>

                    <div class="w-full h-64 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">

                        <img id="pmMainImage" src="" class="object-contain w-full h-full">

                    </div>

                    <div id="pmThumbs" class="mt-3 flex gap-2 overflow-x-auto">
                    </div>

                </div>


                <div>

                    <p id="pmPrice" class="text-3xl font-bold text-green-700">
                    </p>

                    <div class="flex flex-wrap items-center gap-3 mt-1">

                        <p id="pmMrp" class="text-base text-gray-400 line-through">
                        </p>

                        <div id="pmOffBadge" class="hidden bg-red-50 px-2 py-0.5 rounded border border-red-100">

                            <p id="pmOff" class="text-sm font-bold text-red-600">
                            </p>

                        </div>

                    </div>


                    <div id="pmDeliveryRow"
                        class="mt-3 flex items-center gap-2 text-gray-600 bg-gray-50 px-3 py-2 rounded-lg border border-gray-100 w-fit">

                        <span class="text-lg">
                            🚚
                        </span>

                        <p id="pmDelivery_charges" class="text-sm font-medium">
                        </p>

                    </div>

                    <!-- Free Gift / Offer Row -->
                    <div id="pmGiftRow"
                        class="hidden mt-3 items-center gap-2 text-green-800 bg-green-50 px-3.5 py-2.5 rounded-xl border border-green-200">
                        <span class="text-xl">🎁</span>
                        <div>
                            <p class="text-xs font-bold text-green-900 uppercase">Free Gift Offer!</p>
                            <p id="pmGiftText" class="text-sm font-semibold text-green-700"></p>
                        </div>
                    </div>


                    <div id="pmExtra" class="mt-4 text-gray-800 text-base font-bold leading-relaxed">
                    </div>


                    <button
                        class="mt-4 pmAddToCart border border-green-600 p-2 rounded-md text-green-700 hover:bg-green-600 hover:text-white transition">

                        Add to cart

                    </button>

                </div>

            </div>

        </div>

    </div>


    {{-- CART BUTTON --}}
    <button id="cartButton"
        class="fixed bottom-6 right-6 bg-green-600 text-white rounded-full w-14 h-14 shadow-xl flex items-center justify-center text-2xl hover:bg-green-700 transition z-50">

        🛒

    </button>


    {{-- CART MODAL --}}
    <div id="cartModal" class="fixed inset-0 z-50 hidden overflow-y-scroll justify-center bg-black bg-opacity-50 p-4">

        <div class="bg-white rounded-lg max-w-4xl w-full shadow-2xl overflow-y-scroll">

            <div class="flex items-center justify-between p-4 border-b bg-gray-50">

                <h2 class="text-xl font-semibold">
                    Your Cart
                </h2>

                <button id="cartClose" class="text-gray-600 hover:text-gray-900 text-3xl">
                    &times;
                </button>

            </div>


            <div class="p-4 overflow-x-scroll">

                <div class="flex justify-end mb-3">

                    <button id="clearCart" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">

                        Clear Cart

                    </button>

                </div>


                <table class="w-full border text-sm rounded-lg overflow-hidden">

                    <thead class="bg-gray-100">

                        <tr>

                            <th class="border p-2">
                                Image
                            </th>

                            <th class="border p-2 text-left">
                                Product
                            </th>

                            <th class="border p-2 text-center">
                                Qty
                            </th>

                            <th class="border p-2 text-right">
                                Price
                            </th>

                            <th class="border p-2 text-right">
                                Delivery
                            </th>

                            <th class="border p-2 text-center">
                                Remove
                            </th>

                        </tr>

                    </thead>

                    <tbody id="cartTableBody"></tbody>

                </table>


                {{-- ========================================= --}}
                {{-- ORDER FORM --}}
                {{-- ========================================= --}}

                <form id="orderForm" method="POST" action="{{ route('guest.order') }}" class="mt-6 space-y-4">

                    @csrf

                    {{-- PHONE --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Mobile Number</label>
                        <div class="relative">
                            <input required id="phone" name="phone" type="text" maxlength="10" inputmode="numeric"
                                autocomplete="tel" placeholder="Enter 10 Digit Mobile No."
                                class="border p-3 rounded-2xl w-full shadow-sm focus:ring-2 focus:ring-green-500 focus:outline-none">

                            {{-- LOADING --}}
                            <div id="phoneLoading" class="hidden absolute right-3 top-1/2 -translate-y-1/2">
                                <svg class="animate-spin h-5 w-5 text-green-600" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4">
                                    </circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- CUSTOMER FOUND / STATUS MESSAGE --}}
                    <div id="customerMessage" class="hidden rounded-lg px-3 py-2 text-sm">
                    </div>

                    {{-- WHATSAPP OTP CONTAINER (Shown for new customers) --}}
                    <div id="otpSection" class="hidden bg-emerald-50 border border-emerald-200 rounded-xl p-4 space-y-3">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-emerald-800 flex items-center gap-1.5">
                                <span>💬</span> WhatsApp Verification Required
                            </p>
                            <span id="otpVerifiedBadge"
                                class="hidden bg-green-600 text-white text-xs font-bold px-2.5 py-1 rounded-full flex items-center gap-1">
                                ✓ Verified
                            </span>
                        </div>

                        <div id="sendOtpRow" class="flex gap-2 items-center">
                            <input id="otpWhatsapp" type="text" maxlength="10" inputmode="numeric"
                                placeholder="WhatsApp No."
                                class="border p-2.5 rounded-lg w-1/2 text-sm bg-white shadow-sm focus:ring-2 focus:ring-emerald-500 focus:outline-none" />
                            <button type="button" id="sendOtpBtn"
                                class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors flex-1 flex items-center justify-center gap-1">
                                <span>Send OTP</span>
                            </button>
                        </div>

                        <div id="otpInputRow" class="hidden flex gap-2 items-center">
                            <input id="otpInput" type="text" maxlength="4" inputmode="numeric"
                                placeholder="Enter 4-Digit OTP"
                                class="border p-2.5 rounded-lg w-1/2 text-sm bg-white text-center font-bold tracking-widest shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                            <button type="button" id="verifyOtpBtn"
                                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors flex-1 flex items-center justify-center gap-1">
                                <span>Verify OTP</span>
                            </button>
                        </div>

                        <div id="otpMessage" class="hidden text-xs font-medium px-1"></div>
                    </div>

                    {{-- CUSTOMER DETAILS FORM SECTION --}}
                    <div id="customerDetailsSection" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- NAME --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Full Name</label>
                                <input required id="name" name="name" type="text" placeholder="Full Name"
                                    autocomplete="name" class="border p-3 rounded-lg w-full shadow-sm">
                            </div>

                            {{-- WHATSAPP --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">WhatsApp Number</label>
                                <input required id="whatsapp" name="whatsapp" type="text" maxlength="10" inputmode="numeric"
                                    placeholder="WhatsApp Number" class="border p-3 rounded-lg w-full shadow-sm">
                            </div>

                            {{-- DISTRICT --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">District</label>
                                <input required id="district" name="district" type="text" placeholder="District"
                                    class="border p-3 rounded-lg w-full shadow-sm">
                            </div>

                            {{-- PINCODE --}}
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Pincode</label>
                                <div class="relative">
                                    <input required id="pincode" name="pincode" type="text" maxlength="6"
                                        inputmode="numeric" placeholder="6-Digit Pincode"
                                        class="border p-3 rounded-lg w-full shadow-sm focus:ring-2 focus:ring-green-500 focus:outline-none">
                                    <div id="pincodeLoading" class="hidden absolute right-3 top-1/2 -translate-y-1/2">
                                        <svg class="animate-spin h-4 w-4 text-green-600" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div id="pincodeMessage" class="hidden text-xs font-medium mt-1"></div>
                            </div>
                        </div>

                        {{-- ADDRESS --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Full Delivery Address</label>
                            <textarea required id="address" name="address" placeholder="Full Address with Landmark"
                                class="border p-3 rounded-lg w-full shadow-sm h-24"></textarea>
                        </div>

                        {{-- DELIVERY --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Home Delivery Required?</label>
                            <select required id="delivery" name="delivery"
                                class="border p-3 rounded-lg w-full shadow-sm bg-white">
                                <option value="">Select Delivery Preference</option>
                                <option value="Yes">Yes (Home Delivery)</option>
                                <option value="No">No (Store Pickup)</option>
                            </select>
                        </div>
                    </div>

                    {{-- PLACE ORDER BUTTON --}}
                    <button id="placeOrderBtn" type="submit"
                        class="bg-green-600 text-white px-4 py-3.5 rounded-xl hover:bg-green-700 w-full flex items-center justify-center gap-2 font-bold text-base shadow-lg transition-all">
                        <span>Place Order</span>
                    </button>

                </form>

            </div>

        </div>

    </div>

@endsection


@push('extra_js')

    <script>

        document.addEventListener("DOMContentLoaded", () => {


            /* =====================================================
               CATEGORY SCROLL
            ===================================================== */

            const categoryContainer =
                document.getElementById("categoryContainer");

            const catScrollLeft =
                document.getElementById("catScrollLeft");

            const catScrollRight =
                document.getElementById("catScrollRight");


            function updateCategoryArrows() {

                if (!categoryContainer) return;

                const scrollLeft =
                    categoryContainer.scrollLeft;

                const maxScroll =
                    categoryContainer.scrollWidth -
                    categoryContainer.clientWidth;


                if (scrollLeft > 10) {

                    catScrollLeft.classList.remove("hidden");

                } else {

                    catScrollLeft.classList.add("hidden");

                }


                if (scrollLeft < maxScroll - 10) {

                    catScrollRight.classList.remove("hidden");

                } else {

                    catScrollRight.classList.add("hidden");

                }

            }


            if (categoryContainer) {

                categoryContainer.addEventListener(
                    "scroll",
                    updateCategoryArrows
                );


                catScrollLeft.addEventListener(
                    "click",
                    () => {

                        categoryContainer.scrollBy({
                            left: -200,
                            behavior: "smooth"
                        });

                    }
                );


                catScrollRight.addEventListener(
                    "click",
                    () => {

                        categoryContainer.scrollBy({
                            left: 200,
                            behavior: "smooth"
                        });

                    }
                );


                setTimeout(
                    updateCategoryArrows,
                    500
                );

            }


            /* =====================================================
               CART
            ===================================================== */

            const cartKey = "guest_cart";

            const cartButton =
                document.getElementById("cartButton");

            const navCartButton =
                document.getElementById("navCartButton");

            const navCartCount =
                document.getElementById("navCartCount");

            const cartModal =
                document.getElementById("cartModal");

            const cartClose =
                document.getElementById("cartClose");

            const cartTableBody =
                document.getElementById("cartTableBody");

            const clearCartBtn =
                document.getElementById("clearCart");

            const orderForm =
                document.getElementById("orderForm");

            const productModal =
                document.getElementById("productModal");

            const lazyLoader =
                document.getElementById("lazyLoader");

            const productsGrid =
                document.getElementById("productsGrid");


            function getCart() {

                try {

                    return JSON.parse(
                        localStorage.getItem(cartKey)
                    ) || [];

                } catch {

                    return [];

                }

            }


            function saveCart(cart) {

                localStorage.setItem(
                    cartKey,
                    JSON.stringify(cart)
                );

                updateCartButton();

            }


            function updateCartButton() {

                const cart = getCart();

                const itemCount = cart.reduce(
                    (total, item) => total + Number(item.qty || 0),
                    0
                );

                navCartCount.textContent = itemCount;
                navCartCount.classList.toggle("hidden", itemCount === 0);

                if (cart.length > 0) {

                    cartButton.classList.remove("hidden");

                } else {

                    cartButton.classList.add("hidden");

                }

            }


            function addToCart(product) {

                const cart = getCart();

                const existing =
                    cart.find(p => p.id === product.id);


                if (existing) {

                    existing.qty += 1;

                } else {

                    product.qty = 1;

                    cart.push(product);

                }


                saveCart(cart);

            }


            function removeFromCart(id) {

                let cart =
                    getCart().filter(
                        p => p.id !== id
                    );

                saveCart(cart);

                renderCart();

            }


            function clearCart() {

                localStorage.removeItem(
                    cartKey
                );

                renderCart();

                updateCartButton();

            }


            function renderCart() {

                const cart = getCart();

                cartTableBody.innerHTML = "";


                if (cart.length === 0) {

                    cartTableBody.innerHTML = `
                                                            <tr>
                                                                <td colspan="6"
                                                                    class="p-3 text-center text-gray-500">
                                                                    Your cart is empty.
                                                                </td>
                                                            </tr>
                                                        `;

                    return;

                }


                cart.forEach(p => {

                    const tr =
                        document.createElement("tr");


                    tr.innerHTML = `

                                                            <td class="border p-2 text-center">

                                                                <img
                                                                    src="${p.thumbnail || ''}"
                                                                    class="w-12 h-12 object-contain mx-auto">

                                                            </td>


                                                            <td class="border p-2">
                                                                ${p.name}
                                                            </td>


                                                            <td class="border p-2 text-center">

                                                                <input
                                                                    type="number"
                                                                    step="1"
                                                                    min="1"
                                                                    value="${p.qty}"
                                                                    class="qtyInput border w-16 text-center rounded"
                                                                    data-id="${p.id}">

                                                            </td>


                                                            <td class="border p-2 text-right">

                                                                ₹${Number(p.price).toFixed(2)}

                                                            </td>


                                                            <td class="border p-2 text-right">

                                                                ₹${Number(
                        p.delivery_charges || 0
                    ).toFixed(2)}

                                                            </td>


                                                            <td class="border p-2 text-center">

                                                                <button
                                                                    class="removeBtn text-red-600 hover:underline"
                                                                    data-id="${p.id}">

                                                                    Remove

                                                                </button>

                                                            </td>

                                                        `;


                    cartTableBody.appendChild(tr);

                });


                document
                    .querySelectorAll(".removeBtn")
                    .forEach(btn => {

                        btn.addEventListener(
                            "click",
                            () => {

                                removeFromCart(
                                    parseInt(btn.dataset.id)
                                );

                            }
                        );

                    });


                document
                    .querySelectorAll(".qtyInput")
                    .forEach(input => {

                        input.addEventListener(
                            "change",
                            () => {

                                const id =
                                    parseInt(
                                        input.dataset.id
                                    );

                                const newQty =
                                    parseInt(
                                        input.value
                                    ) || 1;


                                const cart =
                                    getCart();

                                const item =
                                    cart.find(
                                        p => p.id === id
                                    );


                                if (item) {

                                    item.qty = newQty;

                                }


                                saveCart(cart);

                            }
                        );

                    });

            }


            /* =====================================================
               MODAL
            ===================================================== */

            function openModal(modal) {

                if (!modal) return;

                modal.classList.remove("hidden");

                modal.classList.add("flex");

            }


            function closeModal(modal) {

                if (!modal) return;

                modal.classList.add("hidden");

                modal.classList.remove("flex");

            }


            /* =====================================================
               PRODUCT MODAL
            ===================================================== */

            function attachProductModalEvents() {

                document
                    .querySelectorAll(".openProductCard")
                    .forEach(card => {

                        if (card.dataset.bound) return;

                        card.dataset.bound = true;


                        card.addEventListener(
                            "click",
                            () => {

                                const data =
                                    JSON.parse(
                                        card.dataset.product
                                    );


                                document
                                    .getElementById("pmName")
                                    .textContent =
                                    data.name || "";


                                document
                                    .getElementById("pmPrice")
                                    .textContent =
                                    data.price
                                        ? "₹" +
                                        Number(
                                            data.price
                                        ).toFixed(2)
                                        : "";


                                const mrp =
                                    data.mrp
                                        ? Number(data.mrp)
                                        : 0;


                                const onlinePrice =
                                    data.price
                                        ? Number(data.price)
                                        : 0;


                                if (mrp > onlinePrice) {

                                    document
                                        .getElementById("pmMrp")
                                        .textContent =
                                        "₹" +
                                        mrp.toFixed(2);


                                    const offPercent =
                                        Math.round(
                                            (
                                                (mrp -
                                                    onlinePrice) /
                                                mrp
                                            ) *
                                            100 *
                                            10
                                        ) / 10;


                                    document
                                        .getElementById("pmOff")
                                        .textContent =
                                        "-" +
                                        offPercent +
                                        "% OFF";


                                    document
                                        .getElementById("pmOffBadge")
                                        .classList
                                        .remove("hidden");

                                } else {

                                    document
                                        .getElementById("pmMrp")
                                        .textContent = "";


                                    document
                                        .getElementById("pmOff")
                                        .textContent = "";


                                    document
                                        .getElementById("pmOffBadge")
                                        .classList
                                        .add("hidden");

                                }

                                const pmGiftRow = document.getElementById("pmGiftRow");
                                const pmGiftText = document.getElementById("pmGiftText");
                                if (pmGiftRow && pmGiftText) {
                                    if (data.free_gift && String(data.free_gift).trim() !== "") {
                                        pmGiftText.textContent = data.free_gift;
                                        pmGiftRow.classList.remove("hidden");
                                        pmGiftRow.classList.add("flex");
                                    } else {
                                        pmGiftRow.classList.add("hidden");
                                        pmGiftRow.classList.remove("flex");
                                    }
                                }


                                const delivery =
                                    data.delivery_charges
                                        ? Number(
                                            data.delivery_charges
                                        )
                                        : 0;


                                const deliveryEl =
                                    document
                                        .getElementById(
                                            "pmDelivery_charges"
                                        );


                                if (delivery > 0) {

                                    deliveryEl.textContent =
                                        "Delivery: ₹" +
                                        delivery.toFixed(2);

                                } else {

                                    deliveryEl.textContent =
                                        "Free Delivery";

                                }


                                document
                                    .getElementById("pmExtra")
                                    .innerHTML =
                                    data.description || "-";


                                const images =
                                    data.images?.length
                                        ? data.images
                                        : (
                                            data.thumbnail
                                                ? [data.thumbnail]
                                                : []
                                        );


                                document
                                    .getElementById(
                                        "pmMainImage"
                                    )
                                    .src =
                                    images[0] || "";


                                const thumbs =
                                    document
                                        .getElementById(
                                            "pmThumbs"
                                        );


                                thumbs.innerHTML = "";


                                images.forEach(src => {

                                    const img =
                                        document
                                            .createElement(
                                                "img"
                                            );


                                    img.src = src;

                                    img.className =
                                        "w-16 h-16 object-cover rounded cursor-pointer border";


                                    img.addEventListener(
                                        "click",
                                        () => {

                                            document
                                                .getElementById(
                                                    "pmMainImage"
                                                )
                                                .src = src;

                                        }
                                    );


                                    thumbs.appendChild(img);

                                });


                                const addBtn =
                                    productModal
                                        .querySelector(
                                            ".pmAddToCart"
                                        );


                                if (addBtn) {

                                    addBtn.onclick = () => {

                                        addToCart(data);

                                        renderCart();

                                        closeModal(
                                            productModal
                                        );

                                        openModal(
                                            cartModal
                                        );

                                    };

                                }


                                openModal(
                                    productModal
                                );

                            }
                        );

                    });

            }


            attachProductModalEvents();


            /* =====================================================
               PRODUCT MODAL CLOSE
            ===================================================== */

            const pmClose =
                document.getElementById(
                    "pmClose"
                );


            if (pmClose) {

                pmClose.addEventListener(
                    "click",
                    () => {

                        closeModal(
                            productModal
                        );

                    }
                );

            }


            productModal.addEventListener(
                "click",
                e => {

                    if (
                        e.target.id ===
                        "productModal"
                    ) {

                        closeModal(
                            productModal
                        );

                    }

                }
            );


            /* =====================================================
               CART MODAL
            ===================================================== */

            cartButton.addEventListener(
                "click",
                () => {

                    renderCart();

                    openModal(
                        cartModal
                    );

                }
            );

            navCartButton.addEventListener(
                "click",
                () => {
                    renderCart();
                    openModal(cartModal);
                }
            );


            cartClose.addEventListener(
                "click",
                () => {

                    closeModal(
                        cartModal
                    );

                }
            );


            cartModal.addEventListener(
                "click",
                e => {

                    if (
                        e.target.id ===
                        "cartModal"
                    ) {

                        closeModal(
                            cartModal
                        );

                    }

                }
            );


            clearCartBtn.addEventListener(
                "click",
                () => {

                    if (
                        confirm(
                            "Clear all items from cart?"
                        )
                    ) {

                        clearCart();

                    }

                }
            );


            /* =====================================================
            /* =====================================================
               🔥 PHONE & WHATSAPP OTP CHECKOUT LOGIC
            ===================================================== */

            const phoneInput = document.getElementById("phone");
            const phoneLoading = document.getElementById("phoneLoading");
            const customerMessage = document.getElementById("customerMessage");

            const otpSection = document.getElementById("otpSection");
            const otpWhatsapp = document.getElementById("otpWhatsapp");
            const sendOtpBtn = document.getElementById("sendOtpBtn");
            const sendOtpRow = document.getElementById("sendOtpRow");
            const otpInputRow = document.getElementById("otpInputRow");
            const otpInput = document.getElementById("otpInput");
            const verifyOtpBtn = document.getElementById("verifyOtpBtn");
            const otpVerifiedBadge = document.getElementById("otpVerifiedBadge");
            const otpMessage = document.getElementById("otpMessage");

            let isExistingCustomer = false;
            let isPhoneVerified = false;
            let phoneRequest = null;
            let resendTimer = null;

            function resetOtpState() {
                otpSection.classList.add("hidden");
                sendOtpRow.classList.remove("hidden");
                otpInputRow.classList.add("hidden");
                otpVerifiedBadge.classList.add("hidden");
                otpMessage.classList.add("hidden");
                otpMessage.textContent = "";
                otpInput.value = "";
                isExistingCustomer = false;
                isPhoneVerified = false;
                if (resendTimer) clearInterval(resendTimer);
                sendOtpBtn.disabled = false;
                sendOtpBtn.innerHTML = "<span>Send OTP</span>";
            }

            phoneInput.addEventListener("input", function () {
                this.value = this.value.replace(/\D/g, "");
                if (this.value.length > 10) {
                    this.value = this.value.substring(0, 10);
                }

                customerMessage.classList.add("hidden");
                resetOtpState();

                if (this.value.length !== 10) {
                    return;
                }

                const phone = this.value;
                phoneLoading.classList.remove("hidden");

                if (phoneRequest) phoneRequest.abort();
                phoneRequest = new AbortController();

                fetch("{{ route('guest.checkPhone') }}", {
                    method: "POST",
                    signal: phoneRequest.signal,
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({ phone: phone })
                })
                    .then(async response => {
                        if (!response.ok) throw new Error("Server returned " + response.status);
                        return response.json();
                    })
                    .then(result => {
                        phoneLoading.classList.add("hidden");

                        if (result.found) {
                            isExistingCustomer = true;
                            isPhoneVerified = true;
                            const data = result.data || {};

                            document.getElementById("name").value = data.name || "";
                            document.getElementById("whatsapp").value = data.whatsapp || "";
                            document.getElementById("district").value = data.district || "";
                            document.getElementById("pincode").value = data.pincode || "";
                            document.getElementById("address").value = data.address || "";

                            customerMessage.textContent = "✓ Existing customer details found & loaded automatically.";
                            customerMessage.className = "rounded-lg px-3 py-2 text-sm bg-green-50 text-green-700 border border-green-200 font-medium";
                            customerMessage.classList.remove("hidden");
                        } else {
                            isExistingCustomer = false;
                            isPhoneVerified = false;

                            otpWhatsapp.value = phone;
                            document.getElementById("whatsapp").value = phone;
                            otpSection.classList.remove("hidden");

                            customerMessage.textContent = "New customer detected. Please verify your WhatsApp number to proceed.";
                            customerMessage.className = "rounded-lg px-3 py-2 text-sm bg-blue-50 text-blue-700 border border-blue-200 font-medium";
                            customerMessage.classList.remove("hidden");
                        }
                    })
                    .catch(error => {
                        phoneLoading.classList.add("hidden");
                        if (error.name === "AbortError") return;

                        console.error("Phone lookup error:", error);
                        customerMessage.textContent = "Unable to check mobile number. Please fill details manually.";
                        customerMessage.className = "rounded-lg px-3 py-2 text-sm bg-amber-50 text-amber-700 border border-amber-200";
                        customerMessage.classList.remove("hidden");
                    });
            });

            /* PINCODE LOOKUP (postalpincode.in API) */
            const pincodeInput = document.getElementById("pincode");
            const pincodeLoading = document.getElementById("pincodeLoading");
            const pincodeMessage = document.getElementById("pincodeMessage");
            const districtInput = document.getElementById("district");

            pincodeInput.addEventListener("input", function () {
                this.value = this.value.replace(/\D/g, "");
                if (this.value.length > 6) {
                    this.value = this.value.substring(0, 6);
                }

                pincodeMessage.classList.add("hidden");
                pincodeMessage.textContent = "";

                if (this.value.length === 6) {
                    const pincode = this.value;
                    pincodeLoading.classList.remove("hidden");

                    fetch(`https://api.postalpincode.in/pincode/${pincode}`)
                        .then(res => res.json())
                        .then(data => {
                            pincodeLoading.classList.add("hidden");
                            if (Array.isArray(data) && data[0] && data[0].Status === "Success" && data[0].PostOffice && data[0].PostOffice.length > 0) {
                                const po = data[0].PostOffice[0];
                                const district = po.District || "";
                                const state = po.State || "";
                                if (district) {
                                    districtInput.value = district;
                                    pincodeMessage.textContent = `✓ ${district}, ${state}`;
                                    pincodeMessage.className = "text-xs font-medium text-emerald-600 mt-1";
                                    pincodeMessage.classList.remove("hidden");
                                } else {
                                    districtInput.value = "";
                                }
                            } else {
                                districtInput.value = "";
                                pincodeMessage.textContent = "Invalid Pincode / Location not found.";
                                pincodeMessage.className = "text-xs font-medium text-red-500 mt-1";
                                pincodeMessage.classList.remove("hidden");
                            }
                        })
                        .catch(err => {
                            pincodeLoading.classList.add("hidden");
                            districtInput.value = "";
                            console.error("Pincode API error:", err);
                        });
                } else {
                    districtInput.value = "";
                }
            });

            /* SEND OTP */
            sendOtpBtn.addEventListener("click", () => {
                const wpNum = otpWhatsapp.value.trim();
                if (!/^\d{10}$/.test(wpNum)) {
                    alert("Please enter a valid 10-digit WhatsApp number.");
                    otpWhatsapp.focus();
                    return;
                }

                sendOtpBtn.disabled = true;
                sendOtpBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
                otpMessage.classList.add("hidden");

                fetch("{{ route('guest.sendOtp') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({ phone: wpNum })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            otpMessage.textContent = "✓ OTP sent to your WhatsApp number (" + wpNum + "). Please enter below.";
                            otpMessage.className = "text-xs font-semibold text-emerald-700 px-1 mt-1";
                            otpMessage.classList.remove("hidden");

                            otpInputRow.classList.remove("hidden");
                            otpInput.focus();

                            let secondsLeft = 30;
                            sendOtpBtn.disabled = true;
                            if (resendTimer) clearInterval(resendTimer);
                            resendTimer = setInterval(() => {
                                secondsLeft--;
                                if (secondsLeft <= 0) {
                                    clearInterval(resendTimer);
                                    sendOtpBtn.disabled = false;
                                    sendOtpBtn.innerHTML = "<span>Resend OTP</span>";
                                } else {
                                    sendOtpBtn.innerHTML = "<span>Resend (" + secondsLeft + "s)</span>";
                                }
                            }, 1000);
                        } else {
                            sendOtpBtn.disabled = false;
                            sendOtpBtn.innerHTML = "<span>Send OTP</span>";
                            otpMessage.textContent = data.message || "Failed to send OTP.";
                            otpMessage.className = "text-xs font-semibold text-red-600 px-1 mt-1";
                            otpMessage.classList.remove("hidden");
                        }
                    })
                    .catch(err => {
                        sendOtpBtn.disabled = false;
                        sendOtpBtn.innerHTML = "<span>Send OTP</span>";
                        console.error(err);
                        otpMessage.textContent = "Error sending OTP. Please try again.";
                        otpMessage.className = "text-xs font-semibold text-red-600 px-1 mt-1";
                        otpMessage.classList.remove("hidden");
                    });
            });

            /* VERIFY OTP */
            function verifyOtpAction() {
                const wpNum = otpWhatsapp.value.trim();
                const otpVal = otpInput.value.trim();

                if (!/^\d{4}$/.test(otpVal)) {
                    otpMessage.textContent = "Please enter the 4-digit OTP code.";
                    otpMessage.className = "text-xs font-semibold text-red-600 px-1 mt-1";
                    otpMessage.classList.remove("hidden");
                    otpInput.focus();
                    return;
                }

                verifyOtpBtn.disabled = true;
                verifyOtpBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';

                fetch("{{ route('guest.verifyOtp') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({ phone: wpNum, otp: otpVal })
                })
                    .then(res => res.json())
                    .then(data => {
                        verifyOtpBtn.disabled = false;
                        verifyOtpBtn.innerHTML = "<span>Verify OTP</span>";

                        if (data.success) {
                            isPhoneVerified = true;
                            document.getElementById("whatsapp").value = wpNum;

                            sendOtpRow.classList.add("hidden");
                            otpInputRow.classList.add("hidden");
                            otpVerifiedBadge.classList.remove("hidden");

                            otpMessage.textContent = "✓ WhatsApp Verified Successfully!";
                            otpMessage.className = "text-xs font-bold text-green-700 px-1 mt-1";
                            otpMessage.classList.remove("hidden");

                            customerMessage.textContent = "✓ WhatsApp Verified. Please enter your details to complete order.";
                            customerMessage.className = "rounded-lg px-3 py-2 text-sm bg-green-50 text-green-700 border border-green-200 font-medium";
                            customerMessage.classList.remove("hidden");
                        } else {
                            otpMessage.textContent = data.message || "Invalid OTP code.";
                            otpMessage.className = "text-xs font-semibold text-red-600 px-1 mt-1";
                            otpMessage.classList.remove("hidden");
                        }
                    })
                    .catch(err => {
                        verifyOtpBtn.disabled = false;
                        verifyOtpBtn.innerHTML = "<span>Verify OTP</span>";
                        console.error(err);
                        otpMessage.textContent = "Verification failed. Please try again.";
                        otpMessage.className = "text-xs font-semibold text-red-600 px-1 mt-1";
                        otpMessage.classList.remove("hidden");
                    });
            }

            verifyOtpBtn.addEventListener("click", verifyOtpAction);

            otpInput.addEventListener("input", function () {
                this.value = this.value.replace(/\D/g, "");
                if (this.value.length === 4) {
                    verifyOtpAction();
                }
            });

            /* ORDER SUBMIT */
            const placeOrderBtn = document.getElementById("placeOrderBtn");

            orderForm.addEventListener("submit", e => {
                e.preventDefault();

                const cart = getCart();
                if (cart.length === 0) {
                    alert("Your cart is empty!");
                    return;
                }

                const phone = phoneInput.value.trim();
                if (!/^\d{10}$/.test(phone)) {
                    alert("Please enter a valid 10-digit mobile number.");
                    phoneInput.focus();
                    return;
                }

                if (!isExistingCustomer && !isPhoneVerified) {
                    alert("Please verify your WhatsApp number with OTP before placing order.");
                    otpWhatsapp.focus();
                    return;
                }

                const originalContent = placeOrderBtn.innerHTML;
                placeOrderBtn.disabled = true;
                placeOrderBtn.classList.add("opacity-75", "cursor-not-allowed");
                placeOrderBtn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> <span>Processing Order...</span>`;

                const simpleCart = cart.map(i => ({ id: i.id, qty: i.qty }));
                const formData = new FormData(orderForm);
                formData.append("cart", JSON.stringify(simpleCart));
                if (otpInput.value) {
                    formData.append("otp", otpInput.value.trim());
                }

                fetch(orderForm.action, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
                        "Accept": "application/json"
                    },
                    body: formData
                })
                    .then(async res => {
                        let data;
                        try {
                            data = await res.json();
                        } catch (e) {
                            console.error("JSON parse error", e);
                            alert("Server error. Please try again.");
                            return;
                        }

                        if (!res.ok) {
                            console.error("Order error:", data);
                            alert(data.error || data.message || "Order failed. Please check your details.");
                            return;
                        }

                        showAlert("Success", data.message || "Order placed successfully!", "success").then(() => {
                            clearCart();
                            location.reload();
                        });
                    })
                    .catch(err => {
                        console.error("Submit error:", err);
                        showAlert("Error", "Order failed. Please try again.", "error");
                    })
                    .finally(() => {
                        placeOrderBtn.disabled = false;
                        placeOrderBtn.classList.remove("opacity-75", "cursor-not-allowed");
                        placeOrderBtn.innerHTML = originalContent;
                    });
            });

            const checkoutMode = new URLSearchParams(window.location.search).get("checkout");
            if (checkoutMode === "1") {
                renderCart();
                openModal(cartModal);
            }


            /* =====================================================
               LAZY LOADING
            ===================================================== */

            let next_page_url =
                "{{ $products->nextPageUrl() }}";

            let loading = false;


            window.addEventListener(
                "scroll",
                () => {

                    if (
                        loading ||
                        !next_page_url
                    ) {

                        return;

                    }


                    const scrollPos =
                        window.scrollY +
                        window.innerHeight;


                    const pageHeight =
                        document.body.scrollHeight;


                    if (
                        scrollPos + 300 >=
                        pageHeight
                    ) {

                        loading = true;


                        lazyLoader.classList.remove(
                            "hidden"
                        );


                        const url =
                            new URL(
                                next_page_url
                            );


                        fetch(
                            url,
                            {

                                headers: {

                                    "X-Requested-With":
                                        "XMLHttpRequest"

                                }

                            }
                        )

                            .then(r =>
                                r.json()
                            )

                            .then(data => {

                                if (data.html) {

                                    productsGrid
                                        .insertAdjacentHTML(
                                            "beforeend",
                                            data.html
                                        );


                                    next_page_url =
                                        data.next_page;


                                    attachProductModalEvents();

                                } else {

                                    next_page_url =
                                        null;

                                }


                                lazyLoader.classList.add(
                                    "hidden"
                                );


                                loading = false;

                            })

                            .catch(err => {

                                console.error(
                                    "Lazy load failed:",
                                    err
                                );


                                lazyLoader.classList.add(
                                    "hidden"
                                );


                                loading = false;

                            });

                    }

                }
            );


            /* =====================================================
               INIT
            ===================================================== */

            updateCartButton();

        });

    </script>

@endpush