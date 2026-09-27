@extends('layouts.main')
@push('page_title')
    <title>Sale Master</title>
@endpush
@section('content_page')
    {{-- search --}}
    @if(!isset($not_show))
    <div class="flex justify-evenly ">
        <div class="flex items-center grow">
            <p>Date Range Search : </p>
        </div>
        <div class="flex grow">
            <!-- Date Range Search Form -->
            <form action="" method="GET" class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Start Date -->
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                        <input type="date" id="start_date" name="start_date" class="mt-1 p-2 border rounded w-full"
                            value="{{ request('start_date') }}">
                    </div>

                    <!-- End Date -->
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                        <input type="date" id="end_date" name="end_date" class="mt-1 p-2 border rounded w-full"
                            value="{{ request('end_date') }}">
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-end">
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                            Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Total Sale Amount -->
    @if (isset($totalSaleAmount))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            Total Sale Amount : <strong> {{ $totalSaleAmount }}</strong>
        </div>
    @endif

    <hr class="border-gray-300 mb-4">
    <!-- Search Form -->
    <form id="search-form" class="mb-6" method="GET">
        <div class="flex">
            <input type="search" id="invoice_id" name="search" placeholder="Search using Invoice ID"
                class="p-2 border rounded-l w-full"
                @isset($search)
            value="{{ $search }}"
            @endisset>
            <button type="submit"
                class="text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 px-4 py-2 rounded-r hover:bg-blue-600">
                Search
            </button>
            <a href="{{ route('sale.list') }}"
                class="text-gray-900 bg-gradient-to-r from-lime-200 via-lime-400 to-lime-500 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-lime-300 dark:focus:ring-lime-800 rounded ml-2 px-4 py-2 rounded-r hover:bg-blue-600">
                Reset
            </a>
        </div>
    </form>
    @endif

    <!-- Table Section -->
    <div>
                <div class="mb-3 flex justify-end">
    <a href="{{ route('sales.export') }}"
        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
        <i class="fa fa-download mr-1"></i> Download Excel
    </a>
</div>

        <h2 class="text-lg font-semibold mb-2">View Sale Master</h2> <!-- Subheading for Table -->

        <div class="bg-white p-4 rounded shadow overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="py-2 px-4 border">Sl</th>
                        <th class="py-2 px-4 border">Customer Name</th>
                        <th class="py-2 px-4 border">Mobile no</th>
                        <th class="py-2 px-4 border">Invoice Number</th>
                        <th class="py-2 px-4 border">Items</th>
                        <th class="py-2 px-4 border">Product</th>
                        <th class="py-2 px-4 border">Model</th>
                        <th class="py-2 px-4 border">Sale Date</th>
                        <th class="py-2 px-4 border">Total Amount</th>
                        <th class="py-2 px-4 border">GST applicable</th>
                        <th class="py-2 px-4 border">Details</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = 1;
                    @endphp
                    @foreach ($sales as $key => $product)
                        <tr class="border">
                            <td class="py-2 px-4 border">{{ $i }}</td>
                            @if (isset($product->customer_id))
                                <td class="py-2 px-4 border">
                                    @isset($product->customer)
                                        {{ $product->customer->name }}
                                    @endisset
                                </td>
                                <td class="py-2 px-4 border">
                                    @isset($product->customer)
                                        {{ $product->customer->phone }}
                                    @endisset
                                </td>
                            @else
                                <td class="py-2 px-4 border" colspan="2" >Cash</td>$product
                            @endif

                            <td class="py-2 px-4 border">{{ $product->invoice_number }}</td>
                            <td class="py-2 px-4 border">@foreach($product->items as $item)
                        {{ $item->product->category->name ?? 'N/A' }}<br>@endforeach</td>
                            <td class="py-2 px-4 border">@foreach($product->items as $item)
                        {{ $item->product->brand->name ?? 'N/A' }}<br>@endforeach</td>
                            <td class="py-2 px-4 border">@foreach($product->items as $item)
                        {{ $item->product->model ?? '' }}<br>@endforeach</td>
                            <td class="py-2 px-4 border text-center">{{ $product->created_at->format('d/M/y') }}</td>
                            <td class="py-2 px-4 border text-right">{{ $product->total }}</td>
                            <td class="py-2 px-4 border">
                                @if ($product->gst == 'yes')
                                    Yes
                                @else
                                    No
                                @endif
                            </td>
                            <td class="flex justify-center gap-2">
                                <a title="Details" id="action"
                                    href="{{ route('sale.show', ['id' => $product->id]) }}"><button
                                        class="mt-1 bg-green-800 text-white px-3 py-1 rounded hover:bg-red-700"><i
                                            class="fa-solid fa-circle-info"></i></button></a>
                                <a title="Invoice" id="action"
                                    href="{{ route('sale.invoice', ['id' => $product->id]) }}"><button
                                        class="mt-1 bg-orange-800 text-white px-3 py-1 rounded hover:bg-green-700">
                                        <i class="fa-solid fa-file"></i></button></a>
                            </td>
                        </tr>
                        @php
                            $i++;
                        @endphp
                    @endforeach
                    @if ($i == 1)
                        <td class="text-red-600">No records found !</td>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="m-4">
        {{ $sales->links() }}
    </div>
@endsection
@push('extra_js')
@endpush
