@extends('layouts.main')
@push('page_title')
    <title>Customer List</title>
@endpush
@section('content_page')
    <div>
        <div class="flex  items-center space-x-2 mb-6">
            <!-- Create Button -->
            <a href="{{ route('customers.create') }}">
                <button
                    class="text-white bg-gradient-to-br from-green-400 to-blue-600 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-green-200 dark:focus:ring-green-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center shadow-lg shadow-green-500/50 dark:shadow-lg dark:shadow-green-800/80">
                    Create New Customer
                </button>
            </a>
        
            <!-- Search Form -->
            <form id="search-form" class="flex flex-1 items-center space-x-2" method="GET">
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
                <a href="{{ route('customers.list') }}"
                    class="text-gray-900 bg-gradient-to-r from-lime-200 via-lime-400 to-lime-500 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-lime-300 dark:focus:ring-lime-800 px-4 py-2 rounded">
                    Reset
                </a>
            </form>
        </div>
       
        
        <div class="flex justify-between items-center mb-3">
    <h2 class="text-lg font-semibold">All Customers List</h2>

    <a href="{{ route('customers.export') }}"
       class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
        <i class="fa fa-download mr-1"></i> Download Excel
    </a>
</div> <!-- Subheading for Table -->
        <div class="bg-white p-4 rounded shadow overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="py-2 px-4 border">Sl</th>
                        <th class="py-2 px-4 border">Name</th>
                        <th class="py-2 px-4 border">Address</th>
                        <th class="py-2 px-4 border">Phone Number</th>
                        <th class="py-2 px-4 border">WhatsApp Number</th>
                        <th class="py-2 px-4 border">Pin</th>
                        <th class="py-2 px-4 border">Salesman</th>
                        <th class="py-2 px-4 border">Created at</th>
                        <th class="py-2 px-4 border" colspan="2">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = 1;
                    @endphp
                    @foreach ($customers as $key => $item)
                        <tr class="border border-gray-300">
                            <td class="py-2 px-4 border border-gray-300">{{ $i }}</td>
                            <td class="py-2 px-2 border border-gray-300">
                                {{ $item->name }}
                            </td>
                            <td class="py-2 px-4 border border-gray-300">{{ $item->address }} </td>
                            <td class="py-2 px-4 border border-gray-300 text-center">{{ $item->phone }} </td>
                            <td class="py-2 px-4 border border-gray-300 text-center">{{ $item->wpnumber }} </td>
                            <td class="py-2 px-4 border border-gray-300 text-center">{{ $item->pin }} </td>
                            {{-- <td class="py-2 px-4 border border-gray-300 text-right">
                                @isset($item->status)
                                @if ($item->status == 0)
                                    <span class="bg-red-200 text-red-800 px-2 py-1 rounded-full text-sm">Inactive</span>
                                @elseif($item->status == 1)
                                    <span
                                        class="bg-green-200 text-green-800 px-2 py-1 rounded-full text-sm">Active</span>
                                @else
                                    <span
                                        class="bg-gray-200 text-gray-800 px-2 py-1 rounded-full text-sm">Unknown</span>
                                @endif
                            @endisset
                            </td> --}}
                            <td class="py-2 px-4 border border-gray-300">
                                @isset($item->salesman_id)
                                    {{ optional($item->salesman)->name ?? '' }}
                                @endisset
                            </td>
                            <td class="py-2 px-4 border border-gray-300 text-center">
                                {{ $item->created_at ? $item->created_at->format('d-M-y h:i A') : '-' }} </td>
                            <td>
                                <a id="action" href="{{route('customers.edit',$item->id)}}" class="pointer"><button
                                        class="bg-green-600 text-white px-3 py-1 mx-2 rounded hover:bg-green-700 pointer"><i
                                            class="fa-solid fa-pen-to-square"></i></button>
                                </a>
                            </td>
                            <td>
                                <form action="{{ route('customers.remove', $item->id) }}" method="POST"
                                    onsubmit="return confirmDelete(event, 'Delete Customer', 'Are you sure you want to delete this customer?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="bg-red-600 text-white px-3 py-1 mr-2 rounded hover:bg-red-700">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                                {{-- <a id="action" href=""><button
                                        class="bg-red-600 text-white px-3 py-1 mr-2 rounded hover:bg-red-700"><i class="fa-solid fa-trash"></i></button></a> --}}
                            </td>

                        </tr>
                        @php
                            $i++;
                        @endphp
                    @endforeach
                </tbody>
            </table>
            @if ($i == 1)
                <div class="text-red-600 text-center">No product found</div>
            @endif
        </div>
        <div class="py-4">
            {{ $customers->links() }}
        </div>
    </div>
@endsection
@push('extra_style')
@endpush
@push('extra_js')
@endpush
