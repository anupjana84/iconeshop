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
                <p id="ext_phone_status" class="text-xs mt-1 text-gray-500"></p>
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
            <div class="md:col-span-2">
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
