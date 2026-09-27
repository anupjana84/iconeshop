<div id="externalFormTemplate" class="hidden">

    <div class="space-y-6">

        {{-- Dashboard --}}
        <div class="grid grid-cols-2 md:grid-cols-6 gap-3">

            <div
                onclick="filterData('all')"
                style="background:#2563eb;color:white"
                class="cursor-pointer p-3 rounded-lg shadow"
            >
                <p class="text-[11px] font-semibold">
                    মোট সার্ভিস
                </p>
                <h4 id="ext_count_total" class="text-xl font-bold">
                    0
                </h4>
            </div>


            <div
                onclick="filterData('pending')"
                style="background:#d97706;color:white"
                class="cursor-pointer p-3 rounded-lg shadow"
            >
                <p class="text-[11px] font-semibold">
                    পেন্ডিং
                </p>
                <h4 id="ext_count_pending" class="text-xl font-bold">
                    0
                </h4>
            </div>


            <div
                onclick="filterData('sent')"
                style="background:#ea580c;color:white"
                class="cursor-pointer p-3 rounded-lg shadow"
            >
                <p class="text-[11px] font-semibold">
                    পাঠানো হয়েছে
                </p>
                <h4 id="ext_count_sent" class="text-xl font-bold">
                    0
                </h4>
            </div>


            <div
                onclick="filterData('back')"
                style="background:#4f46e5;color:white"
                class="cursor-pointer p-3 rounded-lg shadow"
            >
                <p class="text-[11px] font-semibold">
                    ফিরে এসেছে
                </p>
                <h4 id="ext_count_back" class="text-xl font-bold">
                    0
                </h4>
            </div>


            <div
                onclick="filterData('ready')"
                style="background:#9333ea;color:white"
                class="cursor-pointer p-3 rounded-lg shadow"
            >
                <p class="text-[11px] font-semibold">
                    ডেলিভারি বাকি
                </p>
                <h4 id="ext_count_ready" class="text-xl font-bold">
                    0
                </h4>
            </div>


            <div
                onclick="filterData('delivered')"
                style="background:#16a34a;color:white"
                class="cursor-pointer p-3 rounded-lg shadow"
            >
                <p class="text-[11px] font-semibold">
                    ডেলিভারি সম্পন্ন
                </p>
                <h4 id="ext_count_delivered" class="text-xl font-bold">
                    0
                </h4>
            </div>

        </div>


        <form
            onsubmit="saveExternalService(event)"
            class="bg-white p-6 rounded-xl border border-orange-200 shadow-sm space-y-4"
        >

            <h3 class="text-lg font-bold text-orange-600 border-b pb-2 mb-4">
                🚚 বাইরে থেকে সার্ভিস ফর্ম
            </h3>


            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                <div class="md:col-span-2 bg-orange-50 p-3 rounded-lg">

                    <label class="block text-xs font-bold text-orange-800 mb-1">
                        কাস্টমার ফোন
                    </label>

                    <input
                        type="text"
                        id="ext_phone"
                        maxlength="10"
                        oninput="searchCustomer(this.value,'ext')"
                        placeholder="১০ ডিজিট মোবাইল নম্বর"
                        required
                        class="w-full border rounded-lg p-2 text-sm"
                    >

                </div>


                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        কাস্টমারের নাম
                    </label>

                    <input
                        type="text"
                        id="ext_name"
                        required
                        class="w-full border rounded-lg p-2 text-sm"
                    >
                </div>


                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        কাস্টমারের এড্রেস
                    </label>

                    <input
                        type="text"
                        id="ext_address"
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
                        id="ext_pincode"
                        maxlength="6"
                        required
                        class="w-full border rounded-lg p-2 text-sm"
                    >
                </div>


                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        প্রোডাক্ট নাম ও মডেল
                    </label>

                    <input
                        type="text"
                        id="ext_product"
                        required
                        class="w-full border rounded-lg p-2 text-sm"
                    >
                </div>


                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        সিরিয়াল নাম্বার
                    </label>

                    <input
                        type="text"
                        id="ext_serial"
                        class="w-full border rounded-lg p-2 text-sm"
                    >
                </div>


                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        সম্ভাব্য খরচ
                    </label>

                    <input
                        type="number"
                        id="ext_budget"
                        class="w-full border rounded-lg p-2 text-sm"
                    >
                </div>


                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        ফাইনাল চার্জ
                    </label>

                    <input
                        type="number"
                        id="ext_final_cost"
                        class="w-full border rounded-lg p-2 text-sm"
                    >
                </div>


                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        জমা নেওয়ার তারিখ
                    </label>

                    <input
                        type="date"
                        id="ext_receive_date"
                        value="{{ date('Y-m-d') }}"
                        required
                        class="w-full border rounded-lg p-2 text-sm"
                    >
                </div>


                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        সার্ভিস ভেন্ডর
                    </label>

                    <input
                        list="vendorList"
                        id="ext_vendor"
                        placeholder="ভেন্ডরের নাম"
                        class="w-full border rounded-lg p-2 text-sm"
                    >

                    <datalist id="vendorList">
                        <option value="Canon Service Center">
                        <option value="HP Service Hub">
                        <option value="TVS Service Point">
                        <option value="Hitachi Care">
                    </datalist>
                </div>


                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        পাঠানোর তারিখ
                    </label>

                    <input
                        type="date"
                        id="ext_sent_date"
                        class="w-full border rounded-lg p-2 text-sm"
                    >
                </div>


                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        ফেরত আসার তারিখ
                    </label>

                    <input
                        type="date"
                        id="ext_back_date"
                        class="w-full border rounded-lg p-2 text-sm"
                    >
                </div>


                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        ডেলিভারি তারিখ
                    </label>

                    <input
                        type="date"
                        id="ext_delivery_date"
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
                            name="ext_warranty"
                            value="In Warranty"
                            checked
                        >
                        ওয়ারেন্টি
                    </label>

                    <label>
                        <input
                            type="radio"
                            name="ext_warranty"
                            value="Out of Warranty"
                        >
                        উইদাউট ওয়ারেন্টি
                    </label>

                </div>

            </div>


            <div class="text-right pt-4">

                <button
                    type="submit"
                    class="bg-orange-600 hover:bg-orange-700 text-white font-bold px-6 py-2.5 rounded-lg shadow-md"
                >
                    💾 বাইরের সার্ভিস রেকর্ড সেভ করুন
                </button>

            </div>

        </form>

    </div>

</div>