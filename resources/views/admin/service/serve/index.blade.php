@php
    $page_title = 'Service Settings';
@endphp

@extends('layouts.main')

@section('content_page')
<div class="max-w-7xl mx-auto bg-white p-6 rounded-xl shadow-lg">
    <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-3 flex justify-between items-center">
        <span>🛠️ সার্ভিস সেটিং (Service Settings Hub)</span>
        <span class="text-xs font-normal bg-blue-100 text-blue-800 px-3 py-1 rounded-full">Icon Computer Management</span>
    </h2>

    <!-- 3 Main Options -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
        <div onclick="selectService('company')" id="btn-company" class="service-tab-btn cursor-pointer border-2 border-blue-500 bg-blue-50 hover:bg-blue-100 p-5 rounded-xl shadow-sm hover:shadow-md transition text-center">
            <div class="text-3xl text-blue-600 mb-2">📞</div>
            <h3 class="font-bold text-gray-800 text-lg mb-1">১) কোম্পানি কল বুকিং</h3>
            <p class="text-xs text-gray-600">অফিশিয়াল কোম্পানি কল বুকিং ও কেস আইডি ট্র্যাকিং</p>
        </div>

        <div onclick="selectService('external')" id="btn-external" class="service-tab-btn cursor-pointer border-2 border-orange-400 bg-orange-50 hover:bg-orange-100 p-5 rounded-xl shadow-sm hover:shadow-md transition text-center">
            <div class="text-3xl text-orange-500 mb-2">🚚</div>
            <h3 class="font-bold text-gray-800 text-lg mb-1">২) বাইরে থেকে সার্ভিস</h3>
            <p class="text-xs text-gray-600">থার্ডপার্টি/ভেন্ডর আউটসোর্স সার্ভিস ও ট্র্যাকিং</p>
        </div>

        <div onclick="selectService('inhouse')" id="btn-inhouse" class="service-tab-btn cursor-pointer border-2 border-green-500 bg-green-50 hover:bg-green-100 p-5 rounded-xl shadow-sm hover:shadow-md transition text-center">
            <div class="text-3xl text-green-600 mb-2">🛠️</div>
            <h3 class="font-bold text-gray-800 text-lg mb-1">৩) নিজের সার্ভিস</h3>
            <p class="text-xs text-gray-600">নিজস্ব শপ বা ইন-হাউস রিপেয়ারিং ম্যানেজমেন্ট</p>
        </div>
    </div>

    <!-- Dynamic Form & Container Area -->
    <div id="formArea" class="border-2 border-dashed border-gray-300 rounded-xl p-6 bg-gray-50 min-h-[250px]">
        <div class="text-center text-gray-500 font-medium my-10">
            👆 উপরে যেকোনো একটি সার্ভিস অপশন নির্বাচন করুন, সেটির ফর্ম এবং স্ট্যাটাস ড্যাশবোর্ড নিচে ওপেন হবে।
        </div>
    </div>

    <!-- Hidden Templates: each service form lives in its own partial file -->
    <div id="companyFormTemplate" class="hidden">
        @include('admin.service.serve._company-form')
    </div>

    <div id="externalFormTemplate" class="hidden">
        @include('admin.service.serve._external-form')
    </div>

    <div id="inhouseFormTemplate" class="hidden">
        @include('admin.service.serve._inhouse-form')
    </div>

    <!-- Universal Table & Global Search Section -->
    <div id="tableSectionArea" class="mt-8 hidden">
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 border-b pb-4 mb-4">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <span>📋 রেকর্ড ও ট্র্যাকিং তালিকা</span>
                    <span id="activeFilterBadge" class="text-xs bg-gray-200 text-gray-700 font-semibold px-2.5 py-0.5 rounded-full">সব দেখুন</span>
                </h3>

                <!-- Search Box -->
                <div class="w-full md:w-80 relative">
                    <input type="text" id="globalSearchInput" onkeyup="handleSearch(this.value)" placeholder="🔍 ফোন নম্বর, নাম বা প্রোডাক্ট দিয়ে খুঁজুন..." class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <span class="absolute left-3 top-2.5 text-gray-400 text-xs">🔍</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead id="mainTableHead">
                        <!-- Dynamic Header -->
                    </thead>
                    <tbody id="mainTableBody">
                        <!-- Dynamic Data Rows -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('admin.service.serve._print-modal')
@include('admin.service.serve._edit-modal')

<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>

<script>
    const COMPANY_STORE_URL  = @json(route('service-booking.store'));
    const COMPANY_LIST_URL   = @json(route('service-booking.bookings'));
    const CUSTOMER_BY_PHONE_URL = @json(route('customer.by.phone'));

    const INHOUSE_STORE_URL  = @json(route('inhouse.service.store'));
    const INHOUSE_LIST_URL   = @json(route('inhouse.service.list'));
    const INHOUSE_COUNTS_URL = @json(route('inhouse.service.counts'));

    const EXTERNAL_STORE_URL  = @json(route('external.service.store'));
    const EXTERNAL_LIST_URL   = @json(route('external.service.list'));
    const EXTERNAL_COUNTS_URL = @json(route('external.service.counts'));
</script>

<script src="{{ asset('js/service/company.js') }}"></script>
<script src="{{ asset('js/service/external.js') }}"></script>
<script src="{{ asset('js/service/inhouse.js') }}"></script>
<script src="{{ asset('js/service/print.js') }}"></script>
<script src="{{ asset('js/service/ocr.js') }}"></script>
<script src="{{ asset('js/service/common.js') }}"></script>
@endsection
