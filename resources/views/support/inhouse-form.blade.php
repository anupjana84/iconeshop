<div id="inhouseFormTemplate" class="hidden">

    <div class="space-y-6">

        {{-- Dashboard --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

            <div
                onclick="filterData('all')"
                style="background:#2563eb;color:white"
                class="cursor-pointer p-4 rounded-lg shadow"
            >
                <p class="text-xs font-semibold">
                    মোট সার্ভিস
                </p>

                <h4 id="inh_count_total" class="text-2xl font-bold">
                    0
                </h4>
            </div>


            <div
                onclick="filterData('pending')"
                style="background:#d97706;color:white"
                class="cursor-pointer p-4 rounded-lg shadow"
            >
                <p class="text-xs font-semibold">
                    পেন্ডিং
                </p>

                <h4 id="inh_count_pending" class="text-2xl font-bold">
                    0
                </h4>
            </div>


            <div
                onclick="filterData('ready')"
                style="background:#9333ea;color:white"
                class="cursor-pointer p-4 rounded-lg shadow"
            >
                <p class="text-xs font-semibold">
                    কাজ কমপ্লিট
                </p>

                <h4 id="inh_count_ready" class="text-2xl font-bold">
                    0
                </h4>
            </div>


            <div
                onclick="filterData('delivered')"
                style="background:#16a34a;color:white"
                class="cursor-pointer p-4 rounded-lg shadow"
            >
                <p class="text-xs font-semibold">
                    ডেলিভারি
                </p>

                <h4 id="inh_count_delivered" class="text-2xl font-bold">
                    0
                </h4>
            </div>

        </div>


        {{-- Form --}}
        <form
            onsubmit="saveInhouseService(event)"
            class="bg-white p-6 rounded-xl border border-green-200 shadow-sm space-y-4"
        >

            <h3 class="text-lg font-bold text-green-600 border-b pb-2 mb-4">
                🛠️ নিজস্ব সার্ভিস ফর্ম
            </h3>


            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div class="md:col-span-2 bg-green-50 p-3 rounded-lg">

                    <label class="block text-xs font-bold text-green-800 mb-1">
                        ফোন নম্বর
                    </label>

                    <input
                        type="text"
                        id="inh_phone"
                        maxlength="10"
                        oninput="searchCustomer(this.value,'inh')"
                        placeholder="১০ ডিজিট মোবাইল নম্বর"
                        required
                        class="w-full border rounded-lg p-2 text-sm"
                    >

                </div>


                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        কাস্টমার নাম
                    </label>

                    <input
                        type="text"
                        id="inh_name"
                        required
                        class="w-full border rounded-lg p-2 text-sm"
                    >
                </div>


                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        বিকল্প ফোন
                    </label>

                    <input
                        type="text"
                        id="inh_alt_phone"
                        class="w-full border rounded-lg p-2 text-sm"
                    >
                </div>


                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        কাস্টমার এড্রেস
                    </label>

                    <input
                        type="text"
                        id="inh_address"
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
                        id="inh_pincode"
                        maxlength="6"
                        required
                        class="w-full border rounded-lg p-2 text-sm"
                    >
                </div>


                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        প্রোডাক্ট ও প্রবলেম
                    </label>

                    <input
                        type="text"
                        id="inh_product"
                        placeholder="যেমন: Epson L3110 Paper Jam"
                        required
                        class="w-full border rounded-lg p-2 text-sm"
                    >
                </div>


                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        সম্ভাব্য এস্টিমেট
                    </label>

                    <input
                        type="number"
                        id="inh_estimate"
                        class="w-full border rounded-lg p-2 text-sm"
                    >
                </div>


                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        ফাইনাল এমাউন্ট
                    </label>

                    <input
                        type="number"
                        id="inh_final_amount"
                        class="w-full border rounded-lg p-2 text-sm"
                    >
                </div>


                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        জমা দেওয়ার তারিখ
                    </label>

                    <input
                        type="date"
                        id="inh_receive_date"
                        value="{{ date('Y-m-d') }}"
                        required
                        class="w-full border rounded-lg p-2 text-sm"
                    >
                </div>


                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        ডেলিভারি তারিখ
                    </label>

                    <input
                        type="date"
                        id="inh_delivery_date"
                        class="w-full border rounded-lg p-2 text-sm"
                    >
                </div>


                <div>

                    <label class="block text-xs font-semibold text-gray-600 mb-2">
                        ওয়ারেন্টি
                    </label>

                    <label class="mr-5">

                        <input
                            type="radio"
                            name="inh_warranty"
                            value="In Warranty"
                        >

                        ওয়ারেন্টি

                    </label>


                    <label>

                        <input
                            type="radio"
                            name="inh_warranty"
                            value="Out of Warranty"
                            checked
                        >

                        উইদাউট ওয়ারেন্টি

                    </label>

                </div>

            </div>


            <div class="text-right pt-4">

                <button
                    type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white font-bold px-6 py-2.5 rounded-lg shadow-md"
                >
                    💾 ইন-হাউস সার্ভিস রেকর্ড সেভ করুন
                </button>

            </div>

        </form>

    </div>

</div>