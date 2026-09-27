@extends('layouts.main')
@push('page_title')
    <title>GST Return Reports</title>
@endpush
@section('content_page')
    <div class="px-5 py-6">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">GST Return Reports</h2>

        {{-- Success Message --}}
        @if (session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
                <span class="font-medium">Success!</span> {{ session('success') }}
            </div>
        @endif

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Generate Report Form --}}
            <div class="md:col-span-1 bg-white p-6 rounded-lg shadow-md h-fit">
                <h3 class="text-lg font-semibold mb-4 text-gray-700 border-b pb-2">Generate New Report</h3>
                <form action="{{ route('gst.report.generate') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="from_date" class="block mb-2 text-sm font-medium text-gray-900">From Date</label>
                        <input type="date" id="from_date" name="from_date" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                    <div class="mb-4">
                        <label for="to_date" class="block mb-2 text-sm font-medium text-gray-900">To Date</label>
                        <input type="date" id="to_date" name="to_date" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                    <div class="mb-4">
                        <label for="gst_type" class="block mb-2 text-sm font-medium text-gray-900">Report Type / GST Filter</label>
                        <select id="gst_type" name="gst_type" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option value="yes">With GST (GST Included Sales)</option>
                            <option value="no">Without GST (No GST Sales)</option>
                            <option value="all">All Sales (Both)</option>
                        </select>
                    </div>
                    <button type="submit"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 w-full">
                        Generate Excel Report
                    </button>
                </form>
            </div>

            {{-- History Table --}}
            <div class="md:col-span-2 bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold mb-4 text-gray-700 border-b pb-2">Report History</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3">Date Range</th>
                                <th scope="col" class="px-6 py-3">Type</th>
                                <th scope="col" class="px-6 py-3">Generated On</th>
                                <th scope="col" class="px-6 py-3 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($reports as $report)
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($report->from_date)->format('d M Y') }} - 
                                        {{ \Carbon\Carbon::parse($report->to_date)->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if (str_contains($report->file_path, 'With_GST'))
                                            <span class="px-2.5 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">With GST</span>
                                        @elseif (str_contains($report->file_path, 'No_GST'))
                                            <span class="px-2.5 py-1 text-xs font-semibold bg-yellow-100 text-yellow-800 rounded-full">No GST</span>
                                        @else
                                            <span class="px-2.5 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full">All Sales</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $report->created_at->format('d M Y h:i A') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('gst.report.download', $report->id) }}"
                                            class="font-medium text-blue-600 hover:underline">
                                            <i class="fa-solid fa-file-excel mr-1"></i> Download
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                        No reports generated yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
