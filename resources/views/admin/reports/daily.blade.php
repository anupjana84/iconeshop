@extends('layouts.main')
@push('page_title')
    <title>Daily Report</title>
@endpush
@section('content_page')
    <div>
        {{-- ///////////////////////////////////////////// --}}
        <div class="flex justify-evenly">
            <div class="flex items-center grow">
                <p>Date Range Search :</p>
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
                            <label for="end_date" class="block text-sm font-medium text-gray-700">End Date</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}"
                                class="mt-1 p-2 border rounded w-full bg-white">
                        </div>
                        <!-- Search Button -->
                        <div class="flex items-end">
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                                Search
                            </button>
                        </div>
                        <!-- Reset Button -->
                        <div class="flex items-end">
                            <a href="{{ route('reports.daily') }}">
                                <button type="button" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-800">
                                    Reset
                                </button>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ///////////////////////////////////////////// --}}
        <div class="w-full flex text-xl justify-between">
        <div class="bg-white px-4 rounded-sm py-2">
            <p><strong>Total Net profit : </strong> {{number_format($total_net_profit,2)}}</p>
        </div>

        <div class=" flex justify-end text-xl">
            <p class="bg-white px-3 py-2 rounded-sm"><strong>From:</strong> {{ $startDate->format('d M Y') }} <strong>To:</strong>
                {{ $endDate->format('d M Y') }}</p>
            </div>
            </div>

        <div class="bg-white p-4 rounded shadow overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="py-2 px-4 border">Date</th>
                        <th class="py-2 px-4 border">Purchase(sale)</th>
                        <th class="py-2 px-4 border">Sale</th>
                        <th class="py-2 px-4 border">Sale Profit</th>
                        <th class="py-2 px-4 border">Expenses</th>
                        <th class="py-2 px-4 border">Points</th>
                        <th class="py-2 px-4 border">Cash In</th>
                        <th class="py-2 px-4 border">Online In</th>
                        <th class="py-2 px-4 border">Net Profit</th>
                        <th class="py-2 px-4 border">View</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($dailyData as $row)
                        <tr class="border">
                            <td class="py-2 px-4 border text-center">{{ $row['day_label'] }}</td>
                            <td class="py-2 px-4 border text-right">{{ number_format($row['sale_purchase'], 2) }}</td>
                            <td class="py-2 px-4 border text-right">{{ number_format($row['sale'], 2) }}</td>
                            <td class="py-2 px-4 border text-right">{{ number_format($row['sale_profit'], 2) }}</td>
                            <td class="py-2 px-4 border text-right">{{ number_format($row['expenses'], 2) }}</td>
                            <td class="py-2 px-4 border text-right">{{ number_format($row['points'], 2) }}</td>
                            <td class="py-2 px-4 border text-right">{{ number_format($row['cash_in'], 2) }}</td>
                            <td class="py-2 px-4 border text-right">{{ number_format($row['online_in'], 2) }}</td>
                            <td class="py-2 px-4 border text-right">{{ number_format($row['net_profit'], 2) }}</td>
                            <td class="py-2 px-1 border text-center">
                                <a href="{{ route('sales.byDate', ['date' => $row['day_label']]) }}" class="bg-blue-600 rounded-sm px-3 py-1 text-white text-bold">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
