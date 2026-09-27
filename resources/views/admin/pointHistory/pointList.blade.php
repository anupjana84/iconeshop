@extends('layouts.main')
@push('page_title')
    <title>Point History</title>
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
    @if (isset($totalPoints))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            <strong>Total Sale Amount:</strong> {{ $totalPoints }}
        </div>
    @endif

    <hr class="border-gray-300 mb-4">
    <!-- Search Form -->
    <form id="search-form" class="mb-6">
        @csrf
        <div class="flex">
            <input type="search" id="invoice_id" name="search" placeholder="Search using User"
                class="p-2 border rounded-l w-full"
                @isset($search)
            value="{{ $search }}"
            @endisset>
            <button type="submit"
                class="text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 px-4 py-2 rounded-r hover:bg-blue-600">
                Search
            </button>
            <a href="{{ route('point.history') }}"
                class="text-gray-900 bg-gradient-to-r from-lime-200 via-lime-400 to-lime-500 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-lime-300 dark:focus:ring-lime-800 rounded ml-2 px-4 py-2 rounded-r hover:bg-blue-600">
                Reset
            </a>
        </div>
    </form>

    <!-- Table Section -->
    <div>
        <h2 class="text-lg font-semibold mb-2">View User Points</h2> <!-- Subheading for Table -->
        <div class="bg-white p-4 rounded shadow overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="py-2 px-4 border">Sl</th>
                        <th class="py-2 px-4 border">User Name</th>
                        <th class="py-2 px-4 border">Mobile no</th>
                        <th class="py-2 px-4 border">User Role</th>
                        <th class="py-2 px-4 border">Total Amount</th>
                        <th class="py-2 px-4 border">Created At</th>
                        <th class="py-2 px-4 border">Sales</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = 1;
                    @endphp
                    @foreach ($history as $key => $item)
                        <tr class="border">
                            <td class="py-2 px-4 border">{{ $i }}</td>
                            <td class="py-2 px-4 border">{{ $item->user->name }}</td>
                            <td class="py-2 px-4 border">{{ $item->user->phone }}</td>
                            <td class="py-2 px-4 border">{{ $item->user->role }}</td>
                            <td class="py-2 px-4 border text-right">{{ $item->points }}</td>
                            <td class="py-2 px-4 border text-center">{{ $item->date }}</td>
                            <td class="flex justify-center gap-2">
                                <a title="Details" id="action"
                                    href="{{ route('sale.show', ['id' => $item->sales_id]) }}"><button
                                        class="mt-1 bg-green-800 text-white px-3 py-1 rounded hover:bg-red-700"><i
                                            class="fa-solid fa-circle-info"></i></button>
                                </a>

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
        {{ $history->links() }}
    </div>
@endsection
@push('extra_js')
@endpush
