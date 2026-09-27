<div id="companyFormTemplate" class="hidden">

    <div class="space-y-6">

        {{-- Dashboard --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

            <div
                onclick="filterData('all')"
                style="background-color:#2563eb;color:#ffffff;"
                class="cursor-pointer p-4 rounded-lg shadow hover:opacity-90 transition"
            >
                <p class="text-xs font-semibold opacity-90">
                    মোট কল বুকিং
                </p>

                <h4 id="comp_count_total" class="text-2xl font-bold mt-1">
                    0
                </h4>
            </div>


            <div
                onclick="filterData('nocase')"
                style="background-color:#ef4444;color:#ffffff;"
                class="cursor-pointer p-4 rounded-lg shadow hover:opacity-90 transition"
            >
                <p class="text-xs font-semibold opacity-90">
                    কেস আইডি বাকি
                </p>

                <h4 id="comp_count_nocase" class="text-2xl font-bold mt-1">
                    0
                </h4>
            </div>


            <div
                onclick="filterData('visited')"
                style="background-color:#d97706;color:#ffffff;"
                class="cursor-pointer p-4 rounded-lg shadow hover:opacity-90 transition"
            >
                <p class="text-xs font-semibold opacity-90">
                    ইঞ্জিনিয়ার ভিজিটেড
                </p>

                <h4 id="comp_count_visited" class="text-2xl font-bold mt-1">
                    0
                </h4>
            </div>


            <div
                onclick="filterData('completed')"
                style="background-color:#16a34a;color:#ffffff;"
                class="cursor-pointer p-4 rounded-lg shadow hover:opacity-90 transition"
            >
                <p class="text-xs font-semibold opacity-90">
                    কমপ্লিট হয়েছে
                </p>

                <h4 id="comp_count_completed" class="text-2xl font-bold mt-1">
                    0
                </h4>
            </div>

        </div>


        {{-- OCR --}}
        <div class="bg-white p-5 rounded-xl border border-blue-200 shadow-sm">

            <h3 class="text-lg font-bold text-blue-700 mb-3">
                📄 পারচেজ বিল / ইনভয়েস অটো-স্ক্যান
            </h3>


            <div class="flex flex-col sm:flex-row items-center gap-4">

                <input
                    type="file"
                    id="invoiceFile"
                    accept=".pdf,.jpg,.jpeg,.png,image/jpeg,image/png,application/pdf"
                    onchange="handleInvoiceFile(this)"
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
                    onclick="scanBill()"
                    disabled
                    class="bg-gray-400 text-white px-5 py-2 rounded-lg
                    font-medium shadow text-sm whitespace-nowrap
                    cursor-not-allowed"
                >
                    🔍 আপলোড ও অটো-স্ক্যান
                </button>

            </div>


            <div
                id="ocrProgress"
                class="hidden mt-4"
            >

                <div class="flex justify-between text-xs mb-1">

                    <span id="ocrStatus">
                        OCR শুরু হচ্ছে...
                    </span>

                    <span id="ocrPercent">
                        0%
                    </span>

                </div>

                <div class="w-full bg-gray-200 rounded-full h-2">

                    <div
                        id="ocrProgressBar"
                        class="bg-blue-600 h-2 rounded-full transition-all"
                        style="width:0%"
                    ></div>

                </div>

            </div>


            <div
                id="ocrFileName"
                class="mt-2 text-xs text-gray-500"
            ></div>

        </div>


        {{-- Company Form --}}
        <form
            onsubmit="saveCompanyBooking(event)"
            class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-4"
        >

            <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">
                কোম্পানি কল বুকিং ফর্ম
            </h3>


            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div class="md:col-span-2 bg-blue-50 p-3 rounded-lg border border-blue-200">

                    <label class="block text-xs font-bold text-blue-800 mb-1">
                        ফোন নম্বর
                    </label>

                    <input
                        type="text"
                        id="comp_phone"
                        maxlength="10"
                        oninput="searchCustomer(this.value,'comp')"
                        placeholder="১০ ডিজিট মোবাইল নম্বর"
                        required
                        class="w-full border rounded-lg p-2 text-sm bg-white focus:ring-2 focus:ring-blue-400 outline-none"
                    >

                </div>


                <div>

                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        কাস্টমারের নাম
                    </label>

                    <input
                        type="text"
                        id="comp_name"
                        placeholder="কাস্টমার নাম"
                        required
                        class="w-full border rounded-lg p-2 text-sm"
                    >

                </div>


                <div>

                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        বিল / ইনভয়েস তারিখ
                    </label>

                    <input
                        type="date"
                        id="comp_bill_date"
                        value="{{ date('Y-m-d') }}"
                        required
                        class="w-full border rounded-lg p-2 text-sm"
                    >

                </div>


                <div>

                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        কল বুকিং তারিখ
                    </label>

                    <input
                        type="date"
                        id="comp_booking_date"
                        value="{{ date('Y-m-d') }}"
                        required
                        class="w-full border rounded-lg p-2 text-sm"
                    >

                </div>


                <div>

                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        কাস্টমারের অ্যাড্রেস
                    </label>

                    <input
                        type="text"
                        id="comp_address"
                        placeholder="ঠিকানা"
                        required
                        class="w-full border rounded-lg p-2 text-sm"
                    >

                </div>


                <div>

                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        পিন কোড
                    </label>

                    <input
                        type="text"
                        id="comp_pincode"
                        maxlength="6"
                        placeholder="741156"
                        required
                        class="w-full border rounded-lg p-2 text-sm"
                    >

                </div>


                <div>

                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        প্রোডাক্ট ও মডেল
                    </label>

                    <input
                        type="text"
                        id="comp_product"
                        placeholder="যেমন: HITACHI AC 1TON"
                        required
                        class="w-full border rounded-lg p-2 text-sm"
                    >

                </div>


                <div>

                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        সিরিয়াল নম্বর
                    </label>

                    <input
                        type="text"
                        id="comp_serial"
                        placeholder="S/N Number"
                        required
                        class="w-full border rounded-lg p-2 text-sm"
                    >

                </div>


                {{-- 
                <div>

                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        প্রথম কেস আইডি
                    </label>

                    <input
                        type="text"
                        id="comp_case_1"
                        placeholder="কেস আইডি"
                        class="w-full border rounded-lg p-2 text-sm"
                    >

                </div>


                <div>

                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        দ্বিতীয় কেস আইডি
                    </label>

                    <input
                        type="text"
                        id="comp_case_2"
                        placeholder="দ্বিতীয় কেস আইডি"
                        class="w-full border rounded-lg p-2 text-sm"
                    >

                </div>
                --}}

            </div>


            <div>

                <label class="block text-xs font-semibold text-gray-600 mb-1">
                    রিমার্কস
                </label>

                <textarea
                    id="comp_remarks"
                    rows="2"
                    placeholder="অতিরিক্ত মন্তব্য..."
                    class="w-full border rounded-lg p-2 text-sm"
                ></textarea>

            </div>


            <div class="text-right pt-2">

                <button
                    type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2.5 rounded-lg shadow-md text-sm"
                >
                    💾 কল বুকিং সেভ করুন
                </button>

            </div>

        </form>

    </div>

</div>