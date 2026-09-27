// ================================
// company.js — কোম্পানি কল বুকিং ট্যাবের সব লজিক
// ================================

var companyBookings = [];
var companyBookingsRequest = 0;

function normalizeInput(value) {
    return String(value || '').replace(/[\u0000-\u001F\u007F]/g, '').trim();
}

function validateCompanyBooking(values) {
    if (!/^[6-9]\d{9}$/.test(values.phone)) {
        return 'সঠিক ১০ সংখ্যার মোবাইল নম্বর দিন।';
    }

    if (!/^\d{6}$/.test(values.pincode)) {
        return 'পিন কোড অবশ্যই ৬ সংখ্যার হতে হবে।';
    }

    if (!values.name || values.name.length > 255) {
        return 'কাস্টমারের নাম সঠিকভাবে দিন।';
    }

    if (!values.address || values.address.length > 500) {
        return 'কাস্টমারের ঠিকানা সঠিকভাবে দিন।';
    }

    if (!values.productName || values.productName.length > 255) {
        return 'প্রথম product/model সঠিকভাবে দিন।';
    }

    if (values.secondUnitVisible && (!values.productName2 || values.productName2.length > 255)) {
        return 'দ্বিতীয় product/model সঠিকভাবে দিন।';
    }

    if (!values.serialNo || values.serialNo.length > 255 ||
        (values.serialNo2 && values.serialNo2.length > 255)) {
        return 'Serial number সঠিকভাবে দিন।';
    }

    if (values.caseId1.length > 255 || values.caseId2.length > 255) {
        return 'Case ID সর্বোচ্চ ২৫৫ অক্ষরের হতে পারে।';
    }

    if (values.remarks.length > 1000) {
        return 'Remarks সর্বোচ্চ ১০০০ অক্ষরের হতে পারে।';
    }

    return '';
}

/*
|--------------------------------------------------------------------------
| ⭐ TOGGLE FALLBACK INPUT VISIBILITY
|--------------------------------------------------------------------------
| dropdown-এ কোনো real option (ফাঁকা "-- নির্বাচন করুন --" ছাড়া) না থাকলে,
| manual fallback input দেখানো হবে যাতে ইউজার নিজে হাতে লিখতে পারে।
|--------------------------------------------------------------------------
*/
function toggleProductFallback(selectEl, fallbackEl) {
    if (!selectEl || !fallbackEl) {
        return;
    }

    var hasRealOptions = Array.from(selectEl.options).some(function (opt) { return opt.value !== ''; });

    if (hasRealOptions) {
        fallbackEl.classList.add('hidden');
        fallbackEl.value = '';
    } else {
        fallbackEl.classList.remove('hidden');
    }
}

/*
|--------------------------------------------------------------------------
| FETCH CUSTOMER (real backend lookup by phone) — also called from ocr.js
| after a phone number is detected in a scanned bill/invoice.
|--------------------------------------------------------------------------
*/
async function fetchCustomer(phone) {
    var customerFetchStatus = document.getElementById('customerFetchStatus');
    var productSelect = document.getElementById('comp_product');
    var productSelect2 = document.getElementById('comp_product_2');
    var serialInput = document.getElementById('comp_serial');
    var serialInput2 = document.getElementById('comp_serial_2');
    var selectedProduct = document.getElementById('selectedProduct');
    var selectedProduct2 = document.getElementById('selectedProduct_2');
    var secondProductWrap = document.getElementById('secondProductWrap');
    var productManual = document.getElementById('comp_product_manual');
    var productManual2 = document.getElementById('comp_product_2_manual');

    if (!customerFetchStatus || !productSelect || !productSelect2 || !serialInput || !serialInput2 ||
        !selectedProduct || !selectedProduct2 || !secondProductWrap || !productManual || !productManual2) {
        return;
    }

    customerFetchStatus.innerText = '⏳ Customer খোঁজা হচ্ছে...';
    customerFetchStatus.className = 'text-xs mt-1 text-blue-600';

    try {
        var fetchUrl = (typeof CUSTOMER_BY_PHONE_URL !== 'undefined' ? CUSTOMER_BY_PHONE_URL : '/admin/customer/by-phone') + '?phone=' + encodeURIComponent(phone);
        var response = await fetch(fetchUrl, {
            method: 'GET',
            headers: { 'Accept': 'application/json' }
        });

        var data = await response.json();
        // console.log('Customer response:', data);

        if (data.success && data.customer) {
            var customer = data.customer;

            if (customer.name) document.getElementById('comp_name').value = customer.name;
            if (customer.address) document.getElementById('comp_address').value = customer.address;
            if (customer.pincode) document.getElementById('comp_pincode').value = customer.pincode;

            var billDate = document.getElementById('comp_bill_date');
            if (customer.sale && customer.sale.created_at) {
                billDate.value = customer.sale.created_at.split('T')[0];
            } else {
                billDate.value = new Date().toISOString().split('T')[0];
            }

            // Reset product dropdowns / serials / product ids (1st & 2nd unit)
            productSelect.innerHTML = '<option value="">-- Product নির্বাচন করুন --</option>';
            productSelect2.innerHTML = '<option value="">-- SL No নির্বাচন করুন --</option>';
            serialInput.value = '';
            serialInput2.value = '';
            selectedProduct.value = '';
            selectedProduct2.value = '';
            secondProductWrap.classList.add('hidden');

            if (customer.saleitems && customer.saleitems.length > 0) {
                var acFound = false;
                customer.saleitems.forEach(function (item) {
                    var brandName = (item.product && item.product.brand && item.product.brand.name) || '';
                    var catName = (item.product && item.product.category && item.product.category.name) || '';
                    var modelName = (item.product && (item.product.name || item.product.model)) || '';
                    var fullText = (brandName + ' ' + catName + ' ' + modelName + ' ' + (item.sl_no || '')).trim();

                    var itemIsAc = /(\bAC\b|AIR\s*CONDITIONER|SPLIT|WINDOW|\bCOOLER\b)/i.test(fullText);
                    if (itemIsAc) acFound = true;

                    var option1 = document.createElement('option');
                    option1.value = item.sl_no || '';
                    option1.textContent = brandName + ' ' + (item.sl_no || '');
                    option1.dataset.productId = item.product_id || '';
                    option1.dataset.serialNo = item.serial_no || '';
                    option1.dataset.isAc = itemIsAc ? 'true' : 'false';
                    option1.dataset.fullText = fullText;
                    if (item.id) option1.dataset.saleItemId = item.id;
                    productSelect.appendChild(option1);

                    var option2 = document.createElement('option');
                    option2.value = item.sl_no || '';
                    option2.textContent = brandName + ' ' + (item.sl_no || '');
                    option2.dataset.productId = item.product_id || '';
                    option2.dataset.serialNo = item.serial_no || '';
                    option2.dataset.isAc = itemIsAc ? 'true' : 'false';
                    if (item.id) option2.dataset.saleItemId = item.id;
                    productSelect2.appendChild(option2);
                });

                if (customer.saleitems.length > 0) {
                    productSelect.selectedIndex = 1;
                    productSelect.dispatchEvent(new Event('change'));
                }

                // If brand/product is AC -> show 2 serial inputs (secondProductWrap). If not AC -> show 1 serial input
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
                productSelect.innerHTML = '<option value="">কোনো Sale Item পাওয়া যায়নি</option>';
            }

            toggleProductFallback(productSelect, productManual);
            toggleProductFallback(productSelect2, productManual2);

            customerFetchStatus.innerText = '✅ Customer information পাওয়া গেছে';
            customerFetchStatus.className = 'text-xs mt-1 text-green-600 font-semibold';

        } else {
            customerFetchStatus.innerText = '⚠️ এই ফোন নম্বরে Customer পাওয়া যায়নি';
            customerFetchStatus.className = 'text-xs mt-1 text-orange-600';

            document.getElementById('comp_name').value = '';
            document.getElementById('comp_address').value = '';
            document.getElementById('comp_pincode').value = '';

            productSelect.innerHTML = '<option value="">-- Product নির্বাচন করুন --</option>';
            productSelect2.innerHTML = '<option value="">-- SL No নির্বাচন করুন --</option>';
            serialInput.value = '';
            serialInput2.value = '';
            selectedProduct.value = '';
            selectedProduct2.value = '';
            secondProductWrap.classList.add('hidden');

            toggleProductFallback(productSelect, productManual);
        }

    } catch (error) {
        console.error('Customer fetch error:', error);
        customerFetchStatus.innerText = '❌ Customer data fetch করতে সমস্যা হয়েছে';
        customerFetchStatus.className = 'text-xs mt-1 text-red-600';
    }
}

/*
|--------------------------------------------------------------------------
| ⭐ INIT COMPANY FORM
|--------------------------------------------------------------------------
| Called every time the company tab is opened (from selectService in
| common.js), right after the <template> content is cloned into #formArea.
| Re-queries every element and re-binds every listener fresh — this must
| run on every open, not just once at page load, otherwise listeners stay
| attached to elements from a previous (now-discarded) clone.
|--------------------------------------------------------------------------
*/
function initCompanyForm() {
    var form = document.getElementById('companyBookingForm');
    var phoneInput = document.getElementById('comp_phone');
    var productSelect = document.getElementById('comp_product');
    var serialInput = document.getElementById('comp_serial');
    var selectedProduct = document.getElementById('selectedProduct');
    var productSelect2 = document.getElementById('comp_product_2');
    var serialInput2 = document.getElementById('comp_serial_2');
    var selectedProduct2 = document.getElementById('selectedProduct_2');
    var secondProductWrap = document.getElementById('secondProductWrap');
    var productManual = document.getElementById('comp_product_manual');
    var productManual2 = document.getElementById('comp_product_2_manual');

    if (!form || !phoneInput || !productSelect || !serialInput || !selectedProduct ||
        !productSelect2 || !serialInput2 || !selectedProduct2 || !secondProductWrap ||
        !productManual || !productManual2) {
        return;
    }

    if (form.dataset.initialized === 'true') {
        return;
    }

    form.dataset.initialized = 'true';

    loadCompanyBookings();

    // ⭐ পেজ খোলার সাথে সাথেই ১ম ইউনিটের fallback দেখাও (dropdown শুরুতে ফাঁকা)
    toggleProductFallback(productSelect, productManual);

    phoneInput.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '');
        if (this.value.length === 10) fetchCustomer(this.value);
    });

    var pincodeInput = document.getElementById('comp_pincode');
    pincodeInput.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 6);
    });

    productSelect.addEventListener('change', function () {
        var selectedOption = this.options[this.selectedIndex];
        if (!selectedOption || !selectedOption.value) {
            serialInput.value = '';
            selectedProduct.value = '';
        } else {
            selectedProduct.value = selectedOption.dataset.productId || '';
            serialInput.value = selectedOption.dataset.serialNo || selectedOption.value || '';
        }

        // AC check: If AC brand/product, show 2 serial inputs; otherwise hide 2nd serial input
        var fullText = (selectedOption ? (selectedOption.dataset.fullText || selectedOption.textContent) : '') || '';
        var isAc = (selectedOption && selectedOption.dataset.isAc === 'true') || /(\bAC\b|AIR\s*CONDITIONER|SPLIT|WINDOW|\bCOOLER\b)/i.test(fullText);

        if (isAc) {
            secondProductWrap.classList.remove('hidden');
        } else {
            secondProductWrap.classList.add('hidden');
            serialInput2.value = '';
            selectedProduct2.value = '';
        }
    });

    if (productManual) {
        productManual.addEventListener('input', function () {
            if (/(\bAC\b|AIR\s*CONDITIONER|SPLIT|WINDOW|\bCOOLER\b)/i.test(this.value)) {
                secondProductWrap.classList.remove('hidden');
            } else {
                secondProductWrap.classList.add('hidden');
                serialInput2.value = '';
                selectedProduct2.value = '';
            }
        });
    }

    productSelect2.addEventListener('change', function () {
        var selectedOption = this.options[this.selectedIndex];
        if (!selectedOption || !selectedOption.value) {
            serialInput2.value = '';
            selectedProduct2.value = '';
            return;
        }
        selectedProduct2.value = selectedOption.dataset.productId || '';
        serialInput2.value = selectedOption.dataset.serialNo || selectedOption.value || '';
    });

    form.addEventListener('submit', function (event) {
        event.preventDefault();

        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        var phone = normalizeInput(document.getElementById('comp_phone').value).replace(/\D/g, '');
        var name = normalizeInput(document.getElementById('comp_name').value);
        var slNo = document.getElementById('comp_product').value;
        var productId = document.getElementById('selectedProduct').value;
        var serialNo = normalizeInput(document.getElementById('comp_serial').value);
        var productName = normalizeInput(slNo ? slNo : productManual.value);

        var slNo2 = document.getElementById('comp_product_2').value;
        var productId2 = document.getElementById('selectedProduct_2').value;
        var serialNo2 = normalizeInput(document.getElementById('comp_serial_2').value);
        var productName2 = normalizeInput(slNo2 ? slNo2 : productManual2.value);

        var caseId1Elem = document.getElementById('comp_case_1');
        var caseId1 = caseId1Elem ? normalizeInput(caseId1Elem.value) : '';
        var caseId2Elem = document.getElementById('comp_case_2');
        var caseId2 = caseId2Elem ? normalizeInput(caseId2Elem.value) : '';

        var address = normalizeInput(document.getElementById('comp_address').value);
        var pincode = normalizeInput(document.getElementById('comp_pincode').value).replace(/\D/g, '');
        var billDate = document.getElementById('comp_bill_date').value;
        var bookingDate = document.getElementById('comp_booking_date').value;
        var remarks = normalizeInput(document.getElementById('comp_remarks').value);

        var validationError = validateCompanyBooking({
            phone: phone,
            name: name,
            address: address,
            pincode: pincode,
            productName: productName,
            productName2: productName2,
            serialNo: serialNo,
            serialNo2: serialNo2,
            caseId1: caseId1,
            caseId2: caseId2,
            remarks: remarks,
            secondUnitVisible: !secondProductWrap.classList.contains('hidden')
        });

        if (validationError) {
            alert('⚠️ ' + validationError);
            return;
        }

        var statusBox = document.getElementById('bookingSubmitStatus');
        var submitBtn = document.getElementById('bookingSubmitBtn');

        function showStatus(message, type) {
            statusBox.innerText = message;
            statusBox.classList.remove('hidden', 'bg-green-50', 'text-green-700', 'bg-red-50', 'text-red-700');
            statusBox.classList.add(type === 'success' ? 'bg-green-50' : 'bg-red-50', type === 'success' ? 'text-green-700' : 'text-red-700');
        }

        var csrfMeta = document.querySelector('meta[name="csrf-token"]');
        var csrfToken = csrfMeta ? csrfMeta.content : '';

        var formData = new FormData();
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

        var invoiceFileInput = document.getElementById('invoiceFile');
        if (invoiceFileInput && invoiceFileInput.files.length > 0) {
            formData.append('invoice_image', invoiceFileInput.files[0]);
        }

        submitBtn.disabled = true;
        submitBtn.innerText = '⏳ সেভ করা হচ্ছে...';

        var storeUrl = typeof COMPANY_STORE_URL !== 'undefined' ? COMPANY_STORE_URL : '/admin/service-booking';
        fetch(storeUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: formData
        })
            .then(async function (response) {
                var data = await response.json();
                if (!response.ok) throw new Error(data.message || 'সেভ করতে সমস্যা হয়েছে');
                return data;
            })
            .then(function (data) {
                showStatus('✅ ' + (data.message || 'কল বুকিং সফলভাবে সেভ হয়েছে।'), 'success');

                form.reset();

                productSelect.innerHTML = '<option value="">-- SL No নির্বাচন করুন --</option>';
                productSelect2.innerHTML = '<option value="">-- SL No নির্বাচন করুন --</option>';
                selectedProduct.value = '';
                selectedProduct2.value = '';
                secondProductWrap.classList.add('hidden');

                toggleProductFallback(productSelect, productManual);
                toggleProductFallback(productSelect2, productManual2);

                document.getElementById('customerFetchStatus').innerText = '';
                document.getElementById('comp_booking_date').value = new Date().toISOString().split('T')[0];
                document.getElementById('comp_bill_date').value = new Date().toISOString().split('T')[0];

                loadCompanyBookings();
            })
            .catch(function (error) {
                console.error('Booking save error:', error);
                showStatus('❌ ' + error.message, 'error');
            })
            .finally(function () {
                submitBtn.disabled = false;
                submitBtn.innerText = '💾 কল বুকিং সেভ করুন';
            });
    });
}

function loadCompanyBookings() {
    var requestId = ++companyBookingsRequest;

    var listUrl = typeof COMPANY_LIST_URL !== 'undefined' ? COMPANY_LIST_URL : '/admin/service-bookings';
    return fetch(listUrl, {
        method: 'GET',
        headers: { 'Accept': 'application/json' }
    })
        .then(function (response) {
            if (!response.ok) {
                throw new Error('বুকিং তালিকা লোড করা যায়নি');
            }

            return response.json();
        })
        .then(function (data) {
            if (requestId !== companyBookingsRequest || !data.success) {
                return;
            }

            companyBookings = (data.bookings || []).map(function (booking) {
                return {
                    id: String(booking.id),
                    phone: booking.phone || '',
                    name: booking.name || '',
                    address: booking.address || '',
                    pincode: booking.pin || '',
                    product: booking.product_name || booking.sl_no || '-',
                    product2: booking.product_name_2 || booking.sl_no_2 || '',
                    serial: booking.serial_no || '',
                    serial2: booking.serial_no_2 || '',
                    booking_date: booking.call_date || '',
                    case_id_date: booking.case_id_date || '',
                    created_at: booking.case_id_date || booking.created_at || '',
                    case_id_1: booking.case_id_1 || '',
                    case_id_2: booking.case_id_2 || '',
                    status: booking.status || 'Pending'
                };
            });

            updateDashboardAndTable();
        })
        .catch(function (error) {
            console.error('Booking list load error:', error);
        });
}

function updateCompanyDashboard() {
    var total = companyBookings.length;
    var nocase = companyBookings.filter(function (b) { return !b.case_id_1; }).length;
    var visited = companyBookings.filter(function (b) { return b.status === 'Engineer Visited'; }).length;
    var completed = companyBookings.filter(function (b) { return b.status === 'Completed'; }).length;

    if (document.getElementById('comp_count_total')) document.getElementById('comp_count_total').innerText = total;
    if (document.getElementById('comp_count_nocase')) document.getElementById('comp_count_nocase').innerText = nocase;
    if (document.getElementById('comp_count_visited')) document.getElementById('comp_count_visited').innerText = visited;
    if (document.getElementById('comp_count_completed')) document.getElementById('comp_count_completed').innerText = completed;
}

function formatCompanyDate(date) {
    if (!date) return 'N/A';
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
    return date;
}

function renderCompanyTable(thead, tbody) {
    var list = applySearchFilter(companyBookings);

    if (currentFilter !== 'all') {
        if (currentFilter === 'nocase') list = list.filter(function (b) { return !b.case_id_1; });
        else if (currentFilter === 'visited') list = list.filter(function (b) { return b.status === 'Engineer Visited'; });
        else if (currentFilter === 'completed') list = list.filter(function (b) { return b.status === 'Completed'; });
    }

    thead.innerHTML = '<tr class="bg-blue-50 border-b text-blue-900">' +
        '<th class="p-3">আইডি</th><th class="p-3">কাস্টমার ও ফোন</th><th class="p-3">প্রোডাক্ট</th><th class="p-3">কেস আইডি</th><th class="p-3">কল বুকিং তারিখ ও সময়</th><th class="p-3">কল বুকের বয়স (Aging)</th><th class="p-3">স্ট্যাটাস</th><th class="p-3 text-center">অ্যাকশন</th></tr>';

    if (list.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center p-6 text-gray-400">কোনো তথ্য পাওয়া যায়নি।</td></tr>';
        return;
    }

    var html = '';
    for (var i = 0; i < list.length; i++) {
        var item = list[i];
        var days = calculateDays(item.booking_date || item.created_at);
        var caseBtn = item.case_id_1 ?
            '<span class="bg-blue-100 text-blue-800 font-bold px-2 py-1 rounded text-xs">' + item.case_id_1 + '</span>' :
            (item.status === 'Completed' ? '<span class="text-xs text-gray-400">লক করা আছে</span>' :
                '<button onclick="editField(\'' + item.id + '\', \'case_id_1\')" class="text-xs bg-red-100 text-red-600 hover:bg-red-200 px-2 py-1 rounded font-bold">➕ কেস আইডি দিন</button>');

        var statusOptions =
            '<option value="Pending" ' + (item.status === 'Pending' ? 'selected disabled' : 'disabled') + '>পেন্ডিং</option>' +
            '<option value="Engineer Visited" ' + (item.status === 'Engineer Visited' ? 'selected' : '') + '>ইঞ্জিনিয়ার ভিজিটেড</option>' +
            '<option value="Completed" ' + (item.status === 'Completed' ? 'selected' : '') + '>কমপ্লিট</option>' +
            '<option value="Canceled" ' + (item.status === 'Canceled' ? 'selected' : '') + '>ক্যান্সেল</option>';

        html += '<tr class="border-b hover:bg-gray-50">' +
            '<td class="p-3 font-semibold text-xs">' + item.id + '</td>' +
            '<td class="p-3"><div class="font-bold text-gray-800">' + item.name + '</div><div class="text-xs text-gray-500">📞 ' + item.phone + '</div>' + (item.address || item.pincode ? '<div class="text-xs text-gray-600 mt-0.5">📍 ' + (item.address || '') + (item.pincode ? ' (PIN: ' + item.pincode + ')' : '') + '</div>' : '') + '</td>' +
            '<td class="p-3"><div class="text-xs font-semibold">' + item.product + (item.product2 ? ' + ' + item.product2 : '') + '</div><div class="text-xs text-gray-500">S/N: ' + item.serial + (item.serial2 ? ', ' + item.serial2 : '') + '</div></td>' +
            '<td class="p-3">' + caseBtn + (item.case_id_2 ? '<span class="bg-purple-100 text-purple-800 font-bold px-2 py-1 rounded text-xs mt-1 block">' + item.case_id_2 + '</span>' : '') + '</td>' +
            '<td class="p-3 text-xs font-medium text-gray-700">' + formatCompanyDate(item.case_id_date || item.booking_date || item.created_at) + '</td>' +
            '<td class="p-3 text-xs"><span class="bg-blue-100 text-blue-800 font-bold px-2.5 py-1 rounded-full">' + days + '</span></td>' +
            '<td class="p-3">' +
            '<select onchange="updateStatus(\'' + item.id + '\', this.value)" ' + (item.status === 'Completed' ? 'disabled' : '') + ' class="text-xs border rounded p-1 font-semibold outline-none bg-white shadow-sm">' +
            statusOptions +
            '</select>' +
            '</td>' +
            '<td class="p-3 text-center"><button onclick="openEditModal(\'' + item.id + '\')" class="text-amber-600 hover:text-amber-800 hover:underline text-xs font-bold mr-2">✏️ এডিট</button><button onclick="printReceipt(\'' + item.id + '\')" class="text-blue-600 hover:underline text-xs font-bold mr-2">🖨️ প্রিন্ট</button><button onclick="deleteRecord(\'' + item.id + '\')" class="text-red-500 hover:text-red-700 text-xs font-bold">🗑️ ডিলিট</button></td>' +
            '</tr>';
    }
    tbody.innerHTML = html;
}
