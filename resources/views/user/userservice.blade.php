@php
    $page_title = 'Service Settings';
@endphp

@extends(auth()->user()->role === 'user' ? 'layouts.user' : 'layouts.main')

@section('content_page')

    <div class="max-w-7xl mx-auto bg-white p-6 rounded-xl shadow-lg">

        <!-- PAGE HEADER -->
        <h2 class="text-2xl font-bold mb-6 text-gray-800 border-b pb-3 flex justify-between items-center">
            <span>🛠️ সার্ভিস সেটিং (Service Settings Hub)</span>

            <span class="text-xs font-normal bg-blue-100 text-blue-800 px-3 py-1 rounded-full">
                Icon Computer Management
            </span>
        </h2>


        <!-- COMPANY SERVICE ONLY -->
        <div class="space-y-6">

            <!-- DASHBOARD DESIGN -->



            @if(auth()->user()->role !== 'user')
            <!-- IMAGE / PDF OCR SCAN -->
            <div class="bg-white p-5 rounded-xl border border-blue-200 shadow-sm">

                <h3 class="text-lg font-bold text-blue-700 mb-3 flex items-center gap-2">
                    📄 পারচেজ বিল / ইনভয়েস অটো-স্ক্যান
                </h3>

                <div class="flex flex-col sm:flex-row items-center gap-4">

                    <input type="file" id="invoiceFile" accept="image/*,application/pdf" class="block w-full text-sm text-gray-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-lg file:border-0
                        file:text-sm file:font-semibold
                        file:bg-blue-50 file:text-blue-700
                        hover:file:bg-blue-100 cursor-pointer">

                    <button type="button" id="scanBtn" onclick="scanBill(event)" class="bg-blue-600 hover:bg-blue-700 text-white
                        px-5 py-2 rounded-lg font-medium shadow
                        text-sm whitespace-nowrap">
                        🔍 আপলোড ও অটো-স্ক্যান
                    </button>

                </div>

            </div>
            @endif


            <!-- COMPANY FORM -->
            <form id="companyBookingForm" class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm space-y-4">

                <h3 class="text-lg font-bold text-gray-800 border-b pb-2 mb-4">
                    কোম্পানি কল বুকিং ফর্ম
                </h3>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <!-- PHONE -->
                    <div class="md:col-span-2 bg-blue-50 p-3 rounded-lg border border-blue-200">

                        <label class="block text-xs font-bold text-blue-800 mb-1">
                            ফোন নম্বর
                        </label>

                        <input type="text" id="comp_phone" maxlength="10" inputmode="numeric" placeholder="মোবাইল নম্বর"
                            value="{{ auth()->user()->phone ?? '' }}" readonly
                            required class="w-full border rounded-lg p-2 text-sm bg-gray-100 text-gray-700 cursor-not-allowed outline-none font-semibold">

                        <p id="customerFetchStatus" class="text-xs mt-1 text-gray-500"></p>

                    </div>


                    <!-- CUSTOMER NAME -->
                    <div>

                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            কাস্টমারের নাম
                        </label>

                        <input type="text" id="comp_name" placeholder="কাস্টমার নাম"
                            value="{{ $customer->name ?? auth()->user()->name ?? '' }}"
                            required class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-400 outline-none">

                    </div>


                    <!-- ADDRESS -->
                    <div>

                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            কাস্টমারের অ্যাড্রেস
                        </label>

                        <input type="text" id="comp_address" placeholder="ঠিকানা"
                            value="{{ $customer->address ?? '' }}"
                            required class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-400 outline-none">

                    </div>


                    <!-- PINCODE -->
                    <div>

                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            পিন কোড নম্বর
                        </label>

                        <input type="text" id="comp_pincode" maxlength="6" placeholder="741156"
                            value="{{ $customer->pin ?? $customer->pincode ?? '' }}"
                            required class="w-full border rounded-lg p-2 text-sm focus:ring-2 focus:ring-blue-400 outline-none">

                    </div>


                    <!-- BILL DATE -->
                    <div>

                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            ১) বিল/ইনভয়েস অ্যাটাচ করার তারিখ
                        </label>

                        <input type="date" id="comp_bill_date" value="{{ date('Y-m-d') }}" required class="w-full border rounded-lg p-2 text-sm
                            focus:ring-2 focus:ring-blue-400 outline-none">

                    </div>


                    <!-- BOOKING DATE -->
                    <div>

                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            ২) কল বুকিং করার তারিখ
                        </label>

                        <input type="date" id="comp_booking_date" value="{{ date('Y-m-d') }}" required readonly class="w-full border rounded-lg p-2 text-sm
                            focus:ring-2 focus:ring-blue-400 outline-none">

                    </div>


                    <!-- PRODUCT -->
                    <div>

                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            প্রোডাক্ট ও মডেল (১ম ইউনিট)
                        </label>

                        <select id="comp_product" class="w-full border rounded-lg p-2 text-sm
                            focus:ring-2 focus:ring-blue-400 outline-none">
                            <option value="">
                                -- SL No নির্বাচন করুন --
                            </option>
                        </select>

                        <!-- PRODUCT ID -->
                        <input type="hidden" id="selectedProduct" name="product_id">

                        <!-- ⭐ FALLBACK MANUAL ENTRY (dropdown-এ না থাকলে) -->
                        <input type="text" id="comp_product_manual"
                            placeholder="উপরের তালিকায় না থাকলে এখানে প্রোডাক্ট/মডেল লিখুন" class="hidden w-full border border-orange-300 rounded-lg p-2 text-sm mt-2
                            bg-orange-50 focus:ring-2 focus:ring-orange-400 outline-none">

                    </div>


                    <!-- SERIAL -->
                    <div>

                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            সিরিয়াল নম্বর (১ম ইউনিট)
                        </label>

                        <input type="text" id="comp_serial" placeholder="S/N Number" required class="w-full border rounded-lg p-2 text-sm
                            focus:ring-2 focus:ring-blue-400 outline-none">

                    </div>


                    <!-- ⭐ SECOND PRODUCT (AC ইত্যাদির ক্ষেত্রে একই বিলে দুটো ইউনিট থাকলে) -->
                    <div id="secondProductWrap" class="hidden md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4
                        border-t border-dashed pt-4 mt-1">

                        <div>

                            <label class="block text-xs font-semibold text-gray-600 mb-1">
                                প্রোডাক্ট ও মডেল (২য় ইউনিট)
                            </label>

                            <select id="comp_product_2" class="w-full border rounded-lg p-2 text-sm
                                focus:ring-2 focus:ring-blue-400 outline-none">
                                <option value="">
                                    -- SL No নির্বাচন করুন --
                                </option>
                            </select>

                            <input type="hidden" id="selectedProduct_2" name="product_id_2">

                            <!-- ⭐ FALLBACK MANUAL ENTRY (dropdown-এ না থাকলে) -->
                            <input type="text" id="comp_product_2_manual"
                                placeholder="উপরের তালিকায় না থাকলে এখানে প্রোডাক্ট/মডেল লিখুন" class="hidden w-full border border-orange-300 rounded-lg p-2 text-sm mt-2
                                bg-orange-50 focus:ring-2 focus:ring-orange-400 outline-none">

                        </div>

                        <div>

                            <label class="block text-xs font-semibold text-gray-600 mb-1">
                                সিরিয়াল নম্বর (২য় ইউনিট)
                            </label>

                            <input type="text" id="comp_serial_2" placeholder="S/N Number" class="w-full border rounded-lg p-2 text-sm
                                focus:ring-2 focus:ring-blue-400 outline-none">

                        </div>

                    </div>


                    {{--
                    <!-- CASE ID 1 -->
                    <div>

                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            কেস আইডি নম্বর (১ম ইউনিট, অপশনাল)
                        </label>

                        <input type="text" id="comp_case_1" placeholder="প্রথম কেস আইডি" class="w-full border rounded-lg p-2 text-sm
                            focus:ring-2 focus:ring-blue-400 outline-none">

                    </div>


                    <!-- CASE ID 2 -->
                    <div>

                        <label class="block text-xs font-semibold text-gray-600 mb-1">
                            কেস আইডি নম্বর (২য় ইউনিট, অপশনাল)
                        </label>

                        <input type="text" id="comp_case_2" placeholder="দ্বিতীয় কেস আইডি" class="w-full border rounded-lg p-2 text-sm
                            focus:ring-2 focus:ring-blue-400 outline-none">

                    </div>
                    --}}

                </div>


                <!-- REMARKS -->
                <div>

                    <label class="block text-xs font-semibold text-gray-600 mb-1">
                        রিমার্কস (Remarks)
                    </label>

                    <textarea id="comp_remarks" rows="2" placeholder="অতিরিক্ত মন্তব্য..." class="w-full border rounded-lg p-2 text-sm
                        focus:ring-2 focus:ring-blue-400 outline-none"></textarea>

                </div>


                <!-- ⭐ SUBMIT STATUS MESSAGE -->
                <div id="bookingSubmitStatus" class="hidden text-sm font-semibold rounded-lg p-3"></div>


                <!-- SUBMIT -->
                <div class="text-right pt-2">

                    <button type="submit" id="bookingSubmitBtn" class="bg-blue-600 hover:bg-blue-700 text-white
                        font-bold px-6 py-2.5 rounded-lg shadow-md
                        text-sm transition disabled:opacity-60">
                        💾 কল বুকিং সেভ করুন
                    </button>

                </div>

            </form>

        </div>

    </div>


    <!-- OCR LIBRARIES -->
    <script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>


    <script>

        /*
        |--------------------------------------------------------------------------
        | GET ELEMENTS
        |--------------------------------------------------------------------------
        */

        const phoneInput =
            document.getElementById('comp_phone');

        const productSelect =
            document.getElementById('comp_product');

        const serialInput =
            document.getElementById('comp_serial');

        const selectedProduct =
            document.getElementById('selectedProduct');

        // ⭐ SECOND UNIT ELEMENTS
        const productSelect2 =
            document.getElementById('comp_product_2');

        const serialInput2 =
            document.getElementById('comp_serial_2');

        const selectedProduct2 =
            document.getElementById('selectedProduct_2');

        const secondProductWrap =
            document.getElementById('secondProductWrap');

        // ⭐ FALLBACK MANUAL ENTRY ELEMENTS
        const productManual =
            document.getElementById('comp_product_manual');

        const productManual2 =
            document.getElementById('comp_product_2_manual');

        const customerFetchStatus =
            document.getElementById('customerFetchStatus');


        /*
        |--------------------------------------------------------------------------
        | ⭐ TOGGLE FALLBACK INPUT VISIBILITY
        |--------------------------------------------------------------------------
        | dropdown-এ কোনো option (ফাঁকা "-- নির্বাচন করুন --" ছাড়া) না থাকলে,
        | manual fallback input দেখানো হবে যাতে ইউজার নিজে হাতে লিখতে পারে।
        |--------------------------------------------------------------------------
        */

        function toggleProductFallback(selectEl, fallbackEl) {

            // dropdown-এ শুধু প্লেসহোল্ডার option (value="") ছাড়া আর কিছু আছে কিনা চেক
            const hasRealOptions =
                Array.from(selectEl.options)
                    .some(opt => opt.value !== '');

            if (hasRealOptions) {

                fallbackEl.classList.add('hidden');

                fallbackEl.value = '';

            } else {

                fallbackEl.classList.remove('hidden');

            }

        }


        /*
        |--------------------------------------------------------------------------
        | ⭐ পেজ লোড হওয়ার সাথে সাথেই ১ম ইউনিটের fallback দেখাও
        |--------------------------------------------------------------------------
        | শুরুতে কোনো ফোন নম্বর দেওয়া হয়নি, তাই dropdown ফাঁকা —
        | ইউজার চাইলে সরাসরি ম্যানুয়ালি প্রোডাক্ট নাম লিখেই শুরু করতে পারবে।
        | (২য় ইউনিট ব্লক নিজেই hidden থাকে যতক্ষণ না ২টা+ sale item পাওয়া যায়,
        | তাই তার fallback আপাতত দেখানোর দরকার নেই।)
        |--------------------------------------------------------------------------
        */

        toggleProductFallback(productSelect, productManual);


        /*
        |--------------------------------------------------------------------------
        | ⭐ AUTO LOAD LOGGED-IN USER CUSTOMER DATA & PRODUCTS
        |--------------------------------------------------------------------------
        */
        const initialUserPhone = "{{ auth()->user()->phone ?? '' }}";
        if (initialUserPhone && initialUserPhone.length === 10) {
            fetchCustomer(initialUserPhone);
        }


        /*
        |--------------------------------------------------------------------------
        | PHONE INPUT
        |--------------------------------------------------------------------------
        */

        phoneInput.addEventListener('input', function () {

            // শুধু number রাখবে
            this.value =
                this.value.replace(/\D/g, '');

            const phone =
                this.value;


            // 10 digit complete হলে customer fetch
            if (phone.length === 10) {

                fetchCustomer(phone);

            }

        });


        /*
        |--------------------------------------------------------------------------
        | PRODUCT SELECT -> SERIAL NUMBER AUTO-FILL (১ম ইউনিট)
        |--------------------------------------------------------------------------
        | Product select করলে:
        |
        | 1. product_id -> selectedProduct (hidden input)
        | 2. serial_no  -> comp_serial     (visible input)
        |
        | ⭐ FIX: dataset.serialNo খালি থাকলে option value (sl_no)
        | fallback হিসেবে ব্যবহার করা হচ্ছে, যাতে serial ফাঁকা না থাকে।
        |--------------------------------------------------------------------------
        */

        productSelect.addEventListener('change', function () {

            const selectedOption =
                this.options[this.selectedIndex];


            /*
            |--------------------------------------------------------------------------
            | Nothing selected
            |--------------------------------------------------------------------------
            */

            if (
                !selectedOption ||
                !selectedOption.value
            ) {

                serialInput.value = '';

                selectedProduct.value = '';

                return;

            }


            /*
            |--------------------------------------------------------------------------
            | PRODUCT ID
            |--------------------------------------------------------------------------
            */

            selectedProduct.value =
                selectedOption.dataset.productId || '';


            /*
            |--------------------------------------------------------------------------
            | ⭐ SERIAL NUMBER (with fallback to sl_no)
            |--------------------------------------------------------------------------
            */

            serialInput.value =
                selectedOption.dataset.serialNo ||
                selectedOption.value ||
                '';


            /*
            |--------------------------------------------------------------------------
            | DEBUG
            |--------------------------------------------------------------------------
            */

            // console.log(
            //     'Selected SL No (1):',
            //     selectedOption.value
            // );

            // console.log(
            //     'Selected Product ID (1):',
            //     selectedProduct.value
            // );

            // console.log(
            //     'Selected Serial No (1):',
            //     serialInput.value
            // );

        });


        /*
        |--------------------------------------------------------------------------
        | ⭐ PRODUCT SELECT -> SERIAL NUMBER AUTO-FILL (২য় ইউনিট)
        |--------------------------------------------------------------------------
        | একই লজিক, কিন্তু দ্বিতীয় dropdown/ইনপুটের জন্য।
        | AC-এর মতো ক্যাটাগরিতে এক বিলে দুটো ইউনিট থাকলে এটা কাজে লাগে।
        |--------------------------------------------------------------------------
        */

        productSelect2.addEventListener('change', function () {

            const selectedOption =
                this.options[this.selectedIndex];


            if (
                !selectedOption ||
                !selectedOption.value
            ) {

                serialInput2.value = '';

                selectedProduct2.value = '';

                return;

            }


            selectedProduct2.value =
                selectedOption.dataset.productId || '';


            serialInput2.value =
                selectedOption.dataset.serialNo ||
                selectedOption.value ||
                '';


            // console.log(
            //     'Selected SL No (2):',
            //     selectedOption.value
            // );

            // console.log(
            //     'Selected Product ID (2):',
            //     selectedProduct2.value
            // );

            // console.log(
            //     'Selected Serial No (2):',
            //     serialInput2.value
            // );

        });


        /*
        |--------------------------------------------------------------------------
        | FETCH CUSTOMER
        |--------------------------------------------------------------------------
        */

        async function fetchCustomer(phone) {

            customerFetchStatus.innerText =
                '⏳ Customer খোঁজা হচ্ছে...';

            customerFetchStatus.className =
                'text-xs mt-1 text-blue-600';


            try {

                const response =
                    await fetch(
                        `/admin/customer/by-phone?phone=${encodeURIComponent(phone)}`,
                        {
                            method: 'GET',

                            headers: {
                                'Accept': 'application/json'
                            }
                        }
                    );


                const data =
                    await response.json();


                // console.log(
                //     'Customer response:',
                //     data
                // );


                /*
                |--------------------------------------------------------------------------
                | CUSTOMER FOUND
                |--------------------------------------------------------------------------
                */

                if (
                    data.success &&
                    data.customer
                ) {

                    const customer =
                        data.customer;


                    /*
                    |--------------------------------------------------------------------------
                    | NAME
                    |--------------------------------------------------------------------------
                    */

                    if (customer.name) {

                        document.getElementById(
                            'comp_name'
                        ).value =
                            customer.name;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | ADDRESS
                    |--------------------------------------------------------------------------
                    */

                    if (customer.address) {

                        document.getElementById(
                            'comp_address'
                        ).value =
                            customer.address;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | PINCODE
                    |--------------------------------------------------------------------------
                    */

                    if (customer.pincode) {

                        document.getElementById(
                            'comp_pincode'
                        ).value =
                            customer.pincode;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | BILL DATE
                    |--------------------------------------------------------------------------
                    */

                    const billDate =
                        document.getElementById(
                            'comp_bill_date'
                        );


                    if (
                        customer?.sale?.created_at
                    ) {

                        billDate.value =
                            customer.sale.created_at
                                .split('T')[0];

                    } else {

                        billDate.value =
                            new Date()
                                .toISOString()
                                .split('T')[0];

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | RESET PRODUCT DROPDOWN (১ম ও ২য় দুটোই)
                    |--------------------------------------------------------------------------
                    */

                    productSelect.innerHTML =
                        '<option value="">-- Product নির্বাচন করুন --</option>';

                    productSelect2.innerHTML =
                        '<option value="">-- SL No নির্বাচন করুন --</option>';


                    /*
                    |--------------------------------------------------------------------------
                    | RESET SERIAL (১ম ও ২য়)
                    |--------------------------------------------------------------------------
                    */

                    serialInput.value = '';

                    serialInput2.value = '';


                    /*
                    |--------------------------------------------------------------------------
                    | RESET PRODUCT ID (১ম ও ২য়)
                    |--------------------------------------------------------------------------
                    */

                    selectedProduct.value = '';

                    selectedProduct2.value = '';


                    /*
                    |--------------------------------------------------------------------------
                    | ⭐ দ্বিতীয় ইউনিট ব্লক ডিফল্টভাবে লুকানো থাকবে
                    |--------------------------------------------------------------------------
                    */

                    secondProductWrap.classList.add('hidden');


                    /*
                    |--------------------------------------------------------------------------
                    | SALE ITEMS
                    |--------------------------------------------------------------------------
                    */

                    if (
                        customer.saleitems &&
                        customer.saleitems.length > 0
                    ) {


                        customer.saleitems.forEach(
                            (item) => {

                                const brandName =
                                    item?.product?.brand?.name || '';


                                /*
                                |--------------------------------------------------------------------------
                                | ১ম dropdown-এর option
                                |--------------------------------------------------------------------------
                                */

                                const option1 =
                                    document.createElement(
                                        'option'
                                    );

                                option1.value =
                                    item.sl_no || '';

                                option1.textContent =
                                    `${brandName} ${item.sl_no || ''}`;

                                option1.dataset.productId =
                                    item.product_id || '';

                                option1.dataset.serialNo =
                                    item.serial_no || '';

                                if (item.id) {

                                    option1.dataset.saleItemId =
                                        item.id;

                                }

                                productSelect.appendChild(
                                    option1
                                );


                                /*
                                |--------------------------------------------------------------------------
                                | ⭐ ২য় dropdown-এর জন্য আলাদা option (একই ডেটা, আলাদা element)
                                |--------------------------------------------------------------------------
                                */

                                const option2 =
                                    document.createElement(
                                        'option'
                                    );

                                option2.value =
                                    item.sl_no || '';

                                option2.textContent =
                                    `${brandName} ${item.sl_no || ''}`;

                                option2.dataset.productId =
                                    item.product_id || '';

                                option2.dataset.serialNo =
                                    item.serial_no || '';

                                if (item.id) {

                                    option2.dataset.saleItemId =
                                        item.id;

                                }

                                productSelect2.appendChild(
                                    option2
                                );

                            }
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | ⭐ AUTO-SELECT LOGIC
                        |--------------------------------------------------------------------------
                        | - ঠিক ১টা item থাকলে: ১ম dropdown auto-select, ২য়টা hidden থাকবে
                        | - ১টার বেশি item থাকলে (যেমন AC-তে দুটো ইউনিট): ২য় dropdown
                        |   ব্লকটা visible হবে, এবং ১ম ও ২য় item আলাদাভাবে auto-select হবে
                        |--------------------------------------------------------------------------
                        */

                        var acFound = false;
                        customer.saleitems.forEach(function (item) {
                            var brandName = (item.product && item.product.brand && item.product.brand.name) || '';
                            var catName = (item.product && item.product.category && item.product.category.name) || '';
                            var modelName = (item.product && (item.product.name || item.product.model)) || '';
                            var fullText = (brandName + ' ' + catName + ' ' + modelName + ' ' + (item.sl_no || '')).trim();

                            if (/(\bAC\b|AIR\s*CONDITIONER|SPLIT|WINDOW|\bCOOLER\b)/i.test(fullText)) {
                                acFound = true;
                            }
                        });

                        if (customer.saleitems.length > 0) {
                            productSelect.selectedIndex = 1;
                            productSelect.dispatchEvent(new Event('change'));
                        }

                        if (acFound) {
                            secondProductWrap.classList.remove('hidden');
                            if (customer.saleitems.length > 1) {
                                productSelect2.selectedIndex = 2;
                                productSelect2.dispatchEvent(new Event('change'));
                            }
                        } else {
                            secondProductWrap.classList.add('hidden');
                            serialInput2.value = '';
                            selectedProduct2.value = '';
                        }


                    } else {

                        productSelect.innerHTML =
                            '<option value="">কোনো Sale Item পাওয়া যায়নি</option>';

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | ⭐ FALLBACK ইনপুট দেখাবে/লুকাবে (dropdown-এ real option আছে কিনা অনুযায়ী)
                    |--------------------------------------------------------------------------
                    */

                    toggleProductFallback(productSelect, productManual);

                    toggleProductFallback(productSelect2, productManual2);


                    /*
                    |--------------------------------------------------------------------------
                    | SUCCESS STATUS
                    |--------------------------------------------------------------------------
                    */

                    customerFetchStatus.innerText =
                        '✅ Customer information পাওয়া গেছে';

                    customerFetchStatus.className =
                        'text-xs mt-1 text-green-600 font-semibold';


                } else {


                    /*
                    |--------------------------------------------------------------------------
                    | CUSTOMER NOT FOUND
                    |--------------------------------------------------------------------------
                    */

                    customerFetchStatus.innerText =
                        '⚠️ এই ফোন নম্বরে Customer পাওয়া যায়নি';

                    customerFetchStatus.className =
                        'text-xs mt-1 text-orange-600';


                    /*
                    |--------------------------------------------------------------------------
                    | RESET FIELDS
                    |--------------------------------------------------------------------------
                    */

                    document.getElementById(
                        'comp_name'
                    ).value = '';

                    document.getElementById(
                        'comp_address'
                    ).value = '';

                    document.getElementById(
                        'comp_pincode'
                    ).value = '';


                    /*
                    |--------------------------------------------------------------------------
                    | RESET PRODUCT (১ম ও ২য়)
                    |--------------------------------------------------------------------------
                    */

                    productSelect.innerHTML =
                        '<option value="">-- Product নির্বাচন করুন --</option>';

                    productSelect2.innerHTML =
                        '<option value="">-- SL No নির্বাচন করুন --</option>';


                    /*
                    |--------------------------------------------------------------------------
                    | RESET SERIAL (১ম ও ২য়)
                    |--------------------------------------------------------------------------
                    */

                    serialInput.value = '';

                    serialInput2.value = '';


                    /*
                    |--------------------------------------------------------------------------
                    | RESET PRODUCT ID (১ম ও ২য়)
                    |--------------------------------------------------------------------------
                    */

                    selectedProduct.value = '';

                    selectedProduct2.value = '';


                    /*
                    |--------------------------------------------------------------------------
                    | ⭐ দ্বিতীয় ইউনিট ব্লক আবার লুকিয়ে ফেলুন
                    |--------------------------------------------------------------------------
                    */

                    secondProductWrap.classList.add('hidden');


                    /*
                    |--------------------------------------------------------------------------
                    | ⭐ কাস্টমার/সেল আইটেম না পাওয়া গেলে ১ম ইউনিটের fallback দেখান
                    |--------------------------------------------------------------------------
                    | (২য় ইউনিটের ব্লকই hidden, তাই তার fallback দেখানোর দরকার নেই)
                    |--------------------------------------------------------------------------
                    */

                    toggleProductFallback(productSelect, productManual);

                }


            } catch (error) {

                console.error(
                    'Customer fetch error:',
                    error
                );


                customerFetchStatus.innerText =
                    '❌ Customer data fetch করতে সমস্যা হয়েছে';

                customerFetchStatus.className =
                    'text-xs mt-1 text-red-600';

            }

        }


        /*
        |--------------------------------------------------------------------------
        | OCR SCAN
        |--------------------------------------------------------------------------
        */

        async function scanBill(event) {

            if (event) {

                event.preventDefault();

            }


            const fileInput =
                document.getElementById(
                    'invoiceFile'
                );


            const scanBtn =
                document.getElementById(
                    'scanBtn'
                );


            /*
            |--------------------------------------------------------------------------
            | FILE CHECK
            |--------------------------------------------------------------------------
            */

            if (
                !fileInput ||
                !fileInput.files ||
                fileInput.files.length === 0
            ) {

                alert(
                    'অনুগ্রহ করে একটি Invoice Image অথবা PDF নির্বাচন করুন!'
                );

                return;

            }


            const file =
                fileInput.files[0];


            const originalText =
                scanBtn.innerText;


            scanBtn.innerText =
                '⏳ স্ক্যান করা হচ্ছে...';


            scanBtn.disabled =
                true;


            try {

                let imageSource =
                    file;


                /*
                |--------------------------------------------------------------------------
                | PDF
                |--------------------------------------------------------------------------
                */

                if (
                    file.type ===
                    'application/pdf'
                ) {

                    const arrayBuffer =
                        await file.arrayBuffer();


                    const pdf =
                        await pdfjsLib
                            .getDocument({
                                data: arrayBuffer
                            })
                            .promise;


                    const page =
                        await pdf.getPage(1);


                    const viewport =
                        page.getViewport({
                            scale: 2
                        });


                    const canvas =
                        document.createElement(
                            'canvas'
                        );


                    const context =
                        canvas.getContext(
                            '2d'
                        );


                    canvas.width =
                        viewport.width;


                    canvas.height =
                        viewport.height;


                    await page.render({

                        canvasContext:
                            context,

                        viewport:
                            viewport

                    }).promise;


                    imageSource =
                        canvas;

                }


                /*
                |--------------------------------------------------------------------------
                | TESSERACT OCR
                |--------------------------------------------------------------------------
                */

                const result =
                    await Tesseract.recognize(
                        imageSource,
                        'eng'
                    );


                const text =
                    result.data.text;


                // console.log(
                //     'OCR TEXT:',
                //     text
                // );


                /*
                |--------------------------------------------------------------------------
                | FIND PHONE
                |--------------------------------------------------------------------------
                */

                const phoneMatch =

                    text.match(
                        /(?:Mobile\s*No|WA\s*No|Reward\s*Mobile)\s*:\s*(\d{10})/i
                    )

                    ||

                    text.match(
                        /\b[6-9]\d{9}\b/
                    );


                /*
                |--------------------------------------------------------------------------
                | PHONE FOUND
                |--------------------------------------------------------------------------
                */

                if (phoneMatch) {

                    const phone =
                        phoneMatch[1] ||
                        phoneMatch[0];


                    // console.log(
                    //     'OCR Phone:',
                    //     phone
                    // );


                    /*
                    |--------------------------------------------------------------------------
                    | SET PHONE
                    |--------------------------------------------------------------------------
                    */

                    phoneInput.value =
                        phone;


                    /*
                    |--------------------------------------------------------------------------
                    | FETCH CUSTOMER
                    |--------------------------------------------------------------------------
                    */

                    await fetchCustomer(
                        phone
                    );


                } else {

                    alert(
                        '⚠️ Invoice থেকে কোনো valid 10 digit phone number পাওয়া যায়নি।'
                    );

                }


            } catch (error) {

                console.error(
                    'OCR Error:',
                    error
                );


                alert(
                    '❌ Scan করতে সমস্যা হয়েছে।'
                );


            } finally {

                scanBtn.innerText =
                    originalText;

                scanBtn.disabled =
                    false;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | FORM SUBMIT
        |--------------------------------------------------------------------------
        */

        document
            .getElementById(
                'companyBookingForm'
            )
            .addEventListener(
                'submit',
                function (event) {

                    event.preventDefault();


                    /*
                    |--------------------------------------------------------------------------
                    | FORM VALUES (১ম ইউনিট)
                    |--------------------------------------------------------------------------
                    */

                    const phone =
                        document.getElementById(
                            'comp_phone'
                        ).value;


                    const name =
                        document.getElementById(
                            'comp_name'
                        ).value;


                    const slNo =
                        document.getElementById(
                            'comp_product'
                        ).value;


                    const productId =
                        document.getElementById(
                            'selectedProduct'
                        ).value;


                    const serialNo =
                        document.getElementById(
                            'comp_serial'
                        ).value;


                    /*
                    |--------------------------------------------------------------------------
                    | ⭐ dropdown-এ কিছু সিলেক্ট না থাকলে fallback manual ভ্যালু ব্যবহার হবে
                    |--------------------------------------------------------------------------
                    */

                    const productName =
                        slNo
                            ? slNo
                            : productManual.value.trim();


                    /*
                    |--------------------------------------------------------------------------
                    | ⭐ FORM VALUES (২য় ইউনিট, AC-র মতো ক্ষেত্রে)
                    |--------------------------------------------------------------------------
                    */

                    const slNo2 =
                        document.getElementById(
                            'comp_product_2'
                        ).value;


                    const productId2 =
                        document.getElementById(
                            'selectedProduct_2'
                        ).value;


                    const serialNo2 =
                        document.getElementById(
                            'comp_serial_2'
                        ).value;


                    const productName2 =
                        slNo2
                            ? slNo2
                            : productManual2.value.trim();


                    const caseId1Elem = document.getElementById('comp_case_1');
                    const caseId1 = caseId1Elem ? caseId1Elem.value.trim() : '';

                    const caseId2Elem = document.getElementById('comp_case_2');
                    const caseId2 = caseId2Elem ? caseId2Elem.value.trim() : '';


                    /*
                    |--------------------------------------------------------------------------
                    | ⭐ ভ্যালিডেশন: ১ম ইউনিটের প্রোডাক্ট (dropdown বা manual) বাধ্যতামূলক
                    |--------------------------------------------------------------------------
                    | dropdown-এ আগে যে required attribute ছিল, সেটা সরিয়ে এখানে
                    | manual check করা হচ্ছে, কারণ dropdown ফাঁকা থাকলেও fallback
                    | ইনপুটে ভ্যালু থাকতে পারে।
                    |--------------------------------------------------------------------------
                    */

                    if (!productName) {

                        alert(
                            '⚠️ অনুগ্রহ করে প্রোডাক্ট ও মডেল (১ম ইউনিট) সিলেক্ট করুন অথবা লিখুন।'
                        );

                        return;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | ⭐ ২য় ইউনিট ব্লক visible থাকলে সেটাও বাধ্যতামূলক
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !secondProductWrap.classList.contains('hidden') &&
                        !productName2
                    ) {

                        alert(
                            '⚠️ অনুগ্রহ করে প্রোডাক্ট ও মডেল (২য় ইউনিট) সিলেক্ট করুন অথবা লিখুন।'
                        );

                        return;

                    }


                    const address =
                        document.getElementById(
                            'comp_address'
                        ).value;


                    const pincode =
                        document.getElementById(
                            'comp_pincode'
                        ).value;


                    const billDate =
                        document.getElementById(
                            'comp_bill_date'
                        ).value;


                    const bookingDate =
                        document.getElementById(
                            'comp_booking_date'
                        ).value;


                    const remarks =
                        document.getElementById(
                            'comp_remarks'
                        ).value;


                    /*
                    |--------------------------------------------------------------------------
                    | ⭐ SUBMIT স্ট্যাটাস এলিমেন্ট ও বাটন
                    |--------------------------------------------------------------------------
                    */

                    const statusBox =
                        document.getElementById(
                            'bookingSubmitStatus'
                        );

                    const submitBtn =
                        document.getElementById(
                            'bookingSubmitBtn'
                        );


                    function showStatus(message, type) {

                        statusBox.innerText = message;

                        statusBox.classList.remove(
                            'hidden', 'bg-green-50', 'text-green-700',
                            'bg-red-50', 'text-red-700'
                        );

                        if (type === 'success') {

                            statusBox.classList.add(
                                'bg-green-50', 'text-green-700'
                            );

                        } else {

                            statusBox.classList.add(
                                'bg-red-50', 'text-red-700'
                            );

                        }

                    }


                /*
                |--------------------------------------------------------------------------
                | ⭐ CSRF TOKEN
                |--------------------------------------------------------------------------
                | মেইন লেআউটে <meta name="csrf-token" content="{{ csrf_token() }}">
                        | থাকা আবশ্যক(Laravel - এ সাধারণত layouts.main এ আগে থেকেই থাকে)।
                | --------------------------------------------------------------------------
                */

                    const csrfMeta =
                        document.querySelector(
                            'meta[name="csrf-token"]'
                        );

                    const csrfToken =
                        csrfMeta
                            ? csrfMeta.content
                            : '';


                    /*
                    |--------------------------------------------------------------------------
                    | ⭐ FORM DATA তৈরি
                    |--------------------------------------------------------------------------
                    */

                    const formData =
                        new FormData();

                    formData.append('phone', phone);
                    formData.append('name', name);
                    formData.append('address', address);
                    formData.append('pin', pincode);
                    formData.append('bill_date', billDate);
                    formData.append('call_date', bookingDate);
                    formData.append('note', remarks);

                    formData.append('sl_no', slNo);
                    formData.append('product_id', productId);
                    formData.append('product_name', productName);
                    formData.append('serial_no', serialNo);

                    formData.append('sl_no_2', slNo2);
                    formData.append('product_id_2', productId2);
                    formData.append('product_name_2', productName2);
                    formData.append('serial_no_2', serialNo2);

                    formData.append('case_id_1', caseId1);
                    formData.append('case_id_2', caseId2);

                    // ⭐ ইনভয়েস ফাইল থাকলে সেটাও পাঠানো হবে (OCR বক্সে যেটা সিলেক্ট করা হয়েছিল)
                    const invoiceFileInput =
                        document.getElementById(
                            'invoiceFile'
                        );

                    if (
                        invoiceFileInput &&
                        invoiceFileInput.files.length > 0
                    ) {

                        formData.append(
                            'invoice_image',
                            invoiceFileInput.files[0]
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | ⭐ সেভ করার সময় বাটন ডিজেবল
                    |--------------------------------------------------------------------------
                    */

                    submitBtn.disabled = true;

                    submitBtn.innerText =
                        '⏳ সেভ করা হচ্ছে...';


                    /*
                    |--------------------------------------------------------------------------
                    | ⭐ SERVER-এ POST করা
                    |--------------------------------------------------------------------------
                    */

                    fetch('/admin/service-booking', {

                        method: 'POST',

                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },

                        body: formData

                    })
                        .then(async (response) => {

                            const data =
                                await response.json();

                            if (!response.ok) {

                                throw new Error(
                                    data.message ||
                                    'সেভ করতে সমস্যা হয়েছে'
                                );

                            }

                            return data;

                        })
                        .then((data) => {

                            /*
                            |--------------------------------------------------------------------------
                            | ⭐ SUCCESS: মেসেজ দেখাও + ফর্ম ক্লিয়ার করো
                            |--------------------------------------------------------------------------
                            */

                            showStatus(
                                '✅ ' + (data.message || 'কল বুকিং সফলভাবে সেভ হয়েছে।'),
                                'success'
                            );

                            document
                                .getElementById('companyBookingForm')
                                .reset();

                            // dropdown ও hidden ফিল্ডগুলো ম্যানুয়ালি রিসেট (reset() select-এর dataset মুছে না)
                            productSelect.innerHTML =
                                '<option value="">-- SL No নির্বাচন করুন --</option>';

                            productSelect2.innerHTML =
                                '<option value="">-- SL No নির্বাচন করুন --</option>';

                            selectedProduct.value = '';
                            selectedProduct2.value = '';

                            secondProductWrap.classList.add('hidden');

                            toggleProductFallback(productSelect, productManual);
                            toggleProductFallback(productSelect2, productManual2);

                            customerFetchStatus.innerText = '';

                            // বুকিং ডেট আজকের তারিখেই আবার বসিয়ে দাও
                            document.getElementById('comp_booking_date').value =
                                new Date().toISOString().split('T')[0];

                            document.getElementById('comp_bill_date').value =
                                new Date().toISOString().split('T')[0];

                        })
                        .catch((error) => {

                            console.error(
                                'Booking save error:',
                                error
                            );

                            showStatus(
                                '❌ ' + error.message,
                                'error'
                            );

                        })
                        .finally(() => {

                            submitBtn.disabled = false;

                            submitBtn.innerText =
                                '💾 কল বুকিং সেভ করুন';

                        });

                }
            );

    </script>

@endsection