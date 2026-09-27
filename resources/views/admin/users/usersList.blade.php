@extends('layouts.main')
@push('page_title')
    <title>User List</title>
@endpush
@section('content_page')
    <div>
        <div class="mx-4 my-3 relative flex justify-between items-center gap-3">
            <div>
                <a href="{{ route('user.create') }}"><button
                        class="text-white bg-gradient-to-br from-green-400 to-blue-600 hover:bg-gradient-to-bl focus:ring-4 focus:outline-none focus:ring-green-200 dark:focus:ring-green-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 shadow-lg shadow-green-500/50 dark:shadow-lg dark:shadow-green-800/80">Create
                        New User</button></a>
            </div>
            <form id="search-form" class="flex flex-1 items-center space-x-2" action="{{ route('users.list') }}" method="GET">
                @csrf
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
                <a href="{{ route('users.list') }}"
                    class="text-gray-900 bg-gradient-to-r from-lime-200 via-lime-400 to-lime-500 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-lime-300 dark:focus:ring-lime-800 px-4 py-2 rounded">
                    Reset
                </a>
            </form>
            <div class="relative">
                <button id="dropdownDividerButton" data-dropdown-toggle="dropdownDivider"
                    class="text-white bg-gray-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                    type="button">Filter User<svg class="w-2.5 h-2.5 ms-3" aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 4 4 4-4" />
                    </svg>
                </button>
                <!-- Dropdown menu -->
                <div id="dropdownDivider"
                    class="absolute z-50 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-44 dark:bg-gray-700 dark:divide-gray-600">
                    <ul class="py-2  dark:text-gray-200" aria-labelledby="dropdownDividerButton">
                        <li>
                            <a href="{{ route('users.list') }}"
                                class="block px-4 py-2 hover:bg-gray-300 dark:hover:bg-gray-600 dark:hover:text-white">All</a>
                        </li>
                        <li>
                            <a href="{{ route('managers.list') }}"
                                class="block px-4 py-2 hover:bg-gray-300 dark:hover:bg-gray-600 dark:hover:text-white">Manager</a>
                        </li>
                        <li>
                            <a href="{{ route('subdealers.list') }}"
                                class="block px-4 py-2 hover:bg-gray-300 dark:hover:bg-gray-600 dark:hover:text-white">Sub
                                Dealers</a>
                        </li>
                        <li>
                            <a href="{{ route('salesmans.list') }}"
                                class="block px-4 py-2 hover:bg-gray-300 dark:hover:bg-gray-600 dark:hover:text-white">Salesmans</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <h2 class="text-lg font-semibold mb-2">User List </h2> <!-- Subheading for Table -->
        <div class="bg-white p-4 rounded shadow overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="py-2 px-4 border">Sl</th>
                        <th class="py-2 px-4 border">Name</th>
                        <th class="py-2 px-4 border">Phone Number</th>
                        <th class="py-2 px-4 border">Whatsapp Number</th>
                        <th class="py-2 px-4 border">Role</th>
                        <th class="py-2 px-4 border">Status</th>
                        <th class="py-2 px-4 border">Created At</th>
                        <th class="py-2 px-4 border text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = 1;
                    @endphp
                    @foreach ($users as $key => $item)
                        <tr class="border hover:bg-gray-50">
                            <td class="py-2 px-4 border text-center">{{ $i }}</td>
                            <td class="py-2 px-4 border font-medium">{{ $item->name }}</td>
                            <td class="py-2 px-4 border">{{ $item->phone }}</td>
                            <td class="py-2 px-4 border">{{ $item->wpnumber }}</td>
                            <td class="py-2 px-4 border text-center">
                                @if ($item->role == 'salesman')
                                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-semibold">Salesman</span>
                                @elseif($item->role == 'manager')
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-semibold">Manager</span>
                                @elseif($item->role == 'subdealer')
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-semibold">Sub Dealer</span>
                                @elseif($item->role == 'admin')
                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-semibold">Admin</span>
                                @else
                                    <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-full text-xs">Unknown</span>
                                @endif
                            </td>
                            <td class="py-2 px-4 border text-center">
                                @if ($item->status == 'active')
                                    <span class="text-green-600 font-semibold bg-green-50 px-2 py-1 rounded">Active</span>
                                @else
                                    <span class="text-red-600 font-semibold bg-red-50 px-2 py-1 rounded">Inactive</span>
                                @endif
                            </td>
                            <td class="py-2 px-4 border text-center text-sm text-gray-600">
                                {{ $item->created_at ? $item->created_at->format('d/m/Y') : '-' }}
                            </td>
                            <td class="py-2 px-4 border text-center">
                                <div class="flex justify-center items-center gap-2">
                                    {{-- View --}}
                                    <a href="{{ route('user.show', $item->id) }}" class="text-blue-600 hover:text-blue-800" title="View Details">
                                        <i class="fa-solid fa-circle-info text-xl"></i>
                                    </a>

                                    {{-- Edit --}}
                                    <a href="{{ route('user.edit', $item->id) }}">
                                        <button class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 transition shadow-sm" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                    </a>

                                    {{-- Delete --}}
                                    <form action="{{ route('user.delete', $item->id) }}" method="POST" onsubmit="return confirmDelete(event, 'Delete User', 'Are you sure you want to delete this user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 transition shadow-sm" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @php
                            $i++;
                        @endphp
                    @endforeach
                </tbody>
            </table>
            @if ($i == 1)
                <div class="text-red-600 text-center">No user found</div>
            @endif
        </div>
        <div class="py-4">
            {{ $users->links() }}
        </div>
    </div>
@endsection
@push('extra_style')
@endpush
@push('extra_js')
@endpush
