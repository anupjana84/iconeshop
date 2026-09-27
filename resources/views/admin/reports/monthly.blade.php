@extends('layouts.main')
@push('page_title')
    <title>Monthly Report</title>
@endpush
@section('content_page')
    <div>
        <div class="flex justify-evenly ">
            <div class="flex items-center grow">
                <p>Date Range Search : </p>
            </div>
            <div class="flex grow">
                <!-- Date Range Search Form -->
                <form method="GET" class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Start Date -->
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">Start Date</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}"
                                class="mt-1 p-2 border rounded w-full bg-white">
                        </div>
                        <!-- End Date -->
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700">End Date</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}"
                                class="mt-1 p-2 border rounded w-full bg-white">
                        </div>
                        <!-- Submit Button -->
                        <div class="flex items-end">
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                Search
                            </button>
                        </div>
                        <div class="flex items-end">
                            <a href="{{ route('reports.monthly') }}">
                                <button type="button" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-800">
                                    Reset
                                </button></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ////////////////////////////////////////////////// --}}

        {{-- ////////////////////////////////////////////////// --}}

        <p><strong>From:</strong> {{ $startDate->format('d M Y') }} <strong>To:</strong>
            {{ $endDate->format('d M Y') }}</p>

        <div class="bg-white p-4 rounded shadow overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="py-2 px-4 border">Month</th>
                        <th class="py-2 px-4 border">Purchase</th>
                        <th class="py-2 px-4 border">Sale</th>
                        <th class="py-2 px-4 border">Expenses</th>
                        <th class="py-2 px-4 border">Points</th>
                        <th class="py-2 px-4 border">Payment In</th>
                        <th class="py-2 px-4 border">Payment Out</th>
                        <th class="py-2 px-4 border">Online Payment</th>
                        <th class="py-2 px-4 border">Cash Payment</th>
                        <th class="py-2 px-4 border">Profit</th>
                        <th class="py-2 px-4 border">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($monthlyData as $row)
                        <tr class="border">
                            <td class="py-2 px-4 border">{{ $row['month_label'] }}</td>
                            <td class="py-2 px-4 border">{{ number_format($row['purchase'], 2) }}</td>
                            <td class="py-2 px-4 border">{{ number_format($row['sale'], 2) }}</td>
                            <td class="py-2 px-4 border">{{ number_format($row['expenses'], 2) }}</td>
                            <td class="py-2 px-4 border">{{ number_format($row['points'], 2) }}</td>
                            <td class="py-2 px-4 border">{{ number_format($row['payment_in'], 2) }}</td>
                            <td class="py-2 px-4 border">{{ number_format($row['payment_out'], 2) }}</td>
                            <td class="py-2 px-4 border">{{ number_format($row['online_out'], 2) }}</td>
                            <td class="py-2 px-4 border">{{ number_format($row['cash_out'], 2) }}</td>
                            <td class="py-2 px-4 border">{{ number_format($row['profit'], 2) }}</td>
                            <td class="py-2 px-4 border"><a href="#" class="btn btn-sm btn-info">View</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
