@extends('layouts.main')
@push('page_title')
    <title>Ladger</title>
@endpush
@section('content_page')
    <div>
        <div class="flex  items-center space-x-2">
            <!-- Create Button -->
            <a href="{{ route('brand.create') }}">
                <button
                    class="text-white bg-gradient-to-br from-green-400 to-blue-600 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-green-200 dark:focus:ring-green-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center shadow-lg shadow-green-500/50 dark:shadow-lg dark:shadow-green-800/80">
                    Create New Brand
                </button>
            </a>

            <!-- Search Form -->
            <form id="search-form" method="GET" class="flex flex-1 items-center space-x-2">
                <!-- Search Input (fills remaining space) -->
                <input type="text" id="invoice_id" name="search" placeholder="Search using customer details"
                    class="p-2 border rounded flex-1 bg-white"
                    value="{{ request(key: 'search') }}">

                <!-- Search Button -->
                <button type="submit"
                    class="text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 px-4 py-2 rounded">
                    Search
                </button>

                <!-- Reset Button -->
                <a href="{{ route('brand.list') }}"
                    class="text-gray-900 bg-gradient-to-r from-lime-200 via-lime-400 to-lime-500 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-lime-300 dark:focus:ring-lime-800 px-4 py-2 rounded">
                    Reset
                </a>
            </form>
        </div>
        <div class="mx-4 my-3 relative flex justify-between">
            <div>
                <h2 class="text-lg font-semibold mb-2">All Ladger Entries </h2>
            </div>
        </div>
        <!-- Subheading for Table -->
        <div class="bg-white p-4 rounded shadow overflow-visible">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="py-2 px-4 border">Sl</th>
                        <th class="py-2 px-4 border">Name</th>
                        <th class="py-2 px-4 border">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = 1;
                    @endphp
                    @foreach ($brands as $key => $item)
                        <tr class="border border-gray-300">
                            <td class="py-2 px-4 border border-gray-300">{{ $i }}</td>

                            <td class="py-2 px-4 border border-gray-300">{{ $item->name }}</td>

                            <td class="flex justify-center ">
                                <a id="action" href="{{ route('brand.edit', ['id' => $item->id]) }}"><button
                                        class="mt-1 bg-green-800 text-white px-3 py-1 rounded hover:bg-red-700">
                                        <i class="fa-solid fa-pen-to-square"></i></button></a>
                            </td>
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
            {{ $brands->links() }}
        </div>
    </div>
@endsection
@push('extra_style')
@endpush
@push('extra_js')
@endpush
