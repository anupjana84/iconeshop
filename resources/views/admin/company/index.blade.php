@extends('layouts.main')

@push('page_title')
<title>{{ $page_title }}</title>
@endpush

@section('content_page')
<div class="flex  items-center space-x-2">
    <!-- Create Button -->
    <a href="{{ route('companies.create') }}">
        <button
            class="text-white bg-gradient-to-br from-green-400 to-blue-600 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-green-200 dark:focus:ring-green-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center shadow-lg shadow-green-500/50 dark:shadow-lg dark:shadow-green-800/80">
            Create New Company
        </button>
    </a>

    <!-- Search Form -->
    <form id="search-form" class="flex flex-1 items-center space-x-2">
        @csrf
        <!-- Search Input (fills remaining space) -->
        <input type="text" id="invoice_id" name="search" placeholder="Search using Company details"
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
        <a href="{{ route('companies.list') }}"
            class="text-gray-900 bg-gradient-to-r from-lime-200 via-lime-400 to-lime-500 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-lime-300 dark:focus:ring-lime-800 px-4 py-2 rounded">
            Reset
        </a>
    </form>
</div>
<div class="container mx-auto p-4">

    <h2 class="text-xl font-bold mb-4">{{ $page_title }}</h2>

    <table class="table-auto w-full border border-gray-400">
        <thead>
            <tr class="bg-gray-200 text-left">
                <th class="p-2 border">#</th>
                <th class="p-2 border">Name</th>
                <th class="p-2 border">Address</th>
                <th class="p-2 border">Phone</th>
                <th class="p-2 border">GST Number</th>
                <th class="p-2 border" colspan="2">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($companies as $company)
            <tr>
                <td class="p-2 border">{{ $loop->iteration }}</td>
                <td class="p-2 border">{{ $company->name }}</td>
                <td class="p-2 border">{{ $company->address }}</td>
                <td class="p-2 border">{{ $company->phone }}</td>
                <td class="p-2 border">{{ $company->gst_number }}</td>
                <td class="">
                    <a id="action" href="{{route('companies.edit',$company->id)}}" class="pointer"><button
                        class="bg-green-600 text-white px-3 py-1 mx-2 rounded hover:bg-green-700 pointer"><i
                            class="fa-solid fa-pen-to-square"></i></button>
                </a>
                </td>
                <td>
                    <form action="{{ route('companies.destroy', $company->id) }}" method="POST"
                        onsubmit="return confirmDelete(event, 'Delete Company', 'Are you sure you want to delete this company?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="bg-red-600 text-white px-3 py-1 mr-2 rounded hover:bg-red-700">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center p-3 text-gray-500">No companies found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-4">
        {{ $companies->links() }}
    </div>
</div>
@endsection
