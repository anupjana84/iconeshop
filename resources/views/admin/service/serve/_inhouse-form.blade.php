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
    <form onsubmit="saveInhouseService(event)"
         data-store-url="{{ route('inhouse.service.store') }}"
    class="bg-white p-6 rounded-xl border border-green-200 shadow-sm space-y-4">

        <h3 class="text-lg font-bold text-green-600 border-b pb-2 mb-4">🛠️ নিজস্ব সার্ভিস ফর্ম (In-House Repairing)</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2 bg-green-50 p-3 rounded-lg border border-green-200">
                <label class="block text-xs font-bold text-green-800 mb-1">ফোন নম্বর (অটো কাস্টমার লোড হবে)</label>
                <input type="text" id="inh_phone" maxlength="10" inputmode="numeric" autocomplete="tel" placeholder="মোবাইল নম্বর লিখুন..." required class="w-full border rounded-lg p-2 text-sm bg-white focus:ring-2 focus:ring-green-400 outline-none">
                <p id="inh_phone_status" class="text-xs mt-1 text-gray-500"></p>
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
                <input type="date" id="inh_receive_date" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" required class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-green-400 outline-none">
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
