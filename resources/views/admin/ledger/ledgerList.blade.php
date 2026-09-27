@extends('layouts.main')
@push('page_title')
    <title>Ladger</title>
@endpush
@section('content_page')
    <div>
        @php
            $routes = ['ledger/salesman', 'ledger/subDealer', 'ledger/customer', 'ledger/company', 'ledger'];
        @endphp
        {{-- <div class="flex  items-center space-x-2">
            <!-- Create Button -->
            <a href="{{ route('customers.create') }}">
                <button
                    class="text-white bg-gradient-to-br from-green-400 to-blue-600 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-green-200 dark:focus:ring-green-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center shadow-lg shadow-green-500/50 dark:shadow-lg dark:shadow-green-800/80">
                    Create New Customer
                </button>
            </a>
        
            <!-- Search Form -->
            <form id="search-form" class="flex flex-1 items-center space-x-2">
                <!--@csrf-->
                <!-- Search Input (fills remaining space) -->
                <input type="text" id="invoice_id" name="search" placeholder="Search using customer details"
                    class="p-2 border rounded flex-1 bg-white"
                    @isset($search)
                        value="{{ $search }}"
                    @endisset>
        
                <!-- Search Button -->
                <button type="submit"
                    class="text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 px-2 py-2 rounded">
                    Search
                </button>
        
                <!-- Reset Button -->
                <a href="{{ route('customers.list') }}"
                    class="text-gray-900 bg-gradient-to-r from-lime-200 via-lime-400 to-lime-500 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-lime-300 dark:focus:ring-lime-800 px-2 py-2 rounded">
                    Reset
                </a>
            </form>
        </div> --}}
        <div class="mx-4 my-3 relative flex justify-between gap-2 items-center">
            <div>
                <h2 class="text-lg font-semibold mb-2">All Ladger Entries </h2>
            </div>
            <!-- Search Form -->
            <form id="search-form" class="flex flex-1 items-center space-x-2"  action="{{route('ledger.list')}}" method="get">
                <!-- Search Input (fills remaining space) -->
                <input type="text" id="invoice_id" name="search" placeholder="Search using details" 
                    class="p-2 border rounded flex-1 bg-white"
                    @isset($search)
                        value="{{ $search }}"
                    @endisset>

                <!-- Search Button -->
                <button type="submit"
                    class="text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 px-2 py-2 rounded">
                    Search
                </button>

                <!-- Reset Button -->
                <a href="{{ route('ledger.list') }}"
                    class="text-gray-900 bg-gradient-to-r from-lime-200 via-lime-400 to-lime-500 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-lime-300 dark:focus:ring-lime-800 px-2 py-2 rounded">
                    Reset
                </a>
            </form>
            <div class="relative">
                <button id="dropdownDividerButton" data-dropdown-toggle="dropdownDivider"
                    class="text-white bg-gray-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                    type="button">
                    @if (request()->is('ledger'))
                        All
                    @elseif(request()->is('ledger/company'))
                        Company
                    @elseif(request()->is('ledger/customers'))
                        Customers
                    @elseif(request()->is('ledger/subDealer'))
                        Sub Dealer
                    @elseif(request()->is('ledger/salesman'))
                        Salesman
                    @else
                        Dropdown
                    @endif
                    <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 4 4 4-4" />
                    </svg>
                </button>
                <!-- Dropdown menu -->
                <div id="dropdownDivider"
                    class="absolute z-50 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-44 dark:bg-gray-700 dark:divide-gray-600">
                    <ul class="py-2  dark:text-gray-200" aria-labelledby="dropdownDividerButton">
                        <li>
                            <a href="{{ route('ledger.list') }}"
                                class="block px-2 py-2 hover:bg-gray-300 dark:hover:bg-gray-600 dark:hover:text-white">All</a>
                        </li>
                        <li>
                            <a href="{{ route('ledger.company') }}"
                                class="block px-2 py-2 hover:bg-gray-300 dark:hover:bg-gray-600 dark:hover:text-white">Company</a>
                        </li>
                        <li>
                            <a href="{{ route('ledger.customer') }}"
                                class="block px-2 py-2 hover:bg-gray-300 dark:hover:bg-gray-600 dark:hover:text-white">Customer</a>
                        </li>
                        <li>
                            <a href="{{ route('ledger.subDealer') }}"
                                class="block px-2 py-2 hover:bg-gray-300 dark:hover:bg-gray-600 dark:hover:text-white">Sub
                                Dealer</a>
                        </li>
                        <li>
                            <a href="{{ route('ledger.salesman') }}"
                                class="block px-2 py-2 hover:bg-gray-300 dark:hover:bg-gray-600 dark:hover:text-white">Salesman</a>
                        </li>
                    </ul>

                </div>
            </div>
        </div>
        <!-- Subheading for Table -->
        <div class="bg-white p-4 rounded shadow overflow-visible">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="py-2 px-2 border">Sl</th>
                        <th class="py-2 px-2 border">Entity Type</th>
                        <th class="py-2 px-2 border">Particulars</th>
                        <th class="py-2 px-2 border">Phone</th>
                        <th class="py-2 px-2 border">Invoice_id</th>
                        <th class="py-2 px-2 border">Details</th>
                        <th class="py-2 px-2 border">Method</th>
                        <th class="py-2 px-2 border">Debit</th>
                        <th class="py-2 px-2 border">Credit</th>
                        {{-- @isset($filter) --}}
                        @if (!in_array(request()->path(), $routes))
                            <th class="py-2 px-2 border">Balance After</th>
                        @endif
                        {{-- @endisset --}}
                        <th class="py-2 px-2 border">Date</th>
                        <th class="py-2 px-2 border">Created at</th>

                        @empty($filter)
                            <th class="py-2 px-2 border">Action</th>
                        @endempty
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = 1;
                    @endphp
                    @foreach ($ladgers as $key => $item)
                        <tr class="border border-gray-300">
                            <td class="py-2 px-2 border border-gray-300">{{ $i }}</td>
                            <td class="py-2 px-2 border border-gray-300">
                                @if ($item->entity_type == 'company')
                                    Company
                                @elseif($item->entity_type == 'customer')
                                    Customer
                                @elseif($item->entity_type == 'salesman')
                                    Sub Delear
                                @elseif($item->entity_type == 'manager')
                                    Manager
                                @elseif($item->entity_type == 'user')
                                    Salesman
                                @endif
                            </td>
                            <td class="py-2 px-2 border border-gray-300">
                                @isset($item->customer_id)
                                    {{ $item->customer->name }}
                                @endisset
                                @isset($item->company_id)
                                    {{ $item->company->name }}
                                @endisset
                                @isset($item->user_id)
                                    {{ $item->user->name }}
                                @endisset
                            </td>
                            <td class="py-2 px-1 border border-gray-300">
                                @isset($item->customer_id)
                                    {{ $item->customer->phone }}
                                @endisset
                                @isset($item->company_id)
                                    {{ $item->company->phone }}
                                @endisset
                                @isset($item->user_id)
                                    {{ $item->user->phone }}
                                @endisset
                            </td>
                            <td class="py-2 px-2 border border-gray-300 text-center">
                                {{ $item->invoice_id }}
                            </td>
                            <td class="py-2 px-2 border border-gray-300 text-center">{{ $item->transaction_type }} </td>
                            <td class="py-2 px-2 border border-gray-300 text-center">{{ $item->method }} </td>
                            <td class="py-2 px-2 border border-gray-300">
                                @if ($item->type == 'debit')
                                    {{ $item->amount }}
                                @endif
                            </td>
                            <td class="py-2 px-2 border border-gray-300">
                                @if ($item->type == 'credit')
                                    {{ $item->amount }}
                                @endif
                            </td>
                            {{-- @isset($filter) --}}
                            @if (!in_array(request()->path(), $routes))
                                <td class="py-2 px-2 border border-gray-300 text-center">
                                    {{ $item->balance_after }}
                                </td>
                            @endif
                            {{-- @endisset --}}

                            <td class="py-2 px-2 border border-gray-300 text-center">
                                @isset($item->date)
                                    {{ $item->date }}
                                @endisset
                            </td>
                            <td class="py-2 px-2 border border-gray-300 text-center">
                                {{ $item->created_at->format('d-M-y') }} </td>
                            @empty($filter)
                                <td class="flex justify-center ">
                                    <a id="action" href="{{ route('ledger.show', ['id' => $item->id]) }}"><button
                                            class="mt-1 bg-green-800 text-white px-3 py-1 rounded hover:bg-red-700"><i
                                                class="fa-solid fa-circle-info"></i></button></a>
                                </td>
                            @endempty

                        </tr>
                        @php
                            $i++;
                        @endphp
                    @endforeach
                </tbody>
            </table>
            @if ($i == 1)
                <div class="text-red-600 text-center">No record found</div>
            @endif
        </div>
        <div class="py-4">
            {{ $ladgers->links() }}
        </div>
    </div>
@endsection
@push('extra_style')
@endpush
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
