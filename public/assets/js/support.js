@php
    $page_title = 'Service Settings';
@endphp

@extends('layouts.main')

@section('content_page')

<div class="max-w-7xl mx-auto bg-white p-6 rounded-xl shadow-lg">

    <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-3 flex justify-between items-center">
        <span>🛠️ সার্ভিস সেটিং (Service Settings Hub)</span>

        <span class="text-xs font-normal bg-blue-100 text-blue-800 px-3 py-1 rounded-full">
            Icon Computer Management
        </span>
    </h2>

    {{-- Service Tabs --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">

        <div
            onclick="selectService('company')"
            id="btn-company"
            class="service-tab-btn cursor-pointer border-2 border-blue-500 bg-blue-50 hover:bg-blue-100 p-5 rounded-xl shadow-sm hover:shadow-md transition text-center"
        >
            <div class="text-3xl text-blue-600 mb-2">📞</div>

            <h3 class="font-bold text-gray-800 text-lg mb-1">
                ১) কোম্পানি কল বুকিং
            </h3>

            <p class="text-xs text-gray-600">
                অফিসিয়াল কোম্পানি কল বুকিং ও কেস আইডি ট্র্যাকিং
            </p>
        </div>


        <div
            onclick="selectService('external')"
            id="btn-external"
            class="service-tab-btn cursor-pointer border-2 border-orange-400 bg-orange-50 hover:bg-orange-100 p-5 rounded-xl shadow-sm hover:shadow-md transition text-center"
        >
            <div class="text-3xl text-orange-500 mb-2">🚚</div>

            <h3 class="font-bold text-gray-800 text-lg mb-1">
                ২) বাইরে থেকে সার্ভিস
            </h3>

            <p class="text-xs text-gray-600">
                থার্ডপার্টি / ভেন্ডর আউটসোর্স সার্ভিস
            </p>
        </div>


        <div
            onclick="selectService('inhouse')"
            id="btn-inhouse"
            class="service-tab-btn cursor-pointer border-2 border-green-500 bg-green-50 hover:bg-green-100 p-5 rounded-xl shadow-sm hover:shadow-md transition text-center"
        >
            <div class="text-3xl text-green-600 mb-2">🛠️</div>

            <h3 class="font-bold text-gray-800 text-lg mb-1">
                ৩) নিজের সার্ভিস
            </h3>

            <p class="text-xs text-gray-600">
                নিজস্ব শপ বা ইন-হাউস রিপেয়ারিং ম্যানেজমেন্ট
            </p>
        </div>

    </div>


    {{-- Dynamic Form Area --}}
    <div
        id="formArea"
        class="border-2 border-dashed border-gray-300 rounded-xl p-6 bg-gray-50 min-h-[250px]"
    >
        <div class="text-center text-gray-500 font-medium my-10">
            👆 উপরে যেকোনো একটি সার্ভিস অপশন নির্বাচন করুন।
        </div>
    </div>


    {{-- Company Template --}}
    @include('support.company-form')


    {{-- External Template --}}
    @include('support.external-form')


    {{-- In-house Template --}}
    @include('support.inhouse-form')


    {{-- Universal Table --}}
    <div id="tableSectionArea" class="mt-8 hidden">

        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">

            <div class="flex flex-col md:flex-row justify-between items-center gap-4 border-b pb-4 mb-4">

                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">

                    <span>📋 রেকর্ড ও ট্র্যাকিং তালিকা</span>

                    <span
                        id="activeFilterBadge"
                        class="text-xs bg-gray-200 text-gray-700 font-semibold px-2.5 py-0.5 rounded-full"
                    >
                        সব দেখুন
                    </span>

                </h3>


                <div class="w-full md:w-80 relative">

                    <input
                        type="text"
                        id="globalSearchInput"
                        oninput="handleSearch(this.value)"
                        placeholder="🔍 ফোন নম্বর, নাম বা প্রোডাক্ট দিয়ে খুঁজুন..."
                        class="w-full border border-gray-300 rounded-lg pl-9 pr-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none"
                    >

                    <span class="absolute left-3 top-2.5 text-gray-400 text-xs">
                        🔍
                    </span>

                </div>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full text-left border-collapse text-sm">

                    <thead id="mainTableHead"></thead>

                    <tbody id="mainTableBody"></tbody>

                </table>

            </div>

        </div>

    </div>

</div>


{{-- Print Receipt Modal --}}
<div
    id="printModal"
    class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center p-4 z-50"
>

    <div class="bg-white w-full max-w-lg rounded-xl shadow-2xl p-6 relative">

        <button
            onclick="closePrintModal()"
            class="absolute top-3 right-3 text-gray-500 hover:text-black font-bold text-xl"
        >
            &times;
        </button>


        <div
            id="printableReceipt"
            class="p-4 border rounded-lg bg-white text-black font-sans"
        >

            <div class="text-center border-b pb-3 mb-3">

                <h2 class="text-xl font-bold tracking-wide">
                    ICON COMPUTER
                </h2>

                <p class="text-xs">
                    Bethuadahari, Nadia | Ph: 8597753337
                </p>

                <p class="text-xs font-semibold text-gray-600 mt-1">
                    SERVICE & REPAIR RECEIPT
                </p>

            </div>


            <div class="text-xs space-y-1.5 mb-4">

                <div class="flex justify-between">

                    <span>
                        <strong>Receipt No:</strong>
                        <span id="pr_id"></span>
                    </span>

                    <span>
                        <strong>Date:</strong>
                        <span id="pr_date"></span>
                    </span>

                </div>


                <p>
                    <strong>Customer Name:</strong>
                    <span id="pr_name"></span>
                </p>


                <p>
                    <strong>Phone:</strong>
                    <span id="pr_phone"></span>
                </p>


                <p>
                    <strong>Address:</strong>
                    <span id="pr_address"></span>
                </p>


                <p>
                    <strong>Product/Issue:</strong>
                    <span id="pr_product"></span>
                </p>


                <p>
                    <strong>Serial No:</strong>
                    <span id="pr_serial"></span>
                </p>


                <div class="flex justify-between pt-2 border-t font-bold text-sm">

                    <span>
                        Final Amount / Est:
                    </span>

                    <span>
                        ₹<span id="pr_amount"></span>
                    </span>

                </div>

            </div>


            <div class="text-[10px] text-gray-500 text-center border-t pt-2">
                * Please bring this receipt during product delivery. Thank you!
            </div>

        </div>


        <div class="mt-4 flex justify-end gap-3">

            <button
                onclick="closePrintModal()"
                class="px-4 py-2 bg-gray-200 text-gray-700 text-xs font-bold rounded-lg"
            >
                বন্ধ করুন
            </button>

            <button
                onclick="triggerPrint()"
                class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow"
            >
                🖨️ প্রিন্ট আউট
            </button>

        </div>

    </div>

</div>


{{-- OCR Libraries --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.4.168/pdf.min.mjs" type="module"></script>

<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>


{{-- Main JavaScript --}}
<script src="{{ asset('js/support.js') }}"></script>

@endsection