<div class="space-y-6">
    <!-- Fixed Solid Color Dashboard Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div onclick="filterData('all')" style="background-color: #2563eb; color: #ffffff;"
            class="cursor-pointer p-4 rounded-lg shadow hover:opacity-90 transition">
            <p class="text-xs font-semibold opacity-90">মোট কল বুকিং (সব দেখুন)</p>
            <h4 id="comp_count_total" class="text-2xl font-bold mt-1">0</h4>
        </div>
        <div onclick="filterData('nocase')" style="background-color: #ef4444; color: #ffffff;"
            class="cursor-pointer p-4 rounded-lg shadow hover:opacity-90 transition">
            <p class="text-xs font-semibold opacity-90">কেস আইডি বাকি</p>
            <h4 id="comp_count_nocase" class="text-2xl font-bold mt-1">0</h4>
        </div>
        <div onclick="filterData('visited')" style="background-color: #d97706; color: #ffffff;"
            class="cursor-pointer p-4 rounded-lg shadow hover:opacity-90 transition">
            <p class="text-xs font-semibold opacity-90">ইঞ্জিনিয়ার ভিজিটেড</p>
            <h4 id="comp_count_visited" class="text-2xl font-bold mt-1">0</h4>
        </div>
        <div onclick="filterData('completed')" style="background-color: #16a34a; color: #ffffff;"
            class="cursor-pointer p-4 rounded-lg shadow hover:opacity-90 transition">
            <p class="text-xs font-semibold opacity-90">কমপ্লিট হয়েছে</p>
            <h4 id="comp_count_completed" class="text-2xl font-bold mt-1">0</h4>
        </div>
    </div>

    <!-- Auto OCR Scan Attachment -->
    <div class="bg-white p-5 rounded-xl border border-blue-200 shadow-sm">
        <h3 class="text-lg font-bold text-blue-700 mb-3 flex items-center gap-2">📄 পারচেজ বিল / ইনভয়েস অটো-স্ক্যান
        </h3>
        <div class="flex flex-col sm:flex-row items-center gap-4">
            <input type="file" id="invoiceFile" accept="image/*,application/pdf"
                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
            <button type="button" id="scanBtn" onclick="scanBill(event)"
                class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-medium shadow text-sm whitespace-nowrap">🔍
                আপলোড ও অটো-স্ক্যান</button>
        </div>
    </div>

    <!-- Form -->
    <form id="companyBookingForm" class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-4">
        <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">কোম্পানি কল বুকিং ফর্ম</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2 bg-blue-50 p-3 rounded-lg border border-blue-200">
                <label class="block text-xs font-bold text-blue-800 mb-1">ফোন নম্বর</label>
                <input type="text" id="comp_phone" maxlength="10" inputmode="numeric" placeholder="মোবাইল নম্বর"
                    required
                    class="w-full border rounded-lg p-2 text-sm bg-white focus:ring-2 focus:ring-blue-400 outline-none">
                <p id="customerFetchStatus" class="text-xs mt-1 text-gray-500"></p>
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">কাস্টমারের নাম</label><input type="text"
                    id="comp_name" maxlength="255" autocomplete="name" placeholder="কাস্টমার নাম" required
                    class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-400 outline-none"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">কাস্টমারের অ্যাড্রেস</label><input
                    type="text" id="comp_address" maxlength="500" autocomplete="street-address" placeholder="ঠিকানা"
                    required class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-400 outline-none">
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">পিন কোড নম্বর</label><input type="text"
                    id="comp_pincode" maxlength="6" inputmode="numeric" pattern="[0-9]{6}" placeholder="741156" required
                    class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-400 outline-none"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">১) বিল/ইনভয়েস অ্যাটাচ করার
                    তারিখ</label><input type="date" id="comp_bill_date" value="{{ date('Y-m-d') }}" required
                    class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-400 outline-none"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">২) কল বুকিং করার তারিখ</label><input
                    type="date" id="comp_booking_date" value="{{ date('Y-m-d') }}" required readonly
                    class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-400 outline-none"></div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">প্রোডাক্ট ও মডেল (১ম ইউনিট)</label>
                <select id="comp_product"
                    class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-400 outline-none">
                    <option value="">-- SL No নির্বাচন করুন --</option>
                </select>
                <input type="hidden" id="selectedProduct" name="product_id">
                <input type="text" id="comp_product_manual" maxlength="255"
                    placeholder="তালিকায় না থাকলে প্রোডাক্ট/মডেল লিখুন"
                    class="hidden w-full border border-orange-300 rounded-lg p-2 text-sm mt-2 bg-orange-50 focus:ring-2 focus:ring-orange-400 outline-none">
            </div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">সিরিয়াল নম্বর (১ম ইউনিট)</label><input
                    type="text" id="comp_serial" maxlength="255" placeholder="S/N Number" required
                    class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-400 outline-none"></div>
            <div id="secondProductWrap"
                class="hidden md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 border-t border-dashed pt-4 mt-1">
                <div><label class="block text-xs font-semibold text-gray-600 mb-1">প্রোডাক্ট ও মডেল (২য়
                        ইউনিট)</label><select id="comp_product_2"
                        class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-400 outline-none">
                        <option value="">-- SL No নির্বাচন করুন --</option>
                    </select><input type="hidden" id="selectedProduct_2" name="product_id_2"><input type="text"
                        id="comp_product_2_manual" maxlength="255" placeholder="তালিকায় না থাকলে প্রোডাক্ট/মডেল লিখুন"
                        class="hidden w-full border border-orange-300 rounded-lg p-2 text-sm mt-2 bg-orange-50 focus:ring-2 focus:ring-orange-400 outline-none">
                </div>
                <div><label class="block text-xs font-semibold text-gray-600 mb-1">সিরিয়াল নম্বর (২য়
                        ইউনিট)</label><input type="text" id="comp_serial_2" maxlength="255" placeholder="S/N Number"
                        class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-400 outline-none">
                </div>
            </div>
            {{-- <div><label class="block text-xs font-semibold text-gray-600 mb-1">কেস আইডি নম্বর (১ম ইউনিট,
                    অপশনাল)</label><input type="text" id="comp_case_1" maxlength="255" placeholder="প্রথম কেস আইডি"
                    class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-400 outline-none"></div>
            <div><label class="block text-xs font-semibold text-gray-600 mb-1">কেস আইডি নম্বর (২য় ইউনিট,
                    অপশনাল)</label><input type="text" id="comp_case_2" maxlength="255" placeholder="দ্বিতীয় কেস আইডি"
                    class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-400 outline-none"></div> --}}
        </div>
        <div><label class="block text-xs font-semibold text-gray-600 mb-1">রিমার্কস (Remarks)</label><textarea
                id="comp_remarks" maxlength="1000" rows="2" placeholder="অতিরিক্ত মন্তব্য..."
                class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-400 outline-none"></textarea>
        </div>
        <div id="bookingSubmitStatus" class="hidden text-sm font-semibold rounded-lg p-3"></div>
        <div class="text-right pt-2"><button type="submit" id="bookingSubmitBtn"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2.5 rounded-lg shadow-md text-sm transition disabled:opacity-60">💾
                কল বুকিং সেভ করুন</button></div>
    </form>
</div>