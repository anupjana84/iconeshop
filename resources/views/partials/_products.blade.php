@foreach ($products as $product)
    @php
        $pd = $product->details ?? null;
        $thumbnail = $pd->thumbnail_image ?? null;
        $images = $pd && $pd->image ? json_decode($pd->image, true) : [];
        $images = is_array($images) ? $images : [];

        $so = $product->specialOffer ?? null;
        $isSoActive = $so && $so->isCurrentlyActive();

        $effectivePrice = (float) $product->online_price;
        $specialOfferBadgeText = null;
        $freeGiftName = null;
        $freeGiftImage = null;
        $freeProductObj = null;

        if ($isSoActive) {
            if ($so->offer_type === 'flat' && $so->flat_discount) {
                $effectivePrice = max(0, $effectivePrice - (float) $so->flat_discount);
                $specialOfferBadgeText = '₹' . number_format($so->flat_discount, 0) . ' FLAT OFF';
            } elseif ($so->offer_type === 'percentage' && $so->percentage_discount) {
                $effectivePrice = max(0, $effectivePrice - ($effectivePrice * ((float) $so->percentage_discount / 100)));
                $specialOfferBadgeText = '-' . $so->percentage_discount . '% OFF';
            } elseif ($so->offer_type === 'free_product' && $so->freeProduct) {
                $freeProductObj = $so->freeProduct;
            }
        }

        if (!$freeProductObj && $product->freeProduct) {
            $freeProductObj = $product->freeProduct;
        }

        if ($freeProductObj) {
            $freeGiftName = ($freeProductObj->brand->name ?? '') . ' ' . $freeProductObj->model;
            $freeGiftImage = $freeProductObj->details->thumbnail_image ?? null;
        } elseif ($isSoActive && $so->offer_type === 'free_product') {
            $freeGiftName = 'Free Product';
        } elseif (!empty($product->free_gift)) {
            $freeGiftName = $product->free_gift;
        }

        $showOfferBadge = $isSoActive || (!$so && isset($product->special_offer) && $product->special_offer == 'yes');

        $productData = [
            'id' => $product->id,
            'name' => $product->category->name . ' ' . $product->brand->name . ' ' . $product->model,
            'price' => $effectivePrice,
            'original_price' => (float) $product->online_price,
            'mrp' => $product->details->mrp ?? null,
            'thumbnail' => $thumbnail,
            'delivery_charges' => $product->delivery_charges_amount ?? 0,
            'images' => $images,
            'description' => $pd->description ?? '',
            'special_offer' => $showOfferBadge ? 'yes' : 'no',
            'free_gift' => $freeGiftName,
            'offer_discount' => $isSoActive ? ($so->percentage_discount ?? null) : null
        ];
    @endphp

    <div class="bg-white border rounded-2xl shadow-sm overflow-hidden cursor-pointer openProductCard relative group"
        data-product='@json($productData)'>

        <!-- Special Offer / Free Gift Golden Badge (Top Right) -->
        @if (!empty($freeGiftImage))
            <div style="
                position: absolute;
                top: 10px;
                right: 10px;
                width: 4.5rem;
                height: 4.5rem;
                border-radius: 9999px;
                background: linear-gradient(to bottom right, #d4af37, #b88a16, #8f6810);
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                box-shadow: 0 4px 12px rgba(0,0,0,0.35);
                z-index: 20;
                overflow: hidden;
                padding: 3px;
            " title="Free Gift: {{ $freeGiftName }}">
                <!-- Outer Ring -->
                <div style="
                    position: absolute;
                    inset: 2px;
                    border-radius: 9999px;
                    border: 2px solid rgba(255,255,255,0.9);
                    pointer-events: none;
                    z-index: 10;
                "></div>

                <!-- Free Gift Image Container -->
                <div class="w-full h-full bg-white rounded-full flex items-center justify-center overflow-hidden p-1 relative">
                    <img src="{{ $freeGiftImage }}" alt="{{ $freeGiftName }}" class="w-full h-full object-contain">
                    <div class="absolute bottom-0 inset-x-0 bg-amber-900/90 text-[8px] font-black text-amber-200 text-center uppercase py-0.5 leading-none">
                        GIFT
                    </div>
                </div>
            </div>
        @elseif($showOfferBadge)
            <div style="
                                            position: absolute;
                                            top: 10px;
                                            right: 10px;
                                            width: 4.5rem;
                                            height: 4.5rem;
                                            border-radius: 9999px;
                                            background: linear-gradient(to bottom right, #d4af37, #b88a16, #8f6810);
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                            box-shadow: 0 4px 12px rgba(0,0,0,0.35);
                                            z-index: 20;
                                        ">
                <!-- Outer Ring -->
                <div style="
                                            position: absolute;
                                            inset: 3px;
                                            border-radius: 9999px;
                                            border: 2px solid rgba(255,255,255,0.9);
                                        "></div>

                <!-- Text -->
                <div
                    style="position: relative; text-align: center; color: black; background: linear-gradient(145deg, #ffe98b, #f8ce45)">
                    <div style="font-weight: 900; font-size: 11px; line-height: 0.95; text-shadow: 0 1px 2px rgba(0,0,0,0.35);">
                        SPECIAL
                    </div>
                    <div
                        style="font-weight: 900; font-size: 9px; line-height: 0.95; margin-top:2px; text-shadow: 0 1px 2px rgba(0,0,0,0.35);">
                        OFFER!
                    </div>
                </div>
            </div>
        @endif

        <!-- Product Image -->
        <div class="w-full h-40 bg-gray-50 flex items-center justify-center overflow-hidden relative">
            @if ($thumbnail)
                <img src="{{ $thumbnail }}" alt="{{ $product->name }}"
                    class="object-contain w-full h-full p-2 transition-transform duration-300 group-hover:scale-105">
            @else
                <div class="text-gray-400">No image</div>
            @endif
        </div>

        <!-- Product Info -->
        <div class="p-3">
            <h3 class="text-sm font-medium truncate">
                {{ $product->category->name . ' ' . $product->brand->name . ' ' . $product->model }}
            </h3>

            <div class="mt-2 flex flex-wrap items-baseline gap-x-2 gap-y-1">
                <span class="text-lg font-bold text-gray-900">₹{{ number_format($effectivePrice, 2) }}</span>

                @if ($effectivePrice < (float) $product->online_price)
                    <span class="text-xs line-through text-gray-400">
                        ₹{{ number_format($product->online_price, 2) }}
                    </span>
                @elseif ($product->details && $product->details->mrp && $product->details->mrp > $effectivePrice)
                    <span class="text-xs line-through text-gray-400">
                        ₹{{ number_format($product->details->mrp, 2) }}
                    </span>
                    @php
                        $off = round((($product->details->mrp - $effectivePrice) / $product->details->mrp) * 100, 1);
                    @endphp
                    <span class="text-xs font-bold uppercase text-red-600">
                        -{{ $off }}% OFF
                    </span>
                @endif
            </div>

            <!-- Special Offer / Free Gift Label Below Price -->
            @if($isSoActive)
                <div class="mt-2 space-y-1">
                    @if(!empty($specialOfferBadgeText))
                        <div
                            class="inline-flex items-center gap-1 bg-gradient-to-r from-red-600 to-amber-600 text-white text-[10px] font-extrabold px-2 py-0.5 rounded-full uppercase tracking-wider shadow-sm">
                            <span>⚡ SPECIAL OFFER: {{ $specialOfferBadgeText }}</span>
                        </div>
                    @endif

                    @if(!empty($freeGiftName))
                        <div
                            class="flex items-center gap-1 bg-green-50 text-green-700 border border-green-200 text-xs font-semibold px-2 py-1 rounded-lg">
                            <span>🎁 Free Gift:</span>
                            <span class="truncate">{{ $freeGiftName }}</span>
                        </div>
                    @endif

                    <div class="text-[10px] text-amber-700 font-semibold flex items-center gap-1">
                        <span>⏳ Offer Ends: {{ \Carbon\Carbon::parse($so->end_date)->diffForHumans() }}</span>
                    </div>
                </div>
            @elseif(!empty($freeGiftName))
                <div
                    class="mt-1.5 flex items-center gap-1 bg-green-50 text-green-700 border border-green-200 text-xs font-semibold px-2 py-1 rounded-lg">
                    <span>🎁</span>
                    <span class="truncate">{{ $freeGiftName }}</span>
                </div>
            @endif
        </div>
    </div>
@endforeach