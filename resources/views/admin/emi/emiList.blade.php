@extends('layouts.main')
@push('page_title')
    <title>Sale Master</title>
@endpush
@section('content_page')
    <!-- Table Section -->
    <div>
        <div class="mx-4 my-3 relative flex justify-between items-center gap-3">
            <form id="search-form" class="flex flex-1 items-center space-x-2" method="GET">
                <select name="input_finance" id="" class="bg-white p-2 border border-black rounded-sm min-w-40">
                    <option value="">--Finance--</option>
                    @foreach ($finances as $finance)
                        <option value="{{ $finance->id }}" {{ $finance->id == $input_finance ? 'selected' : '' }}>
                            {{ $finance->name }}</option>
                    @endforeach
                </select>
                <select name="status" id="" class="bg-white p-2 border border-black rounded-sm min-w-40">
                    <option value="">--Status--</option>
                    <option value="pending" {{ 'pending' == $status ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ 'completed' == $status ? 'selected' : '' }}>Completed</option>
                    
                </select>
                <!-- Search Input (fills remaining space) -->
                <input type="text" id="invoice_id" name="search" placeholder="Search using customer details"
                    class="p-2 border rounded flex-1 bg-white"
                    @isset($search)
                        value="{{ $search }}"
                    @endisset>

                <!-- Search Button -->
                <button type="submit"
                    class="text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 px-4 py-2 rounded">
                    Search
                </button>

                <!-- Reset Button -->
                <a href="{{ route('emi.list') }}"
                    class="text-gray-900 bg-gradient-to-r from-lime-200 via-lime-400 to-lime-500 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-lime-300 dark:focus:ring-lime-800 px-4 py-2 rounded">
                    Reset
                </a>
            </form>
        </div>
        <h2 class="text-lg font-semibold mb-2">View Sale Master</h2> <!-- Subheading for Table -->
        <div class="bg-white p-4 rounded shadow overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="py-2 px-4 border">Sl</th>
                        <th class="py-2 px-4 border">Customer Name</th>
                        <th class="py-2 px-4 border">Phone Number</th>
                        <th class="py-2 px-4 border">Emi Company</th>
                        <th class="py-2 px-4 border">Date</th>
                        <th class="py-2 px-4 border">Total Amount</th>
                        <th class="py-2 px-4 border">Finance Amount</th>
                        <th class="py-2 px-4 border">Down Payment</th>
                        <th class="py-2 px-4 border">Status</th>
                        <th class="py-2 px-4 border">Clear Date</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = 1;
                    @endphp
                    @foreach ($emis as $key => $item)
                        <tr class="border">
                            <td class="py-2 px-4 border">{{ $i }}</td>
                            <td class="py-2 px-4 border">
                                @isset($item->customer)
                                    {{ $item->customer->name }}
                                @endisset
                            </td>
                            <td class="py-2 px-2 border">{{ $item->customer->phone }}</td>
                            <td class="py-2 px-2 border">{{ $item->financeCompany->name }}</td>
                            <td class="py-2 px-2 border text-center">{{ $item->date }}</td>
                            <td class="py-2 px-2 border text-center">{{ $item->amount + $item->down_payment }}.00</td>
                            <td class="py-2 px-2 border">{{ $item->amount }}</td>
                            <td class="py-2 px-2 border text-right">{{ $item->down_payment }}</td>
                            <td class="py-2 px-2 border">
                                @if ($item->status == 'pending')
                                    <span
                                        class="bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full text-sm">pending</span>
                                @elseif($item->status == 'completed')
                                    <span class="bg-green-300 text-green-800 px-2 py-1 rounded-full text-sm">completed</span>
                                @else
                                    <span class="bg-gray-200 text-gray-800 px-2 py-1 rounded-full text-sm">Unknown</span>
                                @endif
                            </td>
                            <td class="py-2 px-4 border text-center">
                                @if (isset($item->emi_clear_date))
                                    {{ $item->emi_clear_date }}
                                @else
                                <a href="{{route("emi.pay",$item->id)}}">
                                    <button type="button"
                                                class="mt-1 bg-yellow-600 text-white px-3 py-1 rounded hover:bg-red-700 cursor-pointer"><i
                                                    class="fas fa-tasks"></i></button></a>
                                @endif
                            </td>

                        </tr>
                        @php
                            $i++;
                        @endphp
                    @endforeach
                </tbody>
                @if ($i == 1)
                    <td class="text-red-600">No records found !</td>
                @endif
            </table>
        </div>
    </div>
    <div class="m-4">
        {{ $emis->links() }}
    </div>
@endsection
