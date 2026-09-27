@extends('layouts.main')
@push('page_title')
    <title>Payment History</title>
@endpush
@section('content_page')
    {{-- search --}}
    <div class="flex justify-evenly ">
        <div class="flex items-center grow">
            <p>Date Range Search : </p>
        </div>
        <div class="flex grow">
            <!-- Date Range Search Form -->
            <form action="" method="GET" class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
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
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700">Method</label>
                        <select name="payMethod" id="payMethod" class="mt-1 p-2 border rounded w-full">
                            <option value="">-- Select Method --</option>
                            <option value="cash" {{ request('payMethod') == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="online" {{ request('payMethod') == 'online' ? 'selected' : '' }}>Online</option>
                        </select>
                    </div>
                    <div>
                        <label for="pay_type" class="block text-sm font-medium text-gray-700">Payment Type</label>
                        <select name="pay_type" id="pay_type" class="mt-1 p-2 border rounded w-full">
                            <option value="">-- Select Pay Type --</option>
                            <option value="inflow" {{ request('pay_type') == 'inflow' ? 'selected' : '' }}>Inflow</option>
                            <option value="outflow" {{ request('pay_type') == 'outflow' ? 'selected' : '' }}>outflow</option>
                        </select>
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
            <strong>Total Sale Amount:</strong> {{ $totalSaleAmount }}
        </div>
    @endif

    <hr class="border-gray-300 mb-4">
    <!-- Search Form -->
    <form id="search-form" class="mb-6">
        @csrf
        <div class="flex">
            <input type="search" id="invoice_id" name="search" placeholder="Search using Invoice ID"
                class="p-2 border rounded-l w-full" value="{{ request(key: 'search') }}">
            <button type="submit"
                class="text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 px-4 py-2 rounded-r hover:bg-blue-600">
                Search
            </button>
            <a href="{{ route('payment.history') }}"
                class="text-gray-900 bg-gradient-to-r from-lime-200 via-lime-400 to-lime-500 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-lime-300 dark:focus:ring-lime-800 rounded ml-2 px-4 py-2 rounded-r hover:bg-blue-600">
                Reset
            </a>
        </div>
    </form>

    <!-- Table Section -->
    <div>
        <h2 class="text-lg font-semibold mb-2">View Payment Master</h2> <!-- Subheading for Table -->
        <div class="bg-white p-4 rounded shadow overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="py-2 px-4 border">Sl</th>
                        <th class="py-2 px-4 border">Type</th>
                        <th class="py-2 px-4 border">Member</th>
                        <th class="py-2 px-4 border">Mobile No</th>
                        <th class="py-2 px-4 border">Amount</th>
                        <th class="py-2 px-4 border">Method</th>
                        <th class="py-2 px-4 border">Date</th>
                        <th class="py-2 px-4 border">Remark</th>
                        {{-- <th class="py-2 px-4 border">Details</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = 1;
                    @endphp
                    @foreach ($payments as $key => $item)
                        <tr class="border">
                            <td class="py-2 px-4 border">{{ $i }}</td>
                            <td class="py-2 px-4 border">
                                {{ $item->flow_type }}
                            </td>
                            @if (isset($item->expenses_id))
                                <td class="py-2 px-4 border" colspan="2">
                                    {{ $item->expenses->name }}
                                </td>
                            @else
                                
                            <td class="py-2 px-4 border">
                                @isset($item->company)
                                    {{ $item->company->name }}
                                @endisset
                                @isset($item->customer)
                                    {{ $item->customer->name }}
                                    @isset($item->emi_company_id )
                                        ( <small class="text-amber-700">{{ $item->emi->name }}</small> )
                                    @endisset
                                @endisset
                                @isset($item->user)
                                    {{ $item->user->name }}
                                @endisset
                            </td>
                            <td class="py-2 px-4 border">
                                @isset($item->company)
                                    {{ $item->company->phone }}
                                @endisset
                                @isset($item->customer)
                                    {{ $item->customer->phone }}
                                @endisset
                                @isset($item->user)
                                    {{ $item->user->phone }}
                                @endisset
                            </td>
                            @endif

                            <td class="py-2 px-4 border text-right">{{ $item->amount }}</td>
                            <td class="py-2 px-4 border">{{ $item->method }}</td>
                            <td class="py-2 px-4 border">
                                @isset($item->payment_date)
                                {{ $item->payment_date->format('d/M/y') }}
                                @endisset
                                </td>
                            <td class="py-2 px-4 border">
                                {{ $item->remark }}
                            </td>
                            {{-- <td class="flex justify-center ">
                                <a id="action" href="{{ route('purchase.show', ['id' => $item->id]) }}"><button
                                        class="mt-1 bg-green-800 text-white px-3 py-1 rounded hover:bg-red-700"><i
                                            class="fa-solid fa-circle-info"></i></button></a>
                            </td> --}}
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
        {{ $payments->links() }}
    </div>
@endsection
@push('extra_js')
@endpush
