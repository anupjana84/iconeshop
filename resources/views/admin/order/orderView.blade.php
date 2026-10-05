@extends('layouts.main')
@push('page_title')
    <title>view Order</title>
@endpush
@section('content_page')
    <div>
        <form id="search-form" class="mb-6" method="GET">
            <div class="flex flex-wrap items-center gap-2">
                <input type="search" id="invoice_id" name="search" placeholder="Search using customer info or product code"
                    class="p-2 border rounded-l w-full sm:flex-1 min-w-[250px]"
                    @isset($search)
                        value="{{ $search }}"
                    @endisset>

                <!-- Search Button -->
                <button type="submit"
                    class="text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600
                           hover:bg-gradient-to-br focus:ring-4 focus:outline-none
                           focus:ring-green-300 dark:focus:ring-green-800 px-4 py-2 rounded hover:bg-blue-600">
                    Search
                </button>

                <!-- Reset Button -->
                <a href="{{ route('order.view') }}"
                    class="text-gray-900 bg-gradient-to-r from-lime-200 via-lime-400 to-lime-500
                           hover:bg-gradient-to-br focus:ring-4 focus:outline-none
                           focus:ring-lime-300 dark:focus:ring-lime-800 px-4 py-2 rounded hover:bg-blue-600">
                    Reset
                </a>

                <!-- Dropdown Button -->
                <div class="relative">
                    <button id="dropdownDividerButton" data-dropdown-toggle="dropdownDivider" type="button"
                        class="text-white bg-gray-700 hover:bg-green-800 focus:ring-4 focus:outline-none
                               focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5
                               text-center inline-flex items-center dark:bg-blue-600
                               dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        {{ isset($filter) ? $filter : 'Dropdown divider' }}
                        <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 4 4 4-4" />
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div id="dropdownDivider"
                        class="absolute z-50 hidden bg-white divide-y divide-gray-100
                               rounded-lg shadow-sm w-44 dark:bg-gray-700 dark:divide-gray-600">
                        <ul class="py-2 dark:text-gray-200" aria-labelledby="dropdownDividerButton">
                            <li><a href="{{ route('order.view') }}"
                                    class="block px-4 py-2 hover:bg-gray-300 dark:hover:bg-gray-600 dark:hover:text-white">All</a>
                            </li>
                            <li><a href="{{ route('order.pending') }}"
                                    class="block px-4 py-2 hover:bg-gray-300 dark:hover:bg-gray-600 dark:hover:text-white">Pending</a>
                            </li>
                            <li><a href="{{ route('order.delivered') }}"
                                    class="block px-4 py-2 hover:bg-gray-300 dark:hover:bg-gray-600 dark:hover:text-white">Delivered</a>
                            </li>
                            <li><a href="{{ route('order.canceled') }}"
                                    class="block px-4 py-2 hover:bg-gray-300 dark:hover:bg-gray-600 dark:hover:text-white">Canceled</a>
                            </li>
                            <li><a href="{{ route('order.cash') }}"
                                    class="block px-4 py-2 hover:bg-gray-300 dark:hover:bg-gray-600 dark:hover:text-white">Cash Order</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    </div>



    <!-- Table Section -->
    <div>
        <h2 class="text-lg font-semibold mb-2">All order List </h2> <!-- Subheading for Table -->
        <div class="bg-white p-4 rounded shadow overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-800 text-white text-xs uppercase tracking-wider">
                        <th class="py-2 px-3 border">Sl</th>
                        <th class="py-2 px-3 border">Customer</th>
                        <th class="py-2 px-3 border">Phone Number</th>
                        <th class="py-2 px-3 border">Reference Number</th>
                        <th class="py-2 px-3 border">Product Code</th>
                        <th class="py-2 px-3 border">Order Date</th>
                        <th class="py-2 px-3 border">Status</th>
                        <th class="py-2 px-3 border">Free Gift / Offer</th>
                        <th class="py-2 px-3 border">Delivery Date</th>
                        <th class="py-2 px-3 border">Remark</th>
                        <th class="py-2 px-3 border">Source</th>
                        <th class="py-2 px-3 border">Action</th>
                    </tr>
                </thead>
                <tbody class="text-xs">
                    @php
                        $i = 1;
                    @endphp
                    @foreach ($order as $key => $item)
                        @php
                            $giftsList = [];
                            $offersList = [];
                            if (isset($item->orderItems)) {
                                foreach ($item->orderItems as $oItem) {
                                    $p = $oItem->product ?? null;
                                    if ($p) {
                                        $so = $p->specialOffer ?? null;
                                        if ($so && $so->isCurrentlyActive()) {
                                            if ($so->offer_type === 'free_product' && $so->freeProduct) {
                                                $giftsList[] = ($so->freeProduct->brand->name ?? '') . ' ' . $so->freeProduct->model;
                                            } elseif ($so->offer_type === 'flat') {
                                                $offersList[] = '₹' . number_format($so->flat_discount, 0) . ' FLAT OFF';
                                            } elseif ($so->offer_type === 'percentage') {
                                                $offersList[] = $so->percentage_discount . '% OFF';
                                            }
                                        } elseif (!empty($p->free_gift)) {
                                            $giftsList[] = $p->free_gift;
                                        }
                                    }
                                }
                            }
                            $giftsList = array_unique(array_filter($giftsList));
                            $offersList = array_unique(array_filter($offersList));
                        @endphp
                        <tr class="border">
                            @if (isset($item->direct_salesman))
                                <td class="py-2 px-3 border">{{ $i }}</td>
                                <td class="py-2 px-3 border" colspan="3">Cash Order 
                                    @isset($item->dealer->name)
                                        
                                    (<span class="text-gray-500">By-</span><small>{{ $item->dealer->name }}</small>)
                                    @endisset
                                </td>
                                <td class="py-2 px-3 border border-gray-300">
                                    @if (isset($item->orderItems) && count($item->orderItems) > 0)
                                        <div class="flex flex-col gap-1.5 min-w-[130px]">
                                            @foreach ($item->orderItems as $oItem)
                                                @if (isset($oItem->product))
                                                    <div class="bg-gray-100 border border-gray-300 rounded p-1.5 text-xs shadow-2xs">
                                                        @if (!empty($oItem->product->code))
                                                            <div class="flex flex-col items-center justify-center bg-white p-1 rounded border border-gray-200 mb-1">
                                                                <span class="font-mono font-bold text-gray-900 text-[11px]">
                                                                    {{ $oItem->product->code }}
                                                                </span>
                                                                @if(class_exists('DNS1D'))
                                                                    <div class="mt-0.5 overflow-hidden">
                                                                        {!! DNS1D::getBarcodeSVG($oItem->product->code, 'C128', 0.9, 25, 'black') !!}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endif
                                                        <div class="text-gray-700 font-medium text-[10px] text-center">
                                                            {{ $oItem->product->category->name ?? '' }} - {{ $oItem->product->brand->name ?? '' }}
                                                        </div>
                                                        <div class="text-gray-900 font-bold text-[11px] text-center">
                                                            {{ $oItem->product->model ?? '' }}
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-gray-400 text-xs">N/A</span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>

                                <td class="py-2 px-3 border">{{ $item->created_at->format('d/m/Y') }} </td>
                                <td class="py-2 px-3 border">
                                    @if ($item->order_status == 'pending')
                                        <span
                                            class="bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full text-xs font-bold">Pending</span>
                                    @elseif($item->order_status == 'delivered')
                                        <span
                                            class="bg-green-200 text-green-800 px-2 py-1 rounded-full text-xs font-bold">Delivered</span>
                                    @elseif($item->order_status == 'canceled')
                                        <span class="bg-red-200 text-red-800 px-2 py-1 rounded-full text-xs font-bold">Canceled</span>
                                    @else
                                        <span
                                            class="bg-gray-200 text-gray-800 px-2 py-1 rounded-full text-xs font-bold">Unknown</span>
                                    @endif
                                </td>
                                <td class="py-2 px-3 border text-center">
                                    @if(count($giftsList) > 0)
                                        <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-900 border border-emerald-300 text-[11px] font-black px-2 py-0.5 rounded-full shadow-xs">
                                            🎁 {{ implode(', ', $giftsList) }}
                                        </span>
                                    @elseif(count($offersList) > 0)
                                        <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-900 border border-amber-300 text-[11px] font-black px-2 py-0.5 rounded-full shadow-xs">
                                            ⚡ {{ implode(', ', $offersList) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="py-2 px-3 border">{{ $item->delivery_date }}</td>
                                <td class="py-2 px-3 border text-center">
                                    @if (isset($item->notes))
                                        {{ $item->notes }}
                                    @else
                                        --
                                    @endif
                                </td>
                                <td class="py-2 px-3 border text-center">
                                                                        
                                    {{ $item->source }}
                                </td>
                                <td class="flex justify-center py-2 px-2 border">
                                    @if ($item->order_status == 'pending')
                                        <a id="action" href="{{ route('order.direct.process', ['id' => $item->id]) }}"><button
                                                class="mt-1 bg-yellow-600 text-white px-3 py-1 rounded hover:bg-red-700"><i
                                                    class="fas fa-tasks"></i></button></a>
                                    @else
                                        <a id="action"
                                            href="{{ route('order.sale.view', ['id' => $item->id]) }}"><button
                                                class="mt-1 bg-green-800 text-white px-3 py-1 rounded hover:bg-red-700 mr-2"><i
                                                    class="fa-solid fa-circle-info"></i></button></a>
                                    @endif
                                </td>
                            @else
                                <td class="py-2 px-3 border">{{ $i }}</td>
                                <td class="py-2 px-3 border font-semibold">{{ $item->customer->name ?? 'N/A' }}</td>
                                <td class="py-2 px-3 border">{{ $item->customer->phone ?? 'N/A' }}</td>
                                <td class="py-2 px-3 border">{{ $item->referral_phone ?? '-' }}</td>
                                <td class="py-2 px-3 border border-gray-300">
                                    @if (isset($item->orderItems) && count($item->orderItems) > 0)
                                        <div class="flex flex-col gap-1.5 min-w-[130px]">
                                            @foreach ($item->orderItems as $oItem)
                                                @if (isset($oItem->product))
                                                    <div class="bg-gray-100 border border-gray-300 rounded p-1.5 text-xs shadow-2xs">
                                                        @if (!empty($oItem->product->code))
                                                            <div class="flex flex-col items-center justify-center bg-white p-1 rounded border border-gray-200 mb-1">
                                                                <span class="font-mono font-bold text-gray-900 text-[11px]">
                                                                    {{ $oItem->product->code }}
                                                                </span>
                                                                @if(class_exists('DNS1D'))
                                                                    <div class="mt-0.5 overflow-hidden">
                                                                        {!! DNS1D::getBarcodeSVG($oItem->product->code, 'C128', 0.9, 25, 'black') !!}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        @endif
                                                        <div class="text-gray-700 font-medium text-[10px] text-center">
                                                            {{ $oItem->product->category->name ?? '' }} - {{ $oItem->product->brand->name ?? '' }}
                                                        </div>
                                                        <div class="text-gray-900 font-bold text-[11px] text-center">
                                                            {{ $oItem->product->model ?? '' }}
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-gray-400 text-xs">N/A</span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="py-2 px-3 border">{{ $item->created_at->format('d/m/Y') }} </td>


                                <td class="py-2 px-3 border">
                                    @if ($item->order_status == 'pending')
                                        <span
                                            class="bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full text-xs font-bold">Pending</span>
                                    @elseif($item->order_status == 'delivered')
                                        <span
                                            class="bg-green-200 text-green-800 px-2 py-1 rounded-full text-xs font-bold">Delivered</span>
                                    @elseif($item->order_status == 'canceled')
                                        <span class="bg-red-200 text-red-800 px-2 py-1 rounded-full text-xs font-bold">Canceled</span>
                                    @else
                                        <span
                                            class="bg-gray-200 text-gray-800 px-2 py-1 rounded-full text-xs font-bold">Unknown</span>
                                    @endif
                                </td>
                                <td class="py-2 px-3 border text-center">
                                    @if(count($giftsList) > 0)
                                        <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-900 border border-emerald-300 text-[11px] font-black px-2 py-0.5 rounded-full shadow-xs">
                                            🎁 {{ implode(', ', $giftsList) }}
                                        </span>
                                    @elseif(count($offersList) > 0)
                                        <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-900 border border-amber-300 text-[11px] font-black px-2 py-0.5 rounded-full shadow-xs">
                                            ⚡ {{ implode(', ', $offersList) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">-</span>
                                    @endif
                                </td>
                                <td class="py-2 px-3 border">{{ $item->delivery_date }}</td>
                                <td class="py-2 px-3 border text-center">
                                    @if (isset($item->notes))
                                        {{ $item->notes }}
                                    @else
                                        --
                                    @endif
                                </td>
                                <td class="py-2 px-3 border text-center">
                                    {{ $item->source }}
                                </td>

                                <td class="flex justify-center py-2 px-2 border">
                                    @if ($item->order_status == 'pending')
                                        <a id="action" href="{{ route('order.process', ['id' => $item->id]) }}"><button
                                                class="mt-1 bg-yellow-600 text-white px-3 py-1 rounded hover:bg-red-700"><i
                                                    class="fas fa-tasks"></i></button></a>
                                    @else
                                        <a id="action"
                                            href="{{ route('order.sale.view', ['id' => $item->id]) }}"><button
                                                class="mt-1 bg-green-800 text-white px-3 py-1 rounded hover:bg-red-700 mr-2"><i
                                                    class="fa-solid fa-circle-info"></i></button></a>
                                    @endif
                                </td>
                            @endif


                        </tr>
                        @php
                            $i++;
                        @endphp
                </tbody>
                @endforeach
            </table>
            @if ($i == 1)
                <div class="text-red-600 text-center">No record found</div>
            @endif
        </div>
        <div class="py-4">
            {{ $order->links() }}
        </div>
    </div>
@endsection
@push('extra_js')
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.1/dist/flowbite.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const btn = document.getElementById("dropdownDividerButton");
            const menu = document.getElementById("dropdownDivider");

            btn.addEventListener("click", () => {
                menu.classList.toggle("hidden");
            });
        });
    </script>
@endpush
