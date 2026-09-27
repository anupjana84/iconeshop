@extends('layouts.main')
@push('page_title')
    <title>Service Call</title>
@endpush
@section('content_page')
    @php
        $routes = ['service/call/logs', 'service/call/pending', 'service/call/completed', 'service/call/canceled'];
    @endphp
    <div class="mx-4 my-3 relative flex justify-between gap-2 items-center">
        {{-- search --}}
        <form id="search-form" class="flex flex-1 items-center space-x-2" action="{{ route('service.call.logs') }}" method="GET">
            <input type="search" id="invoice_id" name="search" placeholder="Search using details"
                class="p-2 border flex-1 rounded-l w-full bg-white"
                @isset($search)
            value="{{ $search }}"
            @endisset>
            <button type="submit"
                class="text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 px-4 py-2 rounded-r hover:bg-blue-600">
                Search
            </button>
            <a href="{{ route('service.call.logs') }}"
                class="text-gray-900 bg-gradient-to-r from-lime-200 via-lime-400 to-lime-500 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-lime-300 dark:focus:ring-lime-800 rounded ml-2 px-4 py-2 rounded-r hover:bg-blue-600">
                Reset
            </a>
        </form>
        <div class="relative">
            <button id="dropdownDividerButton" data-dropdown-toggle="dropdownDivider"
                class="text-white bg-gray-700 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800"
                type="button">
                @if (request()->is('service/call/logs'))
                    All
                @elseif(request()->is('service/call/pending'))
                    Pending
                @elseif(request()->is('service/call/completed'))
                    Completed
                @elseif(request()->is('service/call/canceled'))
                    Canceled
                @else
                    Dropdown
                @endif
                <svg class="w-2.5 h-2.5 ms-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 10 6">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="m1 1 4 4 4-4" />
                </svg>
            </button>
            <!-- Dropdown menu -->
            <div id="dropdownDivider"
                class="absolute z-50 hidden bg-white divide-y divide-gray-100 rounded-lg shadow-sm w-44 dark:bg-gray-700 dark:divide-gray-600">
                <ul class="py-2  dark:text-gray-200" aria-labelledby="dropdownDividerButton">
                    <li>
                        <a href="{{ route('service.call.logs') }}"
                            class="block px-2 py-2 hover:bg-gray-300 dark:hover:bg-gray-600 dark:hover:text-white">All</a>
                    </li>
                    <li>
                        <a href="{{ route('service.call.pending') }}"
                            class="block px-2 py-2 hover:bg-gray-300 dark:hover:bg-gray-600 dark:hover:text-white">Pending</a>
                    </li>
                    <li>
                        <a href="{{ route('service.call.completed') }}"
                            class="block px-2 py-2 hover:bg-gray-300 dark:hover:bg-gray-600 dark:hover:text-white">Complete</a>
                    </li>
                    <li>
                        <a href="{{ route('service.call.canceled') }}"
                            class="block px-2 py-2 hover:bg-gray-300 dark:hover:bg-gray-600 dark:hover:text-white">Canceled</a>
                    </li>
                </ul>

            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div>
        <h2 class="text-lg font-semibold mb-2">Services</h2> <!-- Subheading for Table -->
        <div class="bg-white p-4 rounded shadow overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="py-2 px-4 border">Sl</th>
                        <th class="py-2 px-4 border"> Name</th>
                        <th class="py-2 px-4 border">Mobile no</th>
                        <th class="py-2 px-4 border">address</th>
                        <th class="py-2 px-4 border">Note</th>
                        <th class="py-2 px-4 border">Invoice image</th>
                        <th class="py-2 px-4 border">Apply date</th>
                        <th class="py-2 px-4 border">Case Id</th>
                        <th class="py-2 px-4 border">Status</th>
                        <th class="py-2 px-4 border">Uplode Image</th>
                        <th class="py-2 px-1 border">Call Details</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = 1;
                    @endphp
                    @foreach ($services as $key => $item)
                        <tr class="border">
                            <td class="py-2 px-4 border">{{ $i }}</td>
                            <td class="py-2 px-4 border">{{ $item->name }}</td>
                            <td class="py-2 px-4 border text-center">{{ $item->phone }}</td>
                            <td class="py-2 px-4 border text-right">{{ $item->address }}</td>
                            <td class="py-2 px-4 border text-right">{{ $item->note }}</td>
                            @php
                                // Decode the JSON string into an array
                                $images = json_decode($item->invoice_image, true);
                            @endphp

                            @if (!empty($images) && isset($images[0]))
                                <td>
                                    <a href="{{ $images[0] }}" target="_blank">
                                        <img src="{{ $images[0] }}" alt="Invoice Image" width="100" height="100"
                                            style="object-fit: cover; border-radius: 5px;">
                                    </a>
                                </td>
                            @else
                                <td>
                                    <img src="{{ asset('images/no-image.png') }}" alt="No Image" width="70"
                                        height="70" style="object-fit: cover; border-radius: 5px;">
                                </td>
                            @endif


                            <td class="py-2 px-4 border text-right">{{ $item->created_at->format('d-M-y') }}</td>
                            <td class="py-2 px-4 border text-right">
                                @isset($item->remark->case_id)
                                     {{ $item->remark->case_id }}
                                @endisset
                            </td>
                            <td class="py-2 px-4 border text-center">
                                @php
                                    $colors = [
                                        'Pending' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                        'Completed' => 'bg-green-100 text-green-800 border-green-300',
                                        'Canceled' => 'bg-red-100 text-red-800 border-red-300',
                                    ];
                                    $style = $colors[$item->status] ?? 'bg-gray-100 text-gray-700 border-gray-300';
                                @endphp

                                <span class="px-3 py-1 rounded-full text-sm font-semibold border {{ $style }}">
                                    {{ $item->status }}
                                </span>
                                @isset($item->call_id)
                                    <div class="mt-2">
                                        {{ \Carbon\Carbon::parse($item->call_date)->format('d-M-y') }}
                                    </div>
                                @endisset

                            </td>

                            <td class="border">
                                @isset($item->remark->image)
                                <a href="{{ asset('storage/' . $item->remark->image) }}" target="_blank">
                                <img src="{{ asset('storage/' . $item->remark->image) }}" alt="No Image" width="70"
                                    height="70" style="object-fit: cover; border-radius: 5px;"></a>
                                @endisset

                            </td>

                            <td class="flex mt-3 justify-center">
                                @if (isset($item->call_id))
                                    <div class="flex gap-3">
                                        <a id="action" href="{{ route('service-status.edit', $item->call_id) }}"><button
                                                class="bg-green-600 text-white px-3 py-1 mx-2 rounded hover:bg-green-700"><i
                                                    class="fa-solid fa-pen-to-square"></i></button>
                                        </a>
                                        <a id="action" href="{{ route('service.send.wp', $item->id) }}"><button
                                                class="bg-green-600 text-white px-3 py-1 mx-2 rounded hover:bg-green-700">
                                                <i class="fa-brands fa-whatsapp"></i></button>
                                        </a>
                                    </div>
                                @else
                                    <a id="action" href="{{ route('service-status.create', $item->id) }} "><button
                                            class="bg-green-800 text-white px-3 py-1 ml-2 rounded hover:bg-red-400"><i
                                                class="fa-solid fa-circle-info"></i></button>
                                    </a>
                                @endif

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
        {{ $services->links() }}
    </div>
@endsection
@push('extra_js')
@endpush
