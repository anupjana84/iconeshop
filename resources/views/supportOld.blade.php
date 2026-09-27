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
        <div onclick="selectService('company')" id="btn-company" class="service-tab-btn            cursor-pointer          border-2 border-blue-500 bg-blue-50 hover:bg-blue-100 p-5 rounded-xl shadow-sm hover:shadow-md transition text-center">
            <!-- <div class="text-3xl text-blue-600 mb-2">📞</div>
            <h3 class="font-bold text-gray-800 text-lg mb-1">১) কোম্পানি কল বুকিং</h3>
            <p class="text-xs text-gray-600">অফিশিয়াল কোম্পানি কল বুকিং ও কেস আইডি ট্র্যাকিং</p> -->
        </div>

        <!-- <div onclick="selectService('external')" id="btn-external" class="service-tab-btn cursor-pointer border-2 border-orange-400 bg-orange-50 hover:bg-orange-100 p-5 rounded-xl shadow-sm hover:shadow-md transition text-center">
            <div class="text-3xl text-orange-500 mb-2">🚚</div>
            <h3 class="font-bold text-gray-800 text-lg mb-1">২) বাইরে থেকে সার্ভিস</h3>
            <p class="text-xs text-gray-600">থার্ডপার্টি/ভেন্ডর আউটসোর্স সার্ভিস ও ট্র্যাকিং</p>
        </div> -->

        <!-- <div onclick="selectService('inhouse')" id="btn-inhouse" class="service-tab-btn cursor-pointer border-2 border-green-500 bg-green-50 hover:bg-green-100 p-5 rounded-xl shadow-sm hover:shadow-md transition text-center">
            <div class="text-3xl text-green-600 mb-2">🛠️</div>
            <h3 class="font-bold text-gray-800 text-lg mb-1">৩) নিজের সার্ভিস</h3>
            <p class="text-xs text-gray-600">নিজস্ব শপ বা ইন-হাউস রিপেয়ারিং ম্যানেজমেন্ট</p>
        </div> -->
    </div>

    <!-- Dynamic Form & Container Area -->
    <div id="formArea" class="border-2 border-dashed border-gray-300 rounded-xl p-6 bg-gray-50 min-h-[250px]">
        <div class="text-center text-gray-500 font-medium my-10">
            👆 উপরে যেকোনো একটি সার্ভিস অপশন নির্বাচন করুন, সেটির ফর্ম এবং স্ট্যাটাস ড্যাশবোর্ড নিচে ওপেন হবে।
        </div>
    </div>

    <!-- Hidden Template: 1) Company Call Booking -->
    <div id="companyFormTemplate" class="hidden">
        <div class="space-y-6">
            <!-- Fixed Solid Color Dashboard Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div onclick="filterData('all')" style="background-color: #2563eb; color: #ffffff;" class="cursor-pointer p-4 rounded-lg shadow hover:opacity-90 transition">
                    <p class="text-xs font-semibold opacity-90">মোট কল বুকিং (সব দেখুন)</p>
                    <h4 id="comp_count_total" class="text-2xl font-bold mt-1">0</h4>
                </div>
                <div onclick="filterData('nocase')" style="background-color: #ef4444; color: #ffffff;" class="cursor-pointer p-4 rounded-lg shadow hover:opacity-90 transition">
                    <p class="text-xs font-semibold opacity-90">কেস আইডি বাকি</p>
                    <h4 id="comp_count_nocase" class="text-2xl font-bold mt-1">0</h4>
                </div>
                <div onclick="filterData('visited')" style="background-color: #d97706; color: #ffffff;" class="cursor-pointer p-4 rounded-lg shadow hover:opacity-90 transition">
                    <p class="text-xs font-semibold opacity-90">ইঞ্জিনিয়ার ভিজিটেড</p>
                    <h4 id="comp_count_visited" class="text-2xl font-bold mt-1">0</h4>
                </div>
                <div onclick="filterData('completed')" style="background-color: #16a34a; color: #ffffff;" class="cursor-pointer p-4 rounded-lg shadow hover:opacity-90 transition">
                    <p class="text-xs font-semibold opacity-90">কমপ্লিট হয়েছে</p>
                    <h4 id="comp_count_completed" class="text-2xl font-bold mt-1">0</h4>
                </div>
            </div>

            <!-- Auto OCR Scan Attachment -->
            <div class="bg-white p-5 rounded-xl border border-blue-200 shadow-sm">
                <h3 class="text-lg font-bold text-blue-700 mb-3 flex items-center gap-2">📄 পারচেজ বিল / ইনভয়েস অটো-স্ক্যান</h3>
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    <input
            type="file"
            id="invoiceFile"
            accept=".pdf,.jpg,.jpeg,.png,image/jpeg,image/png,application/pdf"
            class="block w-full text-sm text-gray-500
                   file:mr-4 file:py-2 file:px-4
                   file:rounded-lg file:border-0
                   file:text-sm file:font-semibold
                   file:bg-blue-50 file:text-blue-700
                   hover:file:bg-blue-100 cursor-pointer"
        >

        <button
            type="button"
            id="scanBillBtn"
            onclick="scanBill(event)"
            class="bg-blue-600 hover:bg-blue-700
                   text-white px-5 py-2 rounded-lg
                   font-medium shadow text-sm
                   whitespace-nowrap"
        >
            🔍 আপলোড ও অটো-স্ক্যান
        </button>
                </div>
            </div>

            <!-- Form -->
            <form onsubmit="saveCompanyBooking(event)" class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-4">
                <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">কোম্পানি কল বুকিং ফর্ম</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2 bg-blue-50 p-3 rounded-lg border border-blue-200">
                        <label class="block text-xs font-bold text-blue-800 mb-1">ফোন নম্বর (টাইপ করলে কাস্টমার অটো লোড হবে)</label>
                        <input type="text" id="comp_phone" onkeyup="searchCustomer(this.value, 'comp')" placeholder="মোবাইল নম্বর" required class="w-full border rounded-lg p-2 text-sm bg-white focus:ring-2 focus:ring-blue-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">কাস্টমারের নাম</label>
                        <input type="text" id="comp_name" placeholder="কাস্টমার নাম" required class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">১) বিল/ইনভয়েস অ্যাটাচ করার তারিখ (Bill Date)</label>
                        <input type="date" id="comp_bill_date" value="{{ date('Y-m-d') }}" required class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">২) কল বুকিং করার তারিখ (Booking Date)</label>
                        <input type="date" id="comp_booking_date" value="{{ date('Y-m-d') }}" required class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">কাস্টমারের অ্যাড্রেস</label>
                        <input type="text" id="comp_address" placeholder="ঠিকানা" required class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">পিন কোড নম্বর</label>
                        <input type="text" id="comp_pincode" placeholder="741156" required class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">প্রোডাক্ট ও মডেল</label>
                        <input type="text" id="comp_product" placeholder="যেমন: HITACHI AC 1TON" required class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">সিরিয়াল নম্বর</label>
                        <input type="text" id="comp_serial" placeholder="S/N Number" required class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">কেস আইডি নম্বর (অপশনাল)</label>
                        <input type="text" id="comp_case_1" placeholder="প্রথম কেস আইডি" class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">দ্বিতীয় কেস আইডি (অপশনাল)</label>
                        <input type="text" id="comp_case_2" placeholder="দ্বিতীয় কেস আইডি" class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-400 outline-none">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">রিমার্কস (Remarks)</label>
                    <textarea id="comp_remarks" rows="2" placeholder="অতিরিক্ত মন্তব্য..." class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-400 outline-none"></textarea>
                </div>
                <div class="text-right pt-2">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2.5 rounded-lg shadow-md text-sm transition">💾 কল বুকিং সেভ করুন</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Hidden Template: 2) External Service Form -->
    <div id="externalFormTemplate" class="hidden">
        <div class="space-y-6">
            <div class="grid grid-cols-2 md:grid-cols-6 gap-3">
                <div onclick="filterData('all')" style="background-color: #2563eb; color: #ffffff;" class="cursor-pointer p-3 rounded-lg shadow hover:opacity-90 transition">
                    <p class="text-[11px] font-semibold leading-tight opacity-90">মোট সার্ভিস জমা (সব)</p>
                    <h4 id="ext_count_total" class="text-xl font-bold mt-1">0</h4>
                </div>
                <div onclick="filterData('pending')" style="background-color: #d97706; color: #ffffff;" class="cursor-pointer p-3 rounded-lg shadow hover:opacity-90 transition">
                    <p class="text-[11px] font-semibold leading-tight opacity-90">কাজ চলছে / পেন্ডিং</p>
                    <h4 id="ext_count_pending" class="text-xl font-bold mt-1">0</h4>
                </div>
                <div onclick="filterData('sent')" style="background-color: #ea580c; color: #ffffff;" class="cursor-pointer p-3 rounded-lg shadow hover:opacity-90 transition">
                    <p class="text-[11px] font-semibold leading-tight opacity-90">সার্ভিস সেন্টারে পাঠানো হয়েছে</p>
                    <h4 id="ext_count_sent" class="text-xl font-bold mt-1">0</h4>
                </div>
                <div onclick="filterData('back')" style="background-color: #4f46e5; color: #ffffff;" class="cursor-pointer p-3 rounded-lg shadow hover:opacity-90 transition">
                    <p class="text-[11px] font-semibold leading-tight opacity-90">সার্ভিস সেন্টার থেকে এসেছে</p>
                    <h4 id="ext_count_back" class="text-xl font-bold mt-1">0</h4>
                </div>
                <div onclick="filterData('ready')" style="background-color: #9333ea; color: #ffffff;" class="cursor-pointer p-3 rounded-lg shadow hover:opacity-90 transition">
                    <p class="text-[11px] font-semibold leading-tight opacity-90">ফেরত এসেছে (ডেলিভারি বাকি)</p>
                    <h4 id="ext_count_ready" class="text-xl font-bold mt-1">0</h4>
                </div>
                <div onclick="filterData('delivered')" style="background-color: #16a34a; color: #ffffff;" class="cursor-pointer p-3 rounded-lg shadow hover:opacity-90 transition">
                    <p class="text-[11px] font-semibold leading-tight opacity-90">ডেলিভারি কমপ্লিট</p>
                    <h4 id="ext_count_delivered" class="text-xl font-bold mt-1">0</h4>
                </div>
            </div>

            <!-- Form -->
            <form onsubmit="saveExternalService(event)" class="bg-white p-6 rounded-xl border border-orange-200 shadow-sm space-y-4">
                <h3 class="text-lg font-bold text-orange-600 border-b pb-2 mb-4">🚚 বাইরে থেকে সার্ভিস ফর্ম (External / Outsourced)</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2 bg-orange-50 p-3 rounded-lg border border-orange-200">
                        <label class="block text-xs font-bold text-orange-800 mb-1">১) কাস্টমার ফোন নাম্বার (অটো-ফিল অন)</label>
                        <input type="text" id="ext_phone" onkeyup="searchCustomer(this.value, 'ext')" placeholder="ফোন নাম্বার টাইপ করুন..." required class="w-full border rounded-lg p-2 text-sm bg-white focus:ring-2 focus:ring-orange-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">২) কাস্টমারের নাম</label>
                        <input type="text" id="ext_name" placeholder="কাস্টমার নাম" required class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-orange-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">৩) কাস্টমারের এড্রেস</label>
                        <input type="text" id="ext_address" placeholder="এড্রেস" required class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-orange-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">৪) পিন কোড নাম্বার</label>
                        <input type="text" id="ext_pincode" placeholder="741156" required class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-orange-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">৫) প্রোডাক্ট নাম ও মডেল</label>
                        <input type="text" id="ext_product" placeholder="প্রোডাক্টের নাম" required class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-orange-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">৬) সিরিয়াল নাম্বার</label>
                        <input type="text" id="ext_serial" placeholder="S/N Number" class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-orange-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">৭) সম্ভাব্য খরচ/বাজেট (Max Budget/Est.)</label>
                        <input type="number" id="ext_budget" placeholder="যেমন: 1500" class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-orange-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">৮) ফাইনাল চার্জ (Final Cost)</label>
                        <input type="number" id="ext_final_cost" placeholder="যেমন: 1200" class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-orange-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">৯) জমা নেওয়ার তারিখ</label>
                        <input type="date" id="ext_receive_date" value="{{ date('Y-m-d') }}" required class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-orange-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">১০) সার্ভিস ভেন্ডর/সেন্টার (পরেও সিলেক্ট করা যাবে)</label>
                        <input list="vendorList" id="ext_vendor" placeholder="ভেন্ডরের নাম বেছে নিন বা টাইপ করুন" class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-orange-400 outline-none">
                        <datalist id="vendorList">
                            <option value="Canon Service Center">
                            <option value="HP Service Hub">
                            <option value="TVS Service Point">
                            <option value="Hitachi Care">
                        </datalist>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">১১) পাঠানোর তারিখ</label>
                        <input type="date" id="ext_sent_date" class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-orange-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">১২) ফেরত আসার তারিখ</label>
                        <input type="date" id="ext_back_date" class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-orange-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">১৩) ডেলিভারি তারিখ</label>
                        <input type="date" id="ext_delivery_date" class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-orange-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">১৪) ওয়ারেন্টি স্ট্যাটাস</label>
                        <div class="flex items-center gap-6 mt-2">
                            <label class="inline-flex items-center text-sm">
                                <input type="radio" name="ext_warranty" value="In Warranty" checked class="form-radio text-orange-600">
                                <span class="ml-2">ওয়ারেন্টি</span>
                            </label>
                            <label class="inline-flex items-center text-sm">
                                <input type="radio" name="ext_warranty" value="Out of Warranty" class="form-radio text-orange-600">
                                <span class="ml-2">উইদাউট ওয়ারেন্টি</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="text-right pt-4">
                    <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white font-bold px-6 py-2.5 rounded-lg shadow-md text-sm transition">💾 বাইরের সার্ভিস রেকর্ড সেভ করুন</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Hidden Template: 3) In-House Service Form -->
    <div id="inhouseFormTemplate" class="hidden">
        <div class="space-y-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div onclick="filterData('all')" style="background-color: #2563eb; color: #ffffff;" class="cursor-pointer p-4 rounded-lg shadow hover:opacity-90 transition">
                    <p class="text-xs font-semibold opacity-90">মোট সার্ভিস জমা (সব)</p>
                    <h4 id="inh_count_total" class="text-2xl font-bold mt-1">0</h4>
                </div>
                <div onclick="filterData('pending')" style="background-color: #d97706; color: #ffffff;" class="cursor-pointer p-4 rounded-lg shadow hover:opacity-90 transition">
                    <p class="text-xs font-semibold opacity-90">কাজ চলছে / পেন্ডিং</p>
                    <h4 id="inh_count_pending" class="text-2xl font-bold mt-1">0</h4>
                </div>
                <div onclick="filterData('ready')" style="background-color: #9333ea; color: #ffffff;" class="cursor-pointer p-4 rounded-lg shadow hover:opacity-90 transition">
                    <p class="text-xs font-semibold opacity-90">কাজ কমপ্লিট হয়েছে (ডেলিভারি বাকি)</p>
                    <h4 id="inh_count_ready" class="text-2xl font-bold mt-1">0</h4>
                </div>
                <div onclick="filterData('delivered')" style="background-color: #16a34a; color: #ffffff;" class="cursor-pointer p-4 rounded-lg shadow hover:opacity-90 transition">
                    <p class="text-xs font-semibold opacity-90">ডেলিভারি দেওয়া হয়েছে</p>
                    <h4 id="inh_count_delivered" class="text-2xl font-bold mt-1">0</h4>
                </div>
            </div>

            <!-- Form -->
            <form onsubmit="saveInhouseService(event)" class="bg-white p-6 rounded-xl border border-green-200 shadow-sm space-y-4">
                <h3 class="text-lg font-bold text-green-600 border-b pb-2 mb-4">🛠️ নিজস্ব সার্ভিস ফর্ম (In-House Repairing)</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2 bg-green-50 p-3 rounded-lg border border-green-200">
                        <label class="block text-xs font-bold text-green-800 mb-1">ফোন নম্বর (অটো কাস্টমার লোড হবে)</label>
                        <input type="text" id="inh_phone" onkeyup="searchCustomer(this.value, 'inh')" placeholder="মোবাইল নম্বর লিখুন..." required class="w-full border rounded-lg p-2 text-sm bg-white focus:ring-2 focus:ring-green-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">১) কাস্টমার নাম</label>
                        <input type="text" id="inh_name" placeholder="কাস্টমার নাম" required class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">২) বিকল্প ফোন নম্বর (যদি থাকে)</label>
                        <input type="text" id="inh_alt_phone" placeholder="বিকল্প মোবাইল নম্বর" class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">৩) কাস্টমার এড্রেস</label>
                        <input type="text" id="inh_address" placeholder="এড্রেস" required class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">৪) পিন কোড নম্বর</label>
                        <input type="text" id="inh_pincode" placeholder="741156" required class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">৫) প্রোডাক্ট ও প্রবলেম ডিটেইলস</label>
                        <input type="text" id="inh_product" placeholder="যেমন: Epson L3110 Paper Jam" required class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">৬) সম্ভাব্য এস্টিমেট (Max Estimate)</label>
                        <input type="number" id="inh_estimate" placeholder="যেমন: 600" class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">৭) ফাইনাল এমাউন্ট (Final Amount)</label>
                        <input type="number" id="inh_final_amount" placeholder="যেমন: 500" class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">৮) জমা দেওয়ার তারিখ</label>
                        <input type="date" id="inh_receive_date" value="{{ date('Y-m-d') }}" required class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">৯) ডেলিভারি করার তারিখ</label>
                        <input type="date" id="inh_delivery_date" class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-400 outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1">১০) ওয়ারেন্টি স্ট্যাটাস</label>
                        <div class="flex items-center gap-6 mt-2">
                            <label class="inline-flex items-center text-sm">
                                <input type="radio" name="inh_warranty" value="In Warranty" class="form-radio text-green-600">
                                <span class="ml-2">ওয়ারেন্টি</span>
                            </label>
                            <label class="inline-flex items-center text-sm">
                                <input type="radio" name="inh_warranty" value="Out of Warranty" checked class="form-radio text-green-600">
                                <span class="ml-2">উইদাউট ওয়ারেন্টি</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="text-right pt-4">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold px-6 py-2.5 rounded-lg shadow-md text-sm transition">💾 ইন-হাউস সার্ভিস রেকর্ড সেভ করুন</button>
                </div>
            </form>
        </div>
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

<!-- Print Money Receipt Modal -->
<div id="printModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center p-4 z-50">
    <div class="bg-white w-full max-w-lg rounded-xl shadow-2xl p-6 relative">
        <button onclick="closePrintModal()" class="absolute top-3 right-3 text-gray-500 hover:text-black font-bold text-xl">&times;</button>

        <!-- Printable Area -->
        <div id="printableReceipt" class="p-4 border rounded-lg bg-white text-black font-sans">
            <div class="text-center border-b pb-3 mb-3">
                <h2 class="text-xl font-bold tracking-wide">ICON COMPUTER</h2>
                <p class="text-xs">Bethuadahari, Nadia | Ph: 8597753337</p>
                <p class="text-xs font-semibold text-gray-600 mt-1">SERVICE & REPAIR RECEIPT</p>
            </div>
            <div class="text-xs space-y-1.5 mb-4">
                <div class="flex justify-between"><span><strong>Receipt No:</strong> <span id="pr_id"></span></span><span><strong>Date:</strong> <span id="pr_date"></span></span></div>
                <p><strong>Customer Name:</strong> <span id="pr_name"></span></p>
                <p><strong>Phone:</strong> <span id="pr_phone"></span></p>
                <p><strong>Address:</strong> <span id="pr_address"></span></p>
                <p><strong>Product/Issue:</strong> <span id="pr_product"></span></p>
                <p><strong>Serial No:</strong> <span id="pr_serial"></span></p>
                <div class="flex justify-between pt-2 border-t font-bold text-sm">
                    <span>Final Amount / Est:</span>
                    <span>₹<span id="pr_amount"></span></span>
                </div>
            </div>
            <div class="text-[10px] text-gray-500 text-center border-t pt-2">
                * Please bring this receipt during product delivery. Thank you!
            </div>
        </div>

        <div class="mt-4 flex justify-end gap-3">
            <button onclick="closePrintModal()" class="px-4 py-2 bg-gray-200 text-gray-700 text-xs font-bold rounded-lg">বন্ধ করুন</button>
            <button onclick="triggerPrint()" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow">🖨️ প্রিন্ট আউট</button>
        </div>
    </div>
</div>

<script>
    // Centralized Customer Database for Instant Auto-Fill
    var customerDatabase = {
        "8597753337": { name: "MS APARNA DEY", address: "BETHUADAHARI NADIA", pincode: "741126" },
        "9800000000": { name: "RITESH ROY", address: "KRISHNANAGAR NADIA", pincode: "741101" }
    };

    // Central Data Stores
    var currentTab = 'company';
    var currentFilter = 'all';
    var searchQuery = '';

    var companyBookings = [];
    var externalBookings = [];
    var inhouseBookings = [];

    // Helper: Calculate Days Difference (Aging)
    function calculateDays(startDate) {
        if (!startDate) return '0 দিন';
        var start = new Date(startDate);
        var today = new Date();
        var diffTime = Math.abs(today - start);
        var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) - 1;
        return (diffDays < 0 ? 0 : diffDays) + ' দিন';
    }

    // Tab Switcher Function
    function selectService(type) {
        currentTab = type;
        currentFilter = 'all';
        searchQuery = '';

        var area = document.getElementById('formArea');
        var tableArea = document.getElementById('tableSectionArea');
        tableArea.classList.remove('hidden');

        // Reset Tab Styles
        document.querySelectorAll('.service-tab-btn').forEach(function(btn) {
            btn.classList.remove('ring-4', 'ring-offset-2');
        });

        if (type === 'company') {
            document.getElementById('btn-company').classList.add('ring-4', 'ring-blue-300');
            area.innerHTML = document.getElementById('companyFormTemplate').innerHTML;
        } else if (type === 'external') {
            document.getElementById('btn-external').classList.add('ring-4', 'ring-orange-300');
            area.innerHTML = document.getElementById('externalFormTemplate').innerHTML;
        } else if (type === 'inhouse') {
            document.getElementById('btn-inhouse').classList.add('ring-4', 'ring-green-300');
            area.innerHTML = document.getElementById('inhouseFormTemplate').innerHTML;
        }

        var searchInput = document.getElementById('globalSearchInput');
        if (searchInput) searchInput.value = '';

        updateDashboardAndTable();
    }

    // Auto-Fill Customer Info on Typing Phone
    function searchCustomer(phone, prefix) {
        var cleanPhone = phone.trim();
        if (customerDatabase[cleanPhone]) {
            var cust = customerDatabase[cleanPhone];
            if (document.getElementById(prefix + '_name')) document.getElementById(prefix + '_name').value = cust.name;
            if (document.getElementById(prefix + '_address')) document.getElementById(prefix + '_address').value = cust.address;
            if (document.getElementById(prefix + '_pincode')) document.getElementById(prefix + '_pincode').value = cust.pincode;
        }
    }

    function saveCustomerToDatabase(phone, name, address, pincode) {
        if (phone && !customerDatabase[phone]) {
            customerDatabase[phone] = { name: name, address: address, pincode: pincode };
        }
    }

    // Filtering by Clicking Dashboard Cards
    function filterData(statusKey) {
        currentFilter = statusKey;
        var badge = document.getElementById('activeFilterBadge');
        if (badge) {
            badge.innerText = 'ফিল্টার: ' + statusKey.toUpperCase();
        }
        renderTable();
    }

    function handleSearch(val) {
        searchQuery = val.trim().toLowerCase();
        renderTable();
    }

    // Save Handlers
    function saveCompanyBooking(e) {
        e.preventDefault();
        var phone = document.getElementById('comp_phone').value.trim();
        var name = document.getElementById('comp_name').value.trim();
        var address = document.getElementById('comp_address').value.trim();
        var pincode = document.getElementById('comp_pincode').value.trim();

        saveCustomerToDatabase(phone, name, address, pincode);

        companyBookings.push({
            id: 'COMP-' + Date.now().toString().slice(-4),
            phone: phone,
            name: name,
            address: address,
            pincode: pincode,
            product: document.getElementById('comp_product').value,
            serial: document.getElementById('comp_serial').value,
            bill_date: document.getElementById('comp_bill_date').value,
            booking_date: document.getElementById('comp_booking_date').value,
            case_id_1: document.getElementById('comp_case_1').value.trim(),
            case_id_2: document.getElementById('comp_case_2').value.trim(),
            status: 'Pending',
            created_at: new Date().toISOString().split('T')[0]
        });

        alert('✅ কোম্পানি কল বুকিং সেভ করা হয়েছে!');
        updateDashboardAndTable();
    }

    function saveExternalService(e) {
        e.preventDefault();
        var phone = document.getElementById('ext_phone').value.trim();
        var name = document.getElementById('ext_name').value.trim();
        var address = document.getElementById('ext_address').value.trim();
        var pincode = document.getElementById('ext_pincode').value.trim();

        saveCustomerToDatabase(phone, name, address, pincode);

        externalBookings.push({
            id: 'EXT-' + Date.now().toString().slice(-4),
            phone: phone,
            name: name,
            address: address,
            pincode: pincode,
            product: document.getElementById('ext_product').value,
            serial: document.getElementById('ext_serial').value || 'N/A',
            budget: document.getElementById('ext_budget').value || '0',
            final_cost: document.getElementById('ext_final_cost').value || '0',
            receive_date: document.getElementById('ext_receive_date').value,
            vendor: document.getElementById('ext_vendor').value || 'নির্ধারণ করা হয়নি',
            sent_date: document.getElementById('ext_sent_date').value,
            back_date: document.getElementById('ext_back_date').value,
            delivery_date: document.getElementById('ext_delivery_date').value,
            status: 'Pending',
            created_at: new Date().toISOString().split('T')[0]
        });

        alert('✅ বাইরের সার্ভিস তথ্য সফলভাবে সেভ করা হয়েছে!');
        updateDashboardAndTable();
    }

    function saveInhouseService(e) {
        e.preventDefault();
        var phone = document.getElementById('inh_phone').value.trim();
        var name = document.getElementById('inh_name').value.trim();
        var address = document.getElementById('inh_address').value.trim();
        var pincode = document.getElementById('inh_pincode').value.trim();

        saveCustomerToDatabase(phone, name, address, pincode);

        inhouseBookings.push({
            id: 'INH-' + Date.now().toString().slice(-4),
            phone: phone,
            name: name,
            address: address,
            pincode: pincode,
            product: document.getElementById('inh_product').value,
            estimate: document.getElementById('inh_estimate').value || '0',
            final_amount: document.getElementById('inh_final_amount').value || '0',
            receive_date: document.getElementById('inh_receive_date').value,
            delivery_date: document.getElementById('inh_delivery_date').value,
            status: 'Pending',
            created_at: new Date().toISOString().split('T')[0]
        });

        alert('✅ ইন-হাউস সার্ভিস রেকর্ড সফলভাবে সেভ হয়েছে!');
        updateDashboardAndTable();
    }

    // Dashboard Counters Update
    function updateDashboardAndTable() {
        if (currentTab === 'company') {
            var total = companyBookings.length;
            var nocase = companyBookings.filter(function(b) { return !b.case_id_1; }).length;
            var visited = companyBookings.filter(function(b) { return b.status === 'Engineer Visited'; }).length;
            var completed = companyBookings.filter(function(b) { return b.status === 'Completed'; }).length;

            if (document.getElementById('comp_count_total')) document.getElementById('comp_count_total').innerText = total;
            if (document.getElementById('comp_count_nocase')) document.getElementById('comp_count_nocase').innerText = nocase;
            if (document.getElementById('comp_count_visited')) document.getElementById('comp_count_visited').innerText = visited;
            if (document.getElementById('comp_count_completed')) document.getElementById('comp_count_completed').innerText = completed;
        } else if (currentTab === 'external') {
            var total = externalBookings.length;
            var pending = externalBookings.filter(function(b) { return b.status === 'Pending'; }).length;
            var sent = externalBookings.filter(function(b) { return b.status === 'Sent to Center'; }).length;
            var back = externalBookings.filter(function(b) { return b.status === 'Returned from Center'; }).length;
            var ready = externalBookings.filter(function(b) { return b.status === 'Ready/Back'; }).length;
            var delivered = externalBookings.filter(function(b) { return b.status === 'Delivered'; }).length;

            if (document.getElementById('ext_count_total')) document.getElementById('ext_count_total').innerText = total;
            if (document.getElementById('ext_count_pending')) document.getElementById('ext_count_pending').innerText = pending;
            if (document.getElementById('ext_count_sent')) document.getElementById('ext_count_sent').innerText = sent;
            if (document.getElementById('ext_count_back')) document.getElementById('ext_count_back').innerText = back;
            if (document.getElementById('ext_count_ready')) document.getElementById('ext_count_ready').innerText = ready;
            if (document.getElementById('ext_count_delivered')) document.getElementById('ext_count_delivered').innerText = delivered;
        } else if (currentTab === 'inhouse') {
            var total = inhouseBookings.length;
            var pending = inhouseBookings.filter(function(b) { return b.status === 'Pending'; }).length;
            var ready = inhouseBookings.filter(function(b) { return b.status === 'Ready/Repaired'; }).length;
            var delivered = inhouseBookings.filter(function(b) { return b.status === 'Delivered'; }).length;

            if (document.getElementById('inh_count_total')) document.getElementById('inh_count_total').innerText = total;
            if (document.getElementById('inh_count_pending')) document.getElementById('inh_count_pending').innerText = pending;
            if (document.getElementById('inh_count_ready')) document.getElementById('inh_count_ready').innerText = ready;
            if (document.getElementById('inh_count_delivered')) document.getElementById('inh_count_delivered').innerText = delivered;
        }

        renderTable();
    }

    // Render Master Table
    function renderTable() {
        var thead = document.getElementById('mainTableHead');
        var tbody = document.getElementById('mainTableBody');
        if (!thead || !tbody) return;

        var list = [];
        if (currentTab === 'company') list = companyBookings;
        else if (currentTab === 'external') list = externalBookings;
        else if (currentTab === 'inhouse') list = inhouseBookings;

        // Apply Search Filter
        if (searchQuery !== '') {
            list = list.filter(function(item) {
                return item.name.toLowerCase().includes(searchQuery) ||
                       item.phone.toLowerCase().includes(searchQuery) ||
                       item.product.toLowerCase().includes(searchQuery);
            });
        }

        // Apply Dashboard Filter
        if (currentFilter !== 'all') {
            if (currentTab === 'company') {
                if (currentFilter === 'nocase') list = list.filter(function(b) { return !b.case_id_1; });
                else if (currentFilter === 'visited') list = list.filter(function(b) { return b.status === 'Engineer Visited'; });
                else if (currentFilter === 'completed') list = list.filter(function(b) { return b.status === 'Completed'; });
            } else if (currentTab === 'external') {
                if (currentFilter === 'pending') list = list.filter(function(b) { return b.status === 'Pending'; });
                else if (currentFilter === 'sent') list = list.filter(function(b) { return b.status === 'Sent to Center'; });
                else if (currentFilter === 'back') list = list.filter(function(b) { return b.status === 'Returned from Center'; });
                else if (currentFilter === 'ready') list = list.filter(function(b) { return b.status === 'Ready/Back'; });
                else if (currentFilter === 'delivered') list = list.filter(function(b) { return b.status === 'Delivered'; });
            } else {
                if (currentFilter === 'pending') list = list.filter(function(b) { return b.status === 'Pending'; });
                else if (currentFilter === 'ready') list = list.filter(function(b) { return b.status === 'Ready/Repaired'; });
                else if (currentFilter === 'delivered') list = list.filter(function(b) { return b.status === 'Delivered'; });
            }
        }

        // Table Header
        if (currentTab === 'company') {
            thead.innerHTML = '<tr class="bg-blue-50 border-b text-blue-900">' +
                '<th class="p-3">আইডি</th><th class="p-3">কাস্টমার ও ফোন</th><th class="p-3">প্রোডাক্ট</th><th class="p-3">কেস আইডি</th><th class="p-3">কল বুকিং তারিখ</th><th class="p-3">কল বুকের বয়স (Aging)</th><th class="p-3">স্ট্যাটাস</th><th class="p-3 text-center">অ্যাকশন</th></tr>';
        } else if (currentTab === 'external') {
            thead.innerHTML = '<tr class="bg-orange-50 border-b text-orange-900">' +
                '<th class="p-3">আইডি</th><th class="p-3">কাস্টমার ও ফোন</th><th class="p-3">প্রোডাক্ট</th><th class="p-3">ভেন্ডর</th><th class="p-3">কতদিন হলো (Aging)</th><th class="p-3">ফাইনাল চার্জ</th><th class="p-3">স্ট্যাটাস (ড্রপডাউন)</th><th class="p-3 text-center">অ্যাকশন</th></tr>';
        } else if (currentTab === 'inhouse') {
            thead.innerHTML = '<tr class="bg-green-50 border-b text-green-900">' +
                '<th class="p-3">আইডি</th><th class="p-3">কাস্টমার ও ফোন</th><th class="p-3">প্রোডাক্ট/সমস্যা</th><th class="p-3">কতদিন হলো (Aging)</th><th class="p-3">এস্টিমেট / ফাইনাল চার্জ</th><th class="p-3">স্ট্যাটাস (ড্রপডাউন)</th><th class="p-3 text-center">অ্যাকশন</th></tr>';
        }

        if (list.length === 0) {
            tbody.innerHTML = '<tr><td colspan="8" class="text-center p-6 text-gray-400">কোনো তথ্য পাওয়া যায়নি।</td></tr>';
            return;
        }

        var html = '';
        for (var i = 0; i < list.length; i++) {
            var item = list[i];

            if (currentTab === 'company') {
                var days = calculateDays(item.booking_date || item.created_at);
                var caseBtn = item.case_id_1 ?
                    '<span class="bg-blue-100 text-blue-800 font-bold px-2 py-1 rounded text-xs">' + item.case_id_1 + '</span>' :
                    '<button onclick="editField(\'' + item.id + '\', \'case_id_1\')" class="text-xs bg-red-100 text-red-600 hover:bg-red-200 px-2 py-1 rounded font-bold">➕ কেস আইডি দিন</button>';

                html += '<tr class="border-b hover:bg-gray-50">' +
                    '<td class="p-3 font-semibold text-xs">' + item.id + '</td>' +
                    '<td class="p-3"><div class="font-bold text-gray-800">' + item.name + '</div><div class="text-xs text-gray-500">📞 ' + item.phone + '</div></td>' +
                    '<td class="p-3"><div class="text-xs font-semibold">' + item.product + '</div><div class="text-xs text-gray-500">S/N: ' + item.serial + '</div></td>' +
                    '<td class="p-3">' + caseBtn + '</td>' +
                    '<td class="p-3 text-xs font-medium text-gray-700">' + (item.booking_date || 'N/A') + '</td>' +
                    '<td class="p-3 text-xs"><span class="bg-blue-100 text-blue-800 font-bold px-2.5 py-1 rounded-full">' + days + '</span></td>' +
                    '<td class="p-3">' +
                        '<select onchange="updateStatus(\'' + item.id + '\', this.value)" class="text-xs border rounded p-1 font-semibold outline-none bg-white shadow-sm">' +
                            '<option value="Pending" ' + (item.status === 'Pending' ? 'selected' : '') + '>পেন্ডিং</option>' +
                            '<option value="Engineer Visited" ' + (item.status === 'Engineer Visited' ? 'selected' : '') + '>ইঞ্জিনিয়ার ভিজিটেড</option>' +
                            '<option value="Completed" ' + (item.status === 'Completed' ? 'selected' : '') + '>কমপ্লিট</option>' +
                        '</select>' +
                    '</td>' +
                    '<td class="p-3 text-center"><button onclick="printReceipt(\'' + item.id + '\')" class="text-blue-600 hover:underline text-xs font-bold mr-2">🖨️ প্রিন্ট</button><button onclick="deleteRecord(\'' + item.id + '\')" class="text-red-500 hover:text-red-700 text-xs font-bold">🗑️ ডিলিট</button></td>' +
                '</tr>';
            }
            else if (currentTab === 'external') {
                var days = calculateDays(item.receive_date);
                html += '<tr class="border-b hover:bg-gray-50">' +
                    '<td class="p-3 font-semibold text-xs">' + item.id + '</td>' +
                    '<td class="p-3"><div class="font-bold text-gray-800">' + item.name + '</div><div class="text-xs text-gray-500">📞 ' + item.phone + '</div></td>' +
                    '<td class="p-3 text-xs font-semibold">' + item.product + '</td>' +
                    '<td class="p-3 text-xs"><span class="font-bold text-gray-700">' + item.vendor + '</span> <button onclick="editField(\'' + item.id + '\', \'vendor\')" class="text-blue-500 hover:underline text-[10px]">✏️ এডিট</button></td>' +
                    '<td class="p-3 text-xs"><span class="bg-orange-100 text-orange-800 font-bold px-2 py-0.5 rounded">' + days + '</span></td>' +
                    '<td class="p-3 text-xs font-bold text-green-700">₹' + item.final_cost + ' <button onclick="editField(\'' + item.id + '\', \'final_cost\')" class="text-blue-500 hover:underline text-[10px]">✏️ এডিট</button></td>' +
                    '<td class="p-3">' +
                        '<select onchange="updateStatus(\'' + item.id + '\', this.value)" class="text-xs border rounded p-1 font-semibold outline-none bg-white shadow-sm">' +
                            '<option value="Pending" ' + (item.status === 'Pending' ? 'selected' : '') + '>পেন্ডিং</option>' +
                            '<option value="Sent to Center" ' + (item.status === 'Sent to Center' ? 'selected' : '') + '>সার্ভিস সেন্টারে পাঠানো হয়েছে</option>' +
                            '<option value="Returned from Center" ' + (item.status === 'Returned from Center' ? 'selected' : '') + '>সার্ভিস সেন্টার থেকে চলে এসেছে</option>' +
                            '<option value="Ready/Back" ' + (item.status === 'Ready/Back' ? 'selected' : '') + '>ফেরত এসেছে (ডেলিভারি বাকি)</option>' +
                            '<option value="Delivered" ' + (item.status === 'Delivered' ? 'selected' : '') + '>ডেলিভারি দেওয়া হয়েছে</option>' +
                        '</select>' +
                    '</td>' +
                    '<td class="p-3 text-center"><button onclick="printReceipt(\'' + item.id + '\')" class="text-blue-600 hover:underline text-xs font-bold mr-2">🖨️ প্রিন্ট</button><button onclick="deleteRecord(\'' + item.id + '\')" class="text-red-500 hover:text-red-700 text-xs font-bold">🗑️ ডিলিট</button></td>' +
                '</tr>';
            }
            else if (currentTab === 'inhouse') {
                var days = calculateDays(item.receive_date);
                html += '<tr class="border-b hover:bg-gray-50">' +
                    '<td class="p-3 font-semibold text-xs">' + item.id + '</td>' +
                    '<td class="p-3"><div class="font-bold text-gray-800">' + item.name + '</div><div class="text-xs text-gray-500">📞 ' + item.phone + '</div></td>' +
                    '<td class="p-3 text-xs font-semibold">' + item.product + '</td>' +
                    '<td class="p-3 text-xs"><span class="bg-green-100 text-green-800 font-bold px-2 py-0.5 rounded">' + days + '</span></td>' +
                    '<td class="p-3 text-xs">Est: ₹' + item.estimate + ' | <span class="font-bold text-green-700">Final: ₹' + item.final_amount + '</span> <button onclick="editField(\'' + item.id + '\', \'final_amount\')" class="text-blue-500 hover:underline text-[10px]">✏️ এডিট</button></td>' +
                    '<td class="p-3">' +
                        '<select onchange="updateStatus(\'' + item.id + '\', this.value)" class="text-xs border rounded p-1 font-semibold outline-none bg-white shadow-sm">' +
                            '<option value="Pending" ' + (item.status === 'Pending' ? 'selected' : '') + '>কাজ চলছে/পেন্ডিং</option>' +
                            '<option value="Ready/Repaired" ' + (item.status === 'Ready/Repaired' ? 'selected' : '') + '>কাজ কমপ্লিট হয়েছে</option>' +
                            '<option value="Delivered" ' + (item.status === 'Delivered' ? 'selected' : '') + '>ডেলিভারি দেওয়া হয়েছে</option>' +
                        '</select>' +
                    '</td>' +
                    '<td class="p-3 text-center"><button onclick="printReceipt(\'' + item.id + '\')" class="text-blue-600 hover:underline text-xs font-bold mr-2">🖨️ প্রিন্ট</button><button onclick="deleteRecord(\'' + item.id + '\')" class="text-red-500 hover:text-red-700 text-xs font-bold">🗑️ ডিলিট</button></td>' +
                '</tr>';
            }
        }
        tbody.innerHTML = html;
    }

    // Editable Fields (Vendor, Final Amount, Case ID)
    function editField(id, fieldName) {
        var newValue = prompt("নতুন তথ্য প্রবেশ করান:");
        if (newValue !== null && newValue.trim() !== "") {
            var list = (currentTab === 'company') ? companyBookings : (currentTab === 'external') ? externalBookings : inhouseBookings;
            for (var i = 0; i < list.length; i++) {
                if (list[i].id === id) {
                    list[i][fieldName] = newValue.trim();
                    break;
                }
            }
            updateDashboardAndTable();
        }
    }

    // Dynamic Status Updater
    function updateStatus(id, newStatus) {
        var list = (currentTab === 'company') ? companyBookings : (currentTab === 'external') ? externalBookings : inhouseBookings;
        for (var i = 0; i < list.length; i++) {
            if (list[i].id === id) {
                list[i].status = newStatus;
                break;
            }
        }
        updateDashboardAndTable();
    }

    // Delete Record
    function deleteRecord(id) {
        if (confirm('আপনি কি এই সার্ভিস রেকর্ডটি ডিলিট করতে চান?')) {
            if (currentTab === 'company') companyBookings = companyBookings.filter(function(b) { return b.id !== id; });
            else if (currentTab === 'external') externalBookings = externalBookings.filter(function(b) { return b.id !== id; });
            else if (currentTab === 'inhouse') inhouseBookings = inhouseBookings.filter(function(b) { return b.id !== id; });

            updateDashboardAndTable();
        }
    }

    // Print Receipt Modal Logic
    function printReceipt(id) {
        var item = null;
        var list = (currentTab === 'company') ? companyBookings : (currentTab === 'external') ? externalBookings : inhouseBookings;
        for (var i = 0; i < list.length; i++) {
            if (list[i].id === id) { item = list[i]; break; }
        }

        if (!item) return;

        document.getElementById('pr_id').innerText = item.id;
        document.getElementById('pr_date').innerText = item.created_at || new Date().toISOString().split('T')[0];
        document.getElementById('pr_name').innerText = item.name;
        document.getElementById('pr_phone').innerText = item.phone;
        document.getElementById('pr_address').innerText = item.address || 'N/A';
        document.getElementById('pr_product').innerText = item.product;
        document.getElementById('pr_serial').innerText = item.serial || 'N/A';
        document.getElementById('pr_amount').innerText = item.final_amount || item.final_cost || item.estimate || item.budget || '0';

        document.getElementById('printModal').classList.remove('hidden');
    }

    function closePrintModal() {
        document.getElementById('printModal').classList.add('hidden');
    }

    function triggerPrint() {
        var printContents = document.getElementById('printableReceipt').innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = '<div style="width: 350px; margin: 0 auto; font-family: sans-serif;">' + printContents + '</div>';
        window.print();
        document.body.innerHTML = originalContents;
        window.location.reload();
    }

    // Default Selection On Load
    window.onload = function() {
        selectService('company');
    };
</script>

<!-- OCR Scan: sends the uploaded bill to the Laravel backend route('scan.ocr') -->
<script>
async function scanBill(evt) {
    const fileInput = document.getElementById('invoiceFile');
    if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
        alert("অনুগ্রহ করে প্রথমে একটি ইনভয়েসের ছবি বা PDF বেছে নিন!");
        return;
    }

    var scanBtn = document.getElementById('scanBillBtn');
    var originalBtnText = scanBtn ? scanBtn.innerText : '';
    if (scanBtn) {
        scanBtn.innerText = "⏳ স্ক্যান করা হচ্ছে...";
        scanBtn.disabled = true;
    }

    const formData = new FormData();
    formData.append('invoice', fileInput.files[0]);

    try {
        let response = await fetch("{{ route('scan.ocr') }}", {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        let result = await response.json();

        if (result.success && result.raw_text) {
            parseAndFillInvoiceData(result.raw_text);
            alert('✅ বিল থেকে কাস্টমারের তথ্য সফলভাবে অটো-ফিল করা হয়েছে!');
        } else {
            alert(result.message || "বিল স্ক্যান করতে সমস্যা হয়েছে। দয়া করে ম্যানুয়ালি তথ্য দিন।");
        }

    } catch (error) {
        console.error("OCR Scanning Error:", error);
        alert("সার্ভার বা স্ক্যানারে সমস্যা হয়েছে। দয়া করে ম্যানুয়ালি তথ্য দিন।");
    } finally {
        if (scanBtn) {
            scanBtn.innerText = originalBtnText;
            scanBtn.disabled = false;
        }
    }
}

function parseAndFillInvoiceData(text) {
    // ১. ফোন নম্বর (Mobile No / WA No / Phone)
    const phoneMatch = text.match(/(?:Mobile|WA|Phone)\s*(?:No)?\s*[:\-]?\s*(\d{10})/i) || text.match(/\b[6-9]\d{9}\b/);
    if (phoneMatch && document.getElementById('comp_phone')) {
        document.getElementById('comp_phone').value = phoneMatch[1] || phoneMatch[0];
    }

    // ২. কাস্টমারের নাম (Bill To এর নিচের লাইন)
    const nameMatch = text.match(/Bill\s*To[\s\S]*?[•\-]?\s*([A-Za-z\s]{3,30})/i);
    if (nameMatch && document.getElementById('comp_name')) {
        let cleanName = nameMatch[1].replace(/Address|Mobile|Pin|Reward/gi, '').trim();
        document.getElementById('comp_name').value = cleanName;
    }

    // ৩. এড্রেস (Address : ...)
    const addressMatch = text.match(/Address\s*[:\-]\s*(.*?),?\s*Pin[-\s]/i);
    if (addressMatch && document.getElementById('comp_address')) {
        document.getElementById('comp_address').value = addressMatch[1].trim();
    }

    // ৪. পিন কোড
    const pinMatch = text.match(/Pin[-\s]*(\d{6})/i) || text.match(/\b7\d{5}\b/);
    if (pinMatch && document.getElementById('comp_pincode')) {
        document.getElementById('comp_pincode').value = pinMatch[1] || pinMatch[0];
    }

    // ৫. বিল তারিখ (28-Jul-2026 বা Sale Date)
    const dateMatch = text.match(/(?:Sale\s*)?Date\s*[:\-]\s*(\d{1,2}-[A-Za-z]{3}-\d{4})/i);
    if (dateMatch && document.getElementById('comp_bill_date')) {
        const d = new Date(dateMatch[1]);
        if (!isNaN(d.getTime())) {
            document.getElementById('comp_bill_date').value = d.toISOString().split('T')[0];
        }
    }

    // ৬. প্রোডাক্ট নাম ও মডেল
    const productMatch = text.match(/(HAIER|HITACHI|CANON|HP|LENOVO|EPSON|LG|SAMSUNG|VOLTAS)\s*[A-Z0-9\s]*\bAC\b/i);
    const modelMatch = text.match(/Model\s*[:\-]?\s*([A-Z0-9.()]+)/i);
    if (document.getElementById('comp_product')) {
        let prodStr = "";
        if (productMatch) prodStr += productMatch[0].trim();
        if (modelMatch) prodStr += (prodStr ? " (" : "") + modelMatch[1].trim() + (prodStr ? ")" : "");
        if (prodStr) document.getElementById('comp_product').value = prodStr;
    }

    // ৭. সিরিয়াল নম্বর (একাধিক থাকলে সব যোগ করুন)
    const serialMatches = [...text.matchAll(/(?:Sl\s*no|S\/N)\s*[:\-]?\s*([A-Za-z0-9]+)/gi)];
    if (serialMatches.length > 0 && document.getElementById('comp_serial')) {
        const serials = serialMatches.map(m => m[1]).join(', ');
        document.getElementById('comp_serial').value = serials;
    }
}
</script>
@endsection