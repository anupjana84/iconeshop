@extends('layouts.main')
@push('page_title')
    <title>view Order</title>
@endpush
@section('content_page')
    <div>
        <form id="search-form" class="mb-6" method="GET">
            <div class="flex flex-wrap items-center gap-2">
                <input type="search" id="invoice_id" name="search" placeholder="Search using customer information"
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
                    <tr class="bg-gray-800 text-white">
                        <th class="py-2 px-4 border">Sl</th>
                        <th class="py-2 px-4 border">Customer</th>
                        <th class="py-2 px-4 border">Phone Number</th>
                        <th class="py-2 px-4 border">Reference Number</th>
                        <th class="py-2 px-4 border">Order Date</th>
                        <th class="py-2 px-4 border">status</th>
                        <th class="py-2 px-4 border">Delivery Date</th>
                        <th class="py-2 px-4 border">Remark</th>
                        <th class="py-2 px-4 border">Source</th>
                        <th class="py-2 px-4 border">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = 1;
                    @endphp
                    @foreach ($order as $key => $item)
                        <tr class="border">
                            @if (isset($item->direct_salesman))
                                <td class="py-2 px-4 border">{{ $i }}</td>
                                <td class="py-2 px-4 border" colspan="3">Cash Order 
                                    @isset($item->dealer->name)
                                        
                                    (<span class="text-gray-500">By-</span><small>{{ $item->dealer->name }}</small>)
                                    @endisset
                                </td>

                                <td class="py-2 px-4 border">{{ $item->created_at->format('d/m/Y') }} </td>
                                <td class="py-2 px-4 border">
                                    @if ($item->order_status == 'pending')
                                        <span
                                            class="bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full text-sm">Pending</span>
                                    @elseif($item->order_status == 'delivered')
                                        <span
                                            class="bg-green-200 text-green-800 px-2 py-1 rounded-full text-sm">Delivered</span>
                                    @elseif($item->order_status == 'canceled')
                                        <span class="bg-red-200 text-red-800 px-2 py-1 rounded-full text-sm">Canceled</span>
                                    @else
                                        <span
                                            class="bg-gray-200 text-gray-800 px-2 py-1 rounded-full text-sm">Unknown</span>
                                    @endif
                                </td>
                                <td class="py-2 px-4 border">{{ $item->delivery_date }}</td>
                                <td class="py-2 px-4 border text-center">
                                    @if (isset($item->notes))
                                        {{ $item->notes }}
                                    @else
                                        --
                                    @endif
                                </td>
                                <td class="py-2 px-4 border text-center">
                                                                        
                                    {{ $item->source }}
                                </td>
                                <td class="flex justify-center ">
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
                                <td class="py-2 px-4 border">{{ $i }}</td>
                                <td class="py-2 px-4 border">{{ $item->customer->name ?? 'N/A' }}</td>
                                <td class="py-2 px-4 border">{{ $item->customer->phone ?? 'N/A' }}</td>
                                <td class="py-2 px-4 border">{{ $item->referral_phone ?? '-' }}</td>
                                <td class="py-2 px-4 border">{{ $item->created_at->format('d/m/Y') }} </td>


                                <td class="py-2 px-4 border">
                                    @if ($item->order_status == 'pending')
                                        <span
                                            class="bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full text-sm">Pending</span>
                                    @elseif($item->order_status == 'delivered')
                                        <span
                                            class="bg-green-200 text-green-800 px-2 py-1 rounded-full text-sm">Delivered</span>
                                    @elseif($item->order_status == 'canceled')
                                        <span class="bg-red-200 text-red-800 px-2 py-1 rounded-full text-sm">Canceled</span>
                                    @else
                                        <span
                                            class="bg-gray-200 text-gray-800 px-2 py-1 rounded-full text-sm">Unknown</span>
                                    @endif
                                </td>
                                <td class="py-2 px-4 border">{{ $item->delivery_date }}</td>
                                <td class="py-2 px-4 border text-center">
                                    @if (isset($item->notes))
                                        {{ $item->notes }}
                                    @else
                                        --
                                    @endif
                                </td>
                                <td class="py-2 px-4 border text-center">
                                    {{ $item->source }}
                                </td>

                                <td class="flex justify-center ">
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
