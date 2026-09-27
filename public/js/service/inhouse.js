
// ============================================================
// inhouse.js
// Database Based In-House Service
// Save
// Load
// Search
// Filter
// Dashboard Counts
// Render Table
// ============================================================

var inhouseCustomerRequest = 0;

function getLocalDateValue() {
    var today = new Date();
    var month = String(today.getMonth() + 1).padStart(2, '0');
    var day = String(today.getDate()).padStart(2, '0');

    return today.getFullYear() + '-' + month + '-' + day;
}

function initInhouseForm() {
    var phoneInput = document.getElementById('inh_phone');

    if (!phoneInput || phoneInput.dataset.lookupInitialized === 'true') {
        return;
    }

    phoneInput.dataset.lookupInitialized = 'true';
    phoneInput.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 10);

        if (this.value.length === 10) {
            lookupInhouseCustomer(this.value);
        }
    });
}

function lookupInhouseCustomer(phone) {
    var requestId = ++inhouseCustomerRequest;

    var statusEl = document.getElementById('inh_phone_status');

    if (statusEl) {
        statusEl.innerText = '⏳ Customer খোঁজা হচ্ছে...';
        statusEl.className = 'text-xs mt-1 text-blue-600';
    }

    fetch('/get-customer-by-phone/' + encodeURIComponent(phone), {
        method: 'GET',
        headers: { 'Accept': 'application/json' }
    })
        .then(function (response) {
            return response.json();
        })
        .then(function (result) {
            if (requestId !== inhouseCustomerRequest) return;

            var statusEl = document.getElementById('inh_phone_status');

            if (!result.success || !result.data) {
                if (statusEl) {
                    statusEl.innerText = '⚠️ এই ফোন নম্বরে Customer পাওয়া যায়নি';
                    statusEl.className = 'text-xs mt-1 text-orange-600';
                }
                return;
            }

            var customer = result.data;
            var name = document.getElementById('inh_name');
            var address = document.getElementById('inh_address');
            var pincode = document.getElementById('inh_pincode');

            if (name && customer.name) name.value = customer.name;
            if (address && customer.address) address.value = customer.address;
            if (pincode && customer.pin) pincode.value = customer.pin;

            if (statusEl) {
                statusEl.innerText = '✅ Customer information পাওয়া গেছে';
                statusEl.className = 'text-xs mt-1 text-green-600 font-semibold';
            }
        })
        .catch(function (error) {
            console.error('In-House customer lookup error:', error);
            var statusEl = document.getElementById('inh_phone_status');
            if (statusEl) {
                statusEl.innerText = '❌ Customer data fetch করতে সমস্যা হয়েছে';
                statusEl.className = 'text-xs mt-1 text-red-600';
            }
        });
}


// ============================================================
// SAVE IN-HOUSE SERVICE
// ============================================================

async function saveInhouseService(event) {

    // IMPORTANT:
    // Prevent normal form submit / page reload
    event.preventDefault();


    // console.log(
    //     'Save Inhouse Service Triggered'
    // );

    // console.log(
    //     'URL:',
    //     INHOUSE_STORE_URL
    // );


    // ========================================================
    // WARRANTY
    // ========================================================

    const warranty =
        document.querySelector(
            'input[name="inh_warranty"]:checked'
        )?.value ||
        'Out of Warranty';


    // ========================================================
    // FORM DATA
    // ========================================================

    const data = {

        phone:
            document
                .getElementById('inh_phone')
                .value
                .trim(),

        name:
            document
                .getElementById('inh_name')
                .value
                .trim(),

        alt_phone:
            document
                .getElementById('inh_alt_phone')
                .value
                .trim(),

        address:
            document
                .getElementById('inh_address')
                .value
                .trim(),

        pincode:
            document
                .getElementById('inh_pincode')
                .value
                .trim(),

        product:
            document
                .getElementById('inh_product')
                .value
                .trim(),

        estimate:
            document
                .getElementById('inh_estimate')
                .value ||
            null,

        final_amount:
            document
                .getElementById('inh_final_amount')
                .value ||
            null,

        receive_date:
            document
                .getElementById('inh_receive_date')
                .value,

        delivery_date:
            document
                .getElementById('inh_delivery_date')
                .value ||
            null,

        warranty:
            warranty

    };


    // console.log(
    //     'Sending Data:',
    //     data
    // );


    // ========================================================
    // VALIDATION
    // ========================================================

    if (!data.phone) {

        alert(
            '❌ ফোন নম্বর দিন।'
        );

        return;
    }


    if (!data.name) {

        alert(
            '❌ কাস্টমার নাম দিন।'
        );

        return;
    }


    if (!data.address) {

        alert(
            '❌ কাস্টমার এড্রেস দিন।'
        );

        return;
    }


    if (!data.pincode) {

        alert(
            '❌ পিন কোড দিন।'
        );

        return;
    }


    if (!data.product) {

        alert(
            '❌ প্রোডাক্ট / সমস্যা লিখুন।'
        );

        return;
    }


    if (!data.receive_date) {

        alert(
            '❌ জমা দেওয়ার তারিখ দিন।'
        );

        return;
    }


    // ========================================================
    // CSRF TOKEN
    // ========================================================

    const csrfElement =
        document.querySelector(
            'meta[name="csrf-token"]'
        );


    if (!csrfElement) {

        alert(
            '❌ CSRF token পাওয়া যায়নি!'
        );

        console.error(
            'meta[name="csrf-token"] missing'
        );

        return;
    }


    const csrfToken =
        csrfElement.getAttribute(
            'content'
        );


    // ========================================================
    // SEND DATA TO LARAVEL
    // ========================================================

    try {

        const response =
            await fetch(
                INHOUSE_STORE_URL,
                {

                    method: 'POST',

                    headers: {

                        'Content-Type':
                            'application/json',

                        'Accept':
                            'application/json',

                        'X-CSRF-TOKEN':
                            csrfToken

                    },

                    body:
                        JSON.stringify(data)

                }
            );


        // ====================================================
        // RESPONSE
        // ====================================================

        const result =
            await response.json();


        // console.log(
        //     'Server Response:',
        //     result
        // );


        // ====================================================
        // VALIDATION / SERVER ERROR
        // ====================================================

        if (!response.ok) {

            if (result.errors) {

                let message = '';


                Object.values(
                    result.errors
                ).forEach(
                    function (errors) {

                        errors.forEach(
                            function (error) {

                                message +=
                                    error +
                                    '\n';

                            }
                        );

                    }
                );


                alert(message);

            }

            else {

                alert(
                    result.message ||
                    '❌ Data save failed'
                );

            }


            return;
        }


        // ====================================================
        // SUCCESS
        // ====================================================

        if (result.success) {

            alert(
                '✅ In-House Service Successfully Saved!'
            );


            // ==================================================
            // CLEAR FORM
            // ==================================================

            clearInhouseForm();


            // ==================================================
            // RELOAD COUNTS
            // ==================================================

            await loadInhouseCounts();


            // ==================================================
            // RELOAD TABLE
            // ==================================================

            await loadInhouseServices(
                currentFilter || 'all',
                searchQuery || ''
            );

        }

        else {

            alert(
                result.message ||
                '❌ Data save failed'
            );

        }


    }

    catch (error) {

        console.error(
            'Save In-House Error:',
            error
        );


        alert(
            '❌ Server error হয়েছে। আবার চেষ্টা করুন।'
        );

    }

}


// ============================================================
// CLEAR IN-HOUSE FORM
// ============================================================

function clearInhouseForm() {

    const fields = [

        'inh_phone',
        'inh_name',
        'inh_alt_phone',
        'inh_address',
        'inh_pincode',
        'inh_product',
        'inh_estimate',
        'inh_final_amount',
        'inh_delivery_date'

    ];


    fields.forEach(
        function (id) {

            const element =
                document.getElementById(id);


            if (element) {

                element.value = '';

            }

        }
    );


    // Reset warranty
    const outWarranty =
        document.querySelector(
            'input[name="inh_warranty"][value="Out of Warranty"]'
        );

    if (outWarranty) {
        outWarranty.checked = true;
    }

    // Reset receive date
    const receiveDate =
        document.getElementById(
            'inh_receive_date'
        );

    if (receiveDate) {
        receiveDate.value = getLocalDateValue();
        receiveDate.max = getLocalDateValue();
    }

    // Clear status message
    const statusEl = document.getElementById('inh_phone_status');
    if (statusEl) {
        statusEl.innerText = '';
        statusEl.className = 'text-xs mt-1 text-gray-500';
    }

}


// ============================================================
// LOAD IN-HOUSE SERVICES
// ============================================================

async function loadInhouseServices(
    status = 'all',
    search = ''
) {

    // console.log(
    //     'Loading In-House Services...',
    //     {
    //         status: status,
    //         search: search
    //     }
    // );


    try {

        // ====================================================
        // URL
        // ====================================================

        let url =
            INHOUSE_LIST_URL;


        const params =
            new URLSearchParams();


        // Status
        if (
            status &&
            status !== 'all'
        ) {

            params.append(
                'status',
                status
            );

        }


        // Search
        if (
            search &&
            search.trim() !== ''
        ) {

            params.append(
                'search',
                search.trim()
            );

        }


        if (
            params.toString()
        ) {

            url +=
                '?' +
                params.toString();

        }


        // console.log(
        //     'Load URL:',
        //     url
        // );


        // ====================================================
        // FETCH
        // ====================================================

        const response =
            await fetch(
                url,
                {

                    method: 'GET',

                    headers: {

                        'Accept':
                            'application/json'

                    }

                }
            );


        const result =
            await response.json();


        // console.log(
        //     'Loaded In-House Data:',
        //     result
        // );


        // ====================================================
        // ERROR
        // ====================================================

        if (
            !response.ok ||
            !result.success
        ) {

            alert(
                result.message ||
                'In-House data load করতে সমস্যা হয়েছে।'
            );

            return;

        }


        // ====================================================
        // RENDER
        // ====================================================

        renderInhouseServices(
            result.data || []
        );


    }

    catch (error) {

        console.error(
            'Load In-House Error:',
            error
        );


        alert(
            '❌ In-House data load করতে server error হয়েছে।'
        );

    }

}


// ============================================================
// RENDER IN-HOUSE TABLE
// ============================================================

function renderInhouseServices(
    services
) {
    window.inhouseBookings = services || [];

    const thead =
        document.getElementById(
            'mainTableHead'
        );

    const tbody =
        document.getElementById(
            'mainTableBody'
        );


    if (
        !thead ||
        !tbody
    ) {

        console.error(
            'Table element পাওয়া যায়নি!'
        );

        return;
    }


    // ========================================================
    // TABLE HEADER
    // ========================================================

    thead.innerHTML = `

        <tr class="bg-green-50 border-b">

            <th class="p-3 font-bold whitespace-nowrap">
                ID
            </th>

            <th class="p-3 font-bold whitespace-nowrap">
                কাস্টমার ও যোগাযোগ
            </th>

            <th class="p-3 font-bold whitespace-nowrap">
                প্রোডাক্ট / সমস্যা
            </th>

            <th class="p-3 font-bold whitespace-nowrap">
                Estimate
            </th>

            <th class="p-3 font-bold whitespace-nowrap">
                Final Amount
            </th>

            <th class="p-3 font-bold whitespace-nowrap">
                জমার তারিখ ও সময়
            </th>

            <th class="p-3 font-bold whitespace-nowrap">
                Delivery Date
            </th>

            <th class="p-3 font-bold whitespace-nowrap">
                Warranty
            </th>

            <th class="p-3 font-bold whitespace-nowrap">
                Status
            </th>

            <th class="p-3 font-bold whitespace-nowrap text-center">
                অ্যাকশন
            </th>

        </tr>

    `;


    // ========================================================
    // NO DATA
    // ========================================================

    if (
        !services ||
        services.length === 0
    ) {

        tbody.innerHTML = `

            <tr>

                <td
                    colspan="10"
                    class="text-center p-10 text-gray-500"
                >

                    📭 কোনো In-House Service Record পাওয়া যায়নি।

                </td>

            </tr>

        `;

        return;
    }


    // ========================================================
    // TABLE ROWS
    // ========================================================

    tbody.innerHTML =
        services.map(
            function (service) {


                // ==============================================
                // STATUS
                // ==============================================

                let statusText =
                    'Pending';

                let statusClass =
                    'bg-yellow-100 text-yellow-700';


                if (
                    service.status ===
                    'ready' ||
                    service.status ===
                    'Ready/Repaired'
                ) {

                    statusText =
                        'Ready';

                    statusClass =
                        'bg-purple-100 text-purple-700';

                }


                else if (
                    service.status ===
                    'delivered' ||
                    service.status ===
                    'Delivered'
                ) {

                    statusText =
                        'Delivered';

                    statusClass =
                        'bg-green-100 text-green-700';

                }


                // ==============================================
                // WARRANTY
                // ==============================================

                let warrantyText =
                    service.warranty ||
                    'Out of Warranty';


                // ==============================================
                // RETURN ROW
                // ==============================================

                return `

                    <tr
                        class="border-b hover:bg-gray-50"
                    >

                        <td class="p-3 font-semibold text-xs">
                            ${escapeInhouseHtml(
                    service.id
                )}
                        </td>


                        <td class="p-3">

                            <div class="font-bold text-gray-800 text-xs">
                                ${escapeInhouseHtml(
                    service.name
                )}
                            </div>

                            <div class="text-xs text-gray-500">
                                📞 ${escapeInhouseHtml(service.phone)}${service.alt_phone ? ` (Alt: ${escapeInhouseHtml(service.alt_phone)})` : ''}
                            </div>

                            ${(service.address || service.pincode)
                        ? `
                                    <div class="text-xs text-gray-600 mt-0.5">
                                        📍 ${escapeInhouseHtml(service.address || '')}${service.pincode ? ` (PIN: ${escapeInhouseHtml(service.pincode)})` : ''}
                                    </div>
                                `
                        : ''
                    }

                        </td>


                        <td class="p-3 text-xs">
                            ${escapeInhouseHtml(
                        service.product
                    )}
                        </td>


                        <td class="p-3 whitespace-nowrap text-xs">

                            ₹${service.estimate ??
                    '0.00'
                    }

                        </td>


                        <td class="p-3 font-semibold whitespace-nowrap text-xs">

                            ₹${service.final_amount ??
                    '0.00'
                    }

                        </td>


                        <td class="p-3 whitespace-nowrap text-xs font-medium">

                            ${formatInhouseDate(
                        service.receive_date || service.created_at
                    )
                    }

                        </td>


                        <td class="p-3 whitespace-nowrap text-xs">

                            ${service.delivery_date
                        ? formatInhouseDate(
                            service.delivery_date
                        )
                        : '-'
                    }

                        </td>


                        <td class="p-3 whitespace-nowrap text-xs">

                            ${escapeInhouseHtml(
                        warrantyText
                    )}

                        </td>


                        <td class="p-3">

                            <span
                                class="px-2.5 py-1 rounded-full text-xs font-bold ${statusClass}"
                            >
                                ${statusText}
                            </span>

                        </td>

                        <td class="p-3 text-center whitespace-nowrap">
                            <button onclick="openEditModal('${service.id}')" class="text-amber-600 hover:text-amber-800 hover:underline text-xs font-bold mr-2">✏️ এডিট</button>
                            <button onclick="printReceipt('${service.id}')" class="text-blue-600 hover:underline text-xs font-bold mr-2">🖨️ প্রিন্ট</button>
                            <button onclick="deleteRecord('${service.id}')" class="text-red-500 hover:text-red-700 text-xs font-bold">🗑️ ডিলিট</button>
                        </td>

                    </tr>

                `;

            }
        ).join('');
}




// ============================================================
// LOAD DASHBOARD COUNTS
// ============================================================

async function loadInhouseCounts() {

    // console.log(
    //     'Loading In-House Counts...'
    // );


    try {

        const response =
            await fetch(
                INHOUSE_COUNTS_URL,
                {

                    method: 'GET',

                    headers: {

                        'Accept':
                            'application/json'

                    }

                }
            );


        const result =
            await response.json();


        // console.log(
        //     'In-House Counts:',
        //     result
        // );


        if (
            !response.ok ||
            !result.success
        ) {

            console.error(
                result.message ||
                'Count load failed'
            );

            return;
        }


        // ====================================================
        // TOTAL
        // ====================================================

        const total =
            document.getElementById(
                'inh_count_total'
            );


        if (total) {

            total.textContent =
                result.total ?? 0;

        }


        // ====================================================
        // PENDING
        // ====================================================

        const pending =
            document.getElementById(
                'inh_count_pending'
            );


        if (pending) {

            pending.textContent =
                result.pending ?? 0;

        }


        // ====================================================
        // READY
        // ====================================================

        const ready =
            document.getElementById(
                'inh_count_ready'
            );


        if (ready) {

            ready.textContent =
                result.ready ?? 0;

        }


        // ====================================================
        // DELIVERED
        // ====================================================

        const delivered =
            document.getElementById(
                'inh_count_delivered'
            );


        if (delivered) {

            delivered.textContent =
                result.delivered ?? 0;

        }


    }

    catch (error) {

        console.error(
            'Count Load Error:',
            error
        );

    }

}


// ============================================================
// DATE FORMAT
// ============================================================

function formatInhouseDate(
    date
) {

    if (!date) {
        return '-';
    }

    try {
        var d = new Date(date);
        if (!isNaN(d.getTime())) {
            var year = d.getFullYear();
            var month = String(d.getMonth() + 1).padStart(2, '0');
            var day = String(d.getDate()).padStart(2, '0');
            var hours = d.getHours();
            var minutes = String(d.getMinutes()).padStart(2, '0');
            var ampm = hours >= 12 ? 'PM' : 'AM';
            hours = hours % 12;
            hours = hours ? hours : 12;
            var strHours = String(hours).padStart(2, '0');

            if (String(date).includes('T') || String(date).includes(':')) {
                return year + '-' + month + '-' + day + ' ' + strHours + ':' + minutes + ' ' + ampm;
            }
            return year + '-' + month + '-' + day;
        }
    } catch (e) { }

    const parts =
        String(date).split('-');

    if (
        parts.length === 3
    ) {
        return (
            parts[0] +
            '-' +
            parts[1] +
            '-' +
            parts[2]
        );
    }

    return date;

}


// ============================================================
// HTML ESCAPE
// ============================================================

function escapeInhouseHtml(
    value
) {

    if (
        value === null ||
        value === undefined
    ) {

        return '';

    }


    return String(value)
        .replace(
            /&/g,
            '&amp;'
        )
        .replace(
            /</g,
            '&lt;'
        )
        .replace(
            />/g,
            '&gt;'
        )
        .replace(
            /"/g,
            '&quot;'
        )
        .replace(
            /'/g,
            '&#039;'
        );

}

