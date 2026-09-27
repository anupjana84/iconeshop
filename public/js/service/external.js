
// ============================================================
// external.js
// Database Based External Service  (table: external_services)
// Save  |  Load  |  Search  |  Filter
// Dashboard Counts  |  Render Table
// ============================================================

var externalCustomerRequest = 0;

function getExternalLocalDateValue() {
    var today = new Date();
    var month = String(today.getMonth() + 1).padStart(2, '0');
    var day   = String(today.getDate()).padStart(2, '0');
    return today.getFullYear() + '-' + month + '-' + day;
}

function initExternalForm() {
    var phoneInput = document.getElementById('ext_phone');

    if (!phoneInput || phoneInput.dataset.lookupInitialized === 'true') {
        return;
    }

    phoneInput.dataset.lookupInitialized = 'true';
    phoneInput.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 10);

        if (this.value.length === 10) {
            lookupExternalCustomer(this.value);
        }
    });
}

function lookupExternalCustomer(phone) {
    var requestId = ++externalCustomerRequest;

    var statusEl = document.getElementById('ext_phone_status');

    if (statusEl) {
        statusEl.innerText = '⏳ Customer খোঁজা হচ্ছে...';
        statusEl.className = 'text-xs mt-1 text-blue-600';
    }

    fetch('/get-customer-by-phone/' + encodeURIComponent(phone), {
        method: 'GET',
        headers: { 'Accept': 'application/json' }
    })
        .then(function (response) { return response.json(); })
        .then(function (result) {
            if (requestId !== externalCustomerRequest) return;

            var statusEl = document.getElementById('ext_phone_status');

            if (!result.success || !result.data) {
                if (statusEl) {
                    statusEl.innerText = '⚠️ এই ফোন নম্বরে Customer পাওয়া যায়নি';
                    statusEl.className = 'text-xs mt-1 text-orange-600';
                }
                return;
            }

            var customer = result.data;
            var name    = document.getElementById('ext_name');
            var address = document.getElementById('ext_address');
            var pincode = document.getElementById('ext_pincode');

            if (name    && customer.name)    name.value    = customer.name;
            if (address && customer.address) address.value = customer.address;
            if (pincode && customer.pin)     pincode.value = customer.pin;

            if (statusEl) {
                statusEl.innerText = '✅ Customer information পাওয়া গেছে';
                statusEl.className = 'text-xs mt-1 text-green-600 font-semibold';
            }
        })
        .catch(function (error) {
            console.error('External customer lookup error:', error);
            var statusEl = document.getElementById('ext_phone_status');
            if (statusEl) {
                statusEl.innerText = '❌ Customer data fetch করতে সমস্যা হয়েছে';
                statusEl.className = 'text-xs mt-1 text-red-600';
            }
        });
}


// ============================================================
// SAVE EXTERNAL SERVICE
// ============================================================

async function saveExternalService(event) {

    event.preventDefault();

    // console.log('Save External Service Triggered');
    // console.log('URL:', EXTERNAL_STORE_URL);

    // --------------------------------------------------------
    // WARRANTY
    // --------------------------------------------------------
    const warranty =
        document.querySelector('input[name="ext_warranty"]:checked')?.value ||
        'Out of Warranty';

    // --------------------------------------------------------
    // FORM DATA
    // --------------------------------------------------------
    const data = {
        phone:         document.getElementById('ext_phone').value.trim(),
        name:          document.getElementById('ext_name').value.trim(),
        address:       document.getElementById('ext_address').value.trim(),
        pincode:       document.getElementById('ext_pincode').value.trim(),
        product:       document.getElementById('ext_product').value.trim(),
        serial:        document.getElementById('ext_serial').value.trim()        || null,
        budget:        document.getElementById('ext_budget').value               || null,
        final_cost:    document.getElementById('ext_final_cost').value           || null,
        receive_date:  document.getElementById('ext_receive_date').value,
        vendor:        document.getElementById('ext_vendor').value.trim()        || null,
        sent_date:     document.getElementById('ext_sent_date').value            || null,
        back_date:     document.getElementById('ext_back_date').value            || null,
        delivery_date: document.getElementById('ext_delivery_date').value        || null,
        warranty:      warranty
    };

    // console.log('Sending Data:', data);

    // --------------------------------------------------------
    // VALIDATION
    // --------------------------------------------------------
    if (!data.phone)        { alert('❌ ফোন নম্বর দিন।');           return; }
    if (!data.name)         { alert('❌ কাস্টমার নাম দিন।');         return; }
    if (!data.address)      { alert('❌ কাস্টমার এড্রেস দিন।');      return; }
    if (!data.pincode)      { alert('❌ পিন কোড দিন।');              return; }
    if (!data.product)      { alert('❌ প্রোডাক্ট / মডেল লিখুন।');   return; }
    if (!data.receive_date) { alert('❌ জমা নেওয়ার তারিখ দিন।');     return; }

    // --------------------------------------------------------
    // CSRF TOKEN
    // --------------------------------------------------------
    const csrfElement = document.querySelector('meta[name="csrf-token"]');
    if (!csrfElement) {
        alert('❌ CSRF token পাওয়া যায়নি!');
        console.error('meta[name="csrf-token"] missing');
        return;
    }
    const csrfToken = csrfElement.getAttribute('content');

    // --------------------------------------------------------
    // SEND TO LARAVEL
    // --------------------------------------------------------
    try {
        const response = await fetch(EXTERNAL_STORE_URL, {
            method: 'POST',
            headers: {
                'Content-Type':  'application/json',
                'Accept':        'application/json',
                'X-CSRF-TOKEN':  csrfToken
            },
            body: JSON.stringify(data)
        });

        const result = await response.json();
        // console.log('Server Response:', result);

        if (!response.ok) {
            if (result.errors) {
                let message = '';
                Object.values(result.errors).forEach(function (errors) {
                    errors.forEach(function (error) { message += error + '\n'; });
                });
                alert(message);
            } else {
                alert(result.message || '❌ Data save failed');
            }
            return;
        }

        if (result.success) {
            alert('✅ External Service Successfully Saved!');

            clearExternalForm();

            await loadExternalCounts();

            await loadExternalServices(
                currentFilter || 'all',
                searchQuery   || ''
            );
        } else {
            alert(result.message || '❌ Data save failed');
        }

    } catch (error) {
        console.error('Save External Error:', error);
        alert('❌ Server error হয়েছে। আবার চেষ্টা করুন।');
    }
}


// ============================================================
// CLEAR EXTERNAL FORM
// ============================================================

function clearExternalForm() {

    const fields = [
        'ext_phone',
        'ext_name',
        'ext_address',
        'ext_pincode',
        'ext_product',
        'ext_serial',
        'ext_budget',
        'ext_final_cost',
        'ext_vendor',
        'ext_sent_date',
        'ext_back_date',
        'ext_delivery_date'
    ];

    fields.forEach(function (id) {
        const element = document.getElementById(id);
        if (element) element.value = '';
    });

    // Reset warranty
    const inWarranty = document.querySelector('input[name="ext_warranty"][value="In Warranty"]');
    if (inWarranty) inWarranty.checked = true;

    // Reset receive date to today
    const receiveDate = document.getElementById('ext_receive_date');
    if (receiveDate) {
        receiveDate.value = getExternalLocalDateValue();
    }

    // Clear status message
    const statusEl = document.getElementById('ext_phone_status');
    if (statusEl) {
        statusEl.innerText = '';
        statusEl.className = 'text-xs mt-1 text-gray-500';
    }
}


// ============================================================
// LOAD EXTERNAL SERVICES
// ============================================================

async function loadExternalServices(
    status = 'all',
    search = ''
) {
    // console.log('Loading External Services...', { status, search });

    try {
        let url = EXTERNAL_LIST_URL;
        const params = new URLSearchParams();

        if (status && status !== 'all') {
            params.append('status', status);
        }
        if (search && search.trim() !== '') {
            params.append('search', search.trim());
        }
        if (params.toString()) {
            url += '?' + params.toString();
        }

        // console.log('Load URL:', url);

        const response = await fetch(url, {
            method: 'GET',
            headers: { 'Accept': 'application/json' }
        });

        const result = await response.json();
        // console.log('Loaded External Data:', result);

        if (!response.ok || !result.success) {
            alert(result.message || 'External data load করতে সমস্যা হয়েছে।');
            return;
        }

        renderExternalServices(result.data || []);

    } catch (error) {
        console.error('Load External Error:', error);
        alert('❌ External data load করতে server error হয়েছে।');
    }
}


// ============================================================
// RENDER EXTERNAL TABLE
// ============================================================

function renderExternalServices(services) {
    window.externalBookings = services || [];

    const thead = document.getElementById('mainTableHead');
    const tbody = document.getElementById('mainTableBody');

    if (!thead || !tbody) {
        console.error('Table element পাওয়া যায়নি!');
        return;
    }

    // --------------------------------------------------------
    // TABLE HEADER
    // --------------------------------------------------------
    thead.innerHTML = `
        <tr class="bg-orange-50 border-b">
            <th class="p-3 font-bold whitespace-nowrap">ID</th>
            <th class="p-3 font-bold whitespace-nowrap">কাস্টমার ও যোগাযোগ</th>
            <th class="p-3 font-bold whitespace-nowrap">প্রোডাক্ট / মডেল</th>
            <th class="p-3 font-bold whitespace-nowrap">Serial No</th>
            <th class="p-3 font-bold whitespace-nowrap">Budget</th>
            <th class="p-3 font-bold whitespace-nowrap">Final Cost</th>
            <th class="p-3 font-bold whitespace-nowrap">জমার তারিখ ও সময়</th>
            <th class="p-3 font-bold whitespace-nowrap">Vendor</th>
            <th class="p-3 font-bold whitespace-nowrap">Sent Date</th>
            <th class="p-3 font-bold whitespace-nowrap">Back Date</th>
            <th class="p-3 font-bold whitespace-nowrap">Delivery Date</th>
            <th class="p-3 font-bold whitespace-nowrap">Warranty</th>
            <th class="p-3 font-bold whitespace-nowrap">Status</th>
            <th class="p-3 font-bold whitespace-nowrap text-center">অ্যাকশন</th>
        </tr>
    `;

    // --------------------------------------------------------
    // NO DATA
    // --------------------------------------------------------
    if (!services || services.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="14" class="text-center p-10 text-gray-500">
                    📭 কোনো External Service Record পাওয়া যায়নি।
                </td>
            </tr>
        `;
        return;
    }

    // --------------------------------------------------------
    // TABLE ROWS
    // --------------------------------------------------------
    tbody.innerHTML = services.map(function (service) {

        // STATUS badge
        let statusText  = 'Pending';
        let statusClass = 'bg-yellow-100 text-yellow-700';

        if (service.status === 'sent') {
            statusText  = 'Sent to Center';
            statusClass = 'bg-orange-100 text-orange-700';
        } else if (service.status === 'back') {
            statusText  = 'Back from Center';
            statusClass = 'bg-indigo-100 text-indigo-700';
        } else if (service.status === 'ready') {
            statusText  = 'Ready / Delivery বাকি';
            statusClass = 'bg-purple-100 text-purple-700';
        } else if (service.status === 'delivered') {
            statusText  = 'Delivered';
            statusClass = 'bg-green-100 text-green-700';
        }

        const warrantyText = service.warranty || 'Out of Warranty';

        return `
            <tr class="border-b hover:bg-gray-50">
                <td class="p-3 font-semibold text-xs">${escapeExternalHtml(service.id)}</td>
                <td class="p-3">
                    <div class="font-bold text-gray-800 text-xs">${escapeExternalHtml(service.name)}</div>
                    <div class="text-xs text-gray-500">📞 ${escapeExternalHtml(service.phone)}</div>
                    ${(service.address || service.pincode) ? `
                        <div class="text-xs text-gray-600 mt-0.5">
                            📍 ${escapeExternalHtml(service.address || '')}${service.pincode ? ` (PIN: ${escapeExternalHtml(service.pincode)})` : ''}
                        </div>
                    ` : ''}
                </td>
                <td class="p-3 text-xs">${escapeExternalHtml(service.product)}</td>
                <td class="p-3 whitespace-nowrap text-xs">${escapeExternalHtml(service.serial || '-')}</td>
                <td class="p-3 whitespace-nowrap text-xs">₹${service.budget ?? '0.00'}</td>
                <td class="p-3 font-semibold whitespace-nowrap text-xs">₹${service.final_cost ?? '0.00'}</td>
                <td class="p-3 whitespace-nowrap text-xs font-medium">${formatExternalDate(service.receive_date || service.created_at)}</td>
                <td class="p-3 text-xs">${escapeExternalHtml(service.vendor || '-')}</td>
                <td class="p-3 whitespace-nowrap text-xs">
                    ${service.sent_date ? `
                        <div class="flex items-center gap-1">
                            <span class="font-medium text-gray-800">${formatExternalDate(service.sent_date)}</span>
                            <button type="button" onclick="extSetDate('${service.id}', 'sent_date', '${service.sent_date}')" title="পাঠানোর তারিখ পরিবর্তন" class="text-blue-600 hover:text-blue-800 text-xs font-bold">✏️</button>
                        </div>
                    ` : `
                        <button type="button" onclick="extSetDate('${service.id}', 'sent_date', '')" class="bg-orange-100 hover:bg-orange-200 text-orange-700 font-bold px-2 py-0.5 rounded text-xs transition whitespace-nowrap flex items-center gap-1">
                            <span>📤</span> Sent Date
                        </button>
                    `}
                </td>
                <td class="p-3 whitespace-nowrap text-xs">
                    ${service.back_date ? `
                        <div class="flex items-center gap-1">
                            <span class="font-medium text-gray-800">${formatExternalDate(service.back_date)}</span>
                            <button type="button" onclick="extSetDate('${service.id}', 'back_date', '${service.back_date}')" title="ফেরত আসার তারিখ পরিবর্তন" class="text-blue-600 hover:text-blue-800 text-xs font-bold">✏️</button>
                        </div>
                    ` : `
                        <button type="button" onclick="extSetDate('${service.id}', 'back_date', '')" class="bg-indigo-100 hover:bg-indigo-200 text-indigo-700 font-bold px-2 py-0.5 rounded text-xs transition whitespace-nowrap flex items-center gap-1">
                            <span>📥</span> Back Date
                        </button>
                    `}
                </td>
                <td class="p-3 whitespace-nowrap text-xs">
                    ${service.delivery_date ? `
                        <div class="flex items-center gap-1">
                            <span class="font-medium text-gray-800">${formatExternalDate(service.delivery_date)}</span>
                            <button type="button" onclick="extSetDate('${service.id}', 'delivery_date', '${service.delivery_date}')" title="ডেলিভারি তারিখ পরিবর্তন" class="text-blue-600 hover:text-blue-800 text-xs font-bold">✏️</button>
                        </div>
                    ` : `
                        <button type="button" onclick="extSetDate('${service.id}', 'delivery_date', '')" class="bg-green-100 hover:bg-green-200 text-green-700 font-bold px-2 py-0.5 rounded text-xs transition whitespace-nowrap flex items-center gap-1">
                            <span>🚚</span> Delivery Date
                        </button>
                    `}
                </td>
                <td class="p-3 whitespace-nowrap text-xs">${escapeExternalHtml(warrantyText)}</td>
                <td class="p-3">
                    <select onchange="extUpdateStatus('${service.id}', this.value)" class="text-xs border rounded p-1 font-semibold outline-none bg-white shadow-sm cursor-pointer">
                        <option value="pending" ${service.status === 'pending' ? 'selected' : ''}>পেন্ডিং (Pending)</option>
                        <option value="sent" ${service.status === 'sent' ? 'selected' : ''}>সার্ভিস সেন্টারে পাঠানো হয়েছে</option>
                        <option value="back" ${service.status === 'back' ? 'selected' : ''}>সার্ভিস সেন্টার থেকে এসেছে</option>
                        <option value="ready" ${service.status === 'ready' ? 'selected' : ''}>ফেরত এসেছে (ডেলিভারি বাকি)</option>
                        <option value="delivered" ${service.status === 'delivered' ? 'selected' : ''}>ডেলিভারি সম্পন্ন (Delivered)</option>
                    </select>
                </td>
                <td class="p-3 text-center whitespace-nowrap">
                    <button onclick="openEditModal('${service.id}')" class="text-amber-600 hover:text-amber-800 hover:underline text-xs font-bold mr-2">✏️ এডিট</button>
                    <button onclick="printReceipt('${service.id}')" class="text-blue-600 hover:underline text-xs font-bold mr-2">🖨️ প্রিন্ট</button>
                    <button onclick="deleteRecord('${service.id}')" class="text-red-500 hover:text-red-700 text-xs font-bold">🗑️ ডিলিট</button>
                </td>
            </tr>
        `;
    }).join('');
}

// ============================================================
// EXTERNAL UPDATE STATUS & EDIT FIELD HELPERS
// ============================================================
function extSetDate(id, fieldName, currentDate) {
    var today = getExternalLocalDateValue();
    var initialVal = currentDate ? currentDate.substring(0, 10) : today;

    var fieldLabels = {
        'sent_date': '📤 পাঠানোর তারিখ (Sent Date)',
        'back_date': '📥 ফেরত আসার তারিখ (Back Date)',
        'delivery_date': '🚚 ডেলিভারি তারিখ (Delivery Date)'
    };
    var title = fieldLabels[fieldName] || 'তারিখ নির্ধারণ করুন';

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: title,
            html:
                '<div class="text-left mt-2">' +
                    '<label class="block text-xs font-bold text-gray-700 mb-1">তারিখ নির্বাচন করুন:</label>' +
                    '<input type="date" id="swal_date_input" value="' + initialVal + '" class="w-full border rounded-xl p-2.5 text-sm outline-none border-gray-300 focus:ring-2 focus:ring-blue-400">' +
                '</div>',
            showCancelButton: true,
            confirmButtonText: '💾 তারিখ সেভ করুন',
            cancelButtonText: 'বাতিল',
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#64748b',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-2xl shadow-2xl border border-slate-100 p-6',
                confirmButton: 'px-5 py-2.5 rounded-xl font-bold text-sm',
                cancelButton: 'px-5 py-2.5 rounded-xl font-bold text-sm'
            },
            preConfirm: function () {
                var val = document.getElementById('swal_date_input').value;
                if (!val) {
                    Swal.showValidationMessage('অনুগ্রহ করে একটি সঠিক তারিখ নির্বাচন করুন!');
                    return false;
                }
                return val;
            }
        }).then(function (result) {
            if (result.isConfirmed && result.value) {
                saveDateToBackend(id, fieldName, result.value);
            }
        });
    } else {
        var inputDate = prompt(title + ' (YYYY-MM-DD):', initialVal);
        if (inputDate !== null && inputDate.trim() !== '') {
            saveDateToBackend(id, fieldName, inputDate.trim());
        }
    }
}

function saveDateToBackend(id, fieldName, dateValue) {
    var csrfElement = document.querySelector('meta[name="csrf-token"]');
    var csrfToken = csrfElement ? csrfElement.getAttribute('content') : '';

    fetch('/service/external/' + id + '/field', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ field: fieldName, value: dateValue })
    })
        .then(function (r) { return r.json(); })
        .then(function (res) {
            if (res.success) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'তারিখ সফলভাবে আপডেট হয়েছে!',
                        timer: 1200,
                        showConfirmButton: false
                    });
                }
                loadExternalCounts();
                loadExternalServices(typeof currentFilter !== 'undefined' ? currentFilter : 'all', typeof searchQuery !== 'undefined' ? searchQuery : '');
            } else {
                alert('❌ তারিখ সেভ করা সম্ভব হয়নি: ' + (res.message || ''));
            }
        })
        .catch(function (err) {
            console.error('Date update error:', err);
            alert('❌ সার্ভারে কানেক্ট করতে সমস্যা হয়েছে।');
        });
}
window.extSetDate = extSetDate;

function extUpdateStatus(id, newStatus) {
    var doUpdate = function () {
        var csrfElement = document.querySelector('meta[name="csrf-token"]');
        var csrfToken = csrfElement ? csrfElement.getAttribute('content') : '';

        fetch('/service/external/' + id + '/status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ status: newStatus })
        })
        .then(function (r) { return r.json(); })
        .then(function (res) {
            if (res.success) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'স্ট্যাটাস আপডেট সফল!',
                        timer: 1200,
                        showConfirmButton: false
                    });
                }
                loadExternalCounts();
                loadExternalServices(typeof currentFilter !== 'undefined' ? currentFilter : 'all', typeof searchQuery !== 'undefined' ? searchQuery : '');
            } else {
                alert('❌ স্ট্যাটাস আপডেট সম্ভব হয়নি: ' + (res.message || ''));
                loadExternalServices(typeof currentFilter !== 'undefined' ? currentFilter : 'all', typeof searchQuery !== 'undefined' ? searchQuery : '');
            }
        })
        .catch(function (err) {
            console.error('Update external status error:', err);
            alert('❌ সার্ভার ত্রুটি হয়েছে।');
            loadExternalServices(typeof currentFilter !== 'undefined' ? currentFilter : 'all', typeof searchQuery !== 'undefined' ? searchQuery : '');
        });
    };

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'স্ট্যাটাস পরিবর্তন নিশ্চিতকরণ',
            text: 'আপনি কি নিশ্চিত যে স্ট্যাটাস পরিবর্তন করতে চান?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'হ্যাঁ, নিশ্চিত করুন',
            cancelButtonText: 'বাতিল',
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#64748b',
            reverseButtons: true
        }).then(function (result) {
            if (result.isConfirmed) {
                doUpdate();
            } else {
                loadExternalServices(typeof currentFilter !== 'undefined' ? currentFilter : 'all', typeof searchQuery !== 'undefined' ? searchQuery : '');
            }
        });
    } else {
        doUpdate();
    }
}
window.extUpdateStatus = extUpdateStatus;

function extEditField(id, fieldName) {
    var newValue = prompt('নতুন তথ্য প্রবেশ করান:');
    if (newValue !== null && newValue.trim() !== '') {
        var csrfElement = document.querySelector('meta[name="csrf-token"]');
        var csrfToken = csrfElement ? csrfElement.getAttribute('content') : '';

        fetch('/service/external/' + id + '/field', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ field: fieldName, value: newValue.trim() })
        })
        .then(function (r) { return r.json(); })
        .then(function (res) {
            if (res.success) {
                loadExternalServices(typeof currentFilter !== 'undefined' ? currentFilter : 'all', typeof searchQuery !== 'undefined' ? searchQuery : '');
            } else {
                alert('❌ ফিল্ড আপডেট করা সম্ভব হয়নি!');
            }
        });
    }
}
window.extEditField = extEditField;


// ============================================================
// LOAD DASHBOARD COUNTS
// ============================================================

async function loadExternalCounts() {

    // console.log('Loading External Counts...');

    try {
        const response = await fetch(EXTERNAL_COUNTS_URL, {
            method: 'GET',
            headers: { 'Accept': 'application/json' }
        });

        const result = await response.json();
        // console.log('External Counts:', result);

        if (!response.ok || !result.success) {
            console.error(result.message || 'Count load failed');
            return;
        }

        const set = function (id, val) {
            const el = document.getElementById(id);
            if (el) el.textContent = val ?? 0;
        };

        set('ext_count_total',     result.total);
        set('ext_count_pending',   result.pending);
        set('ext_count_sent',      result.sent);
        set('ext_count_back',      result.back);
        set('ext_count_ready',     result.ready);
        set('ext_count_delivered', result.delivered);

    } catch (error) {
        console.error('External Count Load Error:', error);
    }
}


// ============================================================
// DATE FORMAT
// ============================================================

function formatExternalDate(date) {
    if (!date) return '-';
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
    } catch (e) {}

    const parts = String(date).split('-');
    if (parts.length === 3) {
        return parts[0] + '-' + parts[1] + '-' + parts[2];
    }
    return date;
}


// ============================================================
// HTML ESCAPE
// ============================================================

function escapeExternalHtml(value) {
    if (value === null || value === undefined) return '';
    return String(value)
        .replace(/&/g,  '&amp;')
        .replace(/</g,  '&lt;')
        .replace(/>/g,  '&gt;')
        .replace(/"/g,  '&quot;')
        .replace(/'/g,  '&#039;');
}
