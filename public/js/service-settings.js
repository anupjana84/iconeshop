// ================================
// service-settings.js
// Icon Computer — Service Settings Hub logic
// ================================

// Centralized Customer Database for Instant Auto-Fill
var customerDatabase = {
    "8597753337": { name: "MS APARNA DEY", address: "BETHUADAHARI NADIA", pincode: "741126" },
    "9800000000": { name: "RITESH ROY", address: "KRISHNANAGAR NADIA", pincode: "741101" }
};

// Central Data Stores
var currentTab = 'company';
var currentFilter = 'all';
var searchQuery = '';

var companyBookings = [];
var externalBookings = [];
var inhouseBookings = [];

// Helper: Calculate Days Difference (Aging)
function calculateDays(startDate) {
    if (!startDate) return '0 দিন';
    var start = new Date(startDate);
    var today = new Date();
    var diffTime = Math.abs(today - start);
    var diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) - 1;
    return (diffDays < 0 ? 0 : diffDays) + ' দিন';
}

// Tab Switcher Function
function selectService(type) {
    currentTab = type;
    currentFilter = 'all';
    searchQuery = '';

    var area = document.getElementById('formArea');
    var tableArea = document.getElementById('tableSectionArea');
    tableArea.classList.remove('hidden');

    // Reset Tab Styles
    document.querySelectorAll('.service-tab-btn').forEach(function(btn) {
        btn.classList.remove('ring-4', 'ring-offset-2');
    });

    if (type === 'company') {
        document.getElementById('btn-company').classList.add('ring-4', 'ring-blue-300');
        area.innerHTML = document.getElementById('companyFormTemplate').innerHTML;
    } else if (type === 'external') {
        document.getElementById('btn-external').classList.add('ring-4', 'ring-orange-300');
        area.innerHTML = document.getElementById('externalFormTemplate').innerHTML;
    } else if (type === 'inhouse') {
        document.getElementById('btn-inhouse').classList.add('ring-4', 'ring-green-300');
        area.innerHTML = document.getElementById('inhouseFormTemplate').innerHTML;
    }

    var searchInput = document.getElementById('globalSearchInput');
    if (searchInput) searchInput.value = '';

    updateDashboardAndTable();
}

// Auto-Fill Customer Info on Typing Phone
function searchCustomer(phone, prefix) {
    var cleanPhone = phone.trim();
    if (customerDatabase[cleanPhone]) {
        var cust = customerDatabase[cleanPhone];
        if (document.getElementById(prefix + '_name')) document.getElementById(prefix + '_name').value = cust.name;
        if (document.getElementById(prefix + '_address')) document.getElementById(prefix + '_address').value = cust.address;
        if (document.getElementById(prefix + '_pincode')) document.getElementById(prefix + '_pincode').value = cust.pincode;
    }
}

function saveCustomerToDatabase(phone, name, address, pincode) {
    if (phone && !customerDatabase[phone]) {
        customerDatabase[phone] = { name: name, address: address, pincode: pincode };
    }
}

// Filtering by Clicking Dashboard Cards
function filterData(statusKey) {
    currentFilter = statusKey;
    var badge = document.getElementById('activeFilterBadge');
    if (badge) {
        badge.innerText = 'ফিল্টার: ' + statusKey.toUpperCase();
    }
    renderTable();
}

function handleSearch(val) {
    searchQuery = val.trim().toLowerCase();
    renderTable();
}

// Save Handlers
function saveCompanyBooking(e) {
    e.preventDefault();
    var phone = document.getElementById('comp_phone').value.trim();
    var name = document.getElementById('comp_name').value.trim();
    var address = document.getElementById('comp_address').value.trim();
    var pincode = document.getElementById('comp_pincode').value.trim();

    saveCustomerToDatabase(phone, name, address, pincode);

    companyBookings.push({
        id: 'COMP-' + Date.now().toString().slice(-4),
        phone: phone,
        name: name,
        address: address,
        pincode: pincode,
        product: document.getElementById('comp_product').value,
        serial: document.getElementById('comp_serial').value,
        bill_date: document.getElementById('comp_bill_date').value,
        booking_date: document.getElementById('comp_booking_date').value,
        case_id_1: document.getElementById('comp_case_1').value.trim(),
        case_id_2: document.getElementById('comp_case_2').value.trim(),
        status: 'Pending',
        created_at: new Date().toISOString().split('T')[0]
    });

    alert('✅ কোম্পানি কল বুকিং সেভ করা হয়েছে!');
    updateDashboardAndTable();
}

function saveExternalService(e) {
    e.preventDefault();
    var phone = document.getElementById('ext_phone').value.trim();
    var name = document.getElementById('ext_name').value.trim();
    var address = document.getElementById('ext_address').value.trim();
    var pincode = document.getElementById('ext_pincode').value.trim();

    saveCustomerToDatabase(phone, name, address, pincode);

    externalBookings.push({
        id: 'EXT-' + Date.now().toString().slice(-4),
        phone: phone,
        name: name,
        address: address,
        pincode: pincode,
        product: document.getElementById('ext_product').value,
        serial: document.getElementById('ext_serial').value || 'N/A',
        budget: document.getElementById('ext_budget').value || '0',
        final_cost: document.getElementById('ext_final_cost').value || '0',
        receive_date: document.getElementById('ext_receive_date').value,
        vendor: document.getElementById('ext_vendor').value || 'নির্ধারণ করা হয়নি',
        sent_date: document.getElementById('ext_sent_date').value,
        back_date: document.getElementById('ext_back_date').value,
        delivery_date: document.getElementById('ext_delivery_date').value,
        status: 'Pending',
        created_at: new Date().toISOString().split('T')[0]
    });

    alert('✅ বাইরের সার্ভিস তথ্য সফলভাবে সেভ করা হয়েছে!');
    updateDashboardAndTable();
}

function saveInhouseService(e) {
    e.preventDefault();
    var phone = document.getElementById('inh_phone').value.trim();
    var name = document.getElementById('inh_name').value.trim();
    var address = document.getElementById('inh_address').value.trim();
    var pincode = document.getElementById('inh_pincode').value.trim();

    saveCustomerToDatabase(phone, name, address, pincode);

    inhouseBookings.push({
        id: 'INH-' + Date.now().toString().slice(-4),
        phone: phone,
        name: name,
        address: address,
        pincode: pincode,
        product: document.getElementById('inh_product').value,
        estimate: document.getElementById('inh_estimate').value || '0',
        final_amount: document.getElementById('inh_final_amount').value || '0',
        receive_date: document.getElementById('inh_receive_date').value,
        delivery_date: document.getElementById('inh_delivery_date').value,
        status: 'Pending',
        created_at: new Date().toISOString().split('T')[0]
    });

    alert('✅ ইন-হাউস সার্ভিস রেকর্ড সফলভাবে সেভ হয়েছে!');
    updateDashboardAndTable();
}

// Dashboard Counters Update
function updateDashboardAndTable() {
    if (currentTab === 'company') {
        var total = companyBookings.length;
        var nocase = companyBookings.filter(function(b) { return !b.case_id_1; }).length;
        var visited = companyBookings.filter(function(b) { return b.status === 'Engineer Visited'; }).length;
        var completed = companyBookings.filter(function(b) { return b.status === 'Completed'; }).length;

        if (document.getElementById('comp_count_total')) document.getElementById('comp_count_total').innerText = total;
        if (document.getElementById('comp_count_nocase')) document.getElementById('comp_count_nocase').innerText = nocase;
        if (document.getElementById('comp_count_visited')) document.getElementById('comp_count_visited').innerText = visited;
        if (document.getElementById('comp_count_completed')) document.getElementById('comp_count_completed').innerText = completed;
    } else if (currentTab === 'external') {
        var total = externalBookings.length;
        var pending = externalBookings.filter(function(b) { return b.status === 'Pending'; }).length;
        var sent = externalBookings.filter(function(b) { return b.status === 'Sent to Center'; }).length;
        var back = externalBookings.filter(function(b) { return b.status === 'Returned from Center'; }).length;
        var ready = externalBookings.filter(function(b) { return b.status === 'Ready/Back'; }).length;
        var delivered = externalBookings.filter(function(b) { return b.status === 'Delivered'; }).length;

        if (document.getElementById('ext_count_total')) document.getElementById('ext_count_total').innerText = total;
        if (document.getElementById('ext_count_pending')) document.getElementById('ext_count_pending').innerText = pending;
        if (document.getElementById('ext_count_sent')) document.getElementById('ext_count_sent').innerText = sent;
        if (document.getElementById('ext_count_back')) document.getElementById('ext_count_back').innerText = back;
        if (document.getElementById('ext_count_ready')) document.getElementById('ext_count_ready').innerText = ready;
        if (document.getElementById('ext_count_delivered')) document.getElementById('ext_count_delivered').innerText = delivered;
    } else if (currentTab === 'inhouse') {
        var total = inhouseBookings.length;
        var pending = inhouseBookings.filter(function(b) { return b.status === 'Pending'; }).length;
        var ready = inhouseBookings.filter(function(b) { return b.status === 'Ready/Repaired'; }).length;
        var delivered = inhouseBookings.filter(function(b) { return b.status === 'Delivered'; }).length;

        if (document.getElementById('inh_count_total')) document.getElementById('inh_count_total').innerText = total;
        if (document.getElementById('inh_count_pending')) document.getElementById('inh_count_pending').innerText = pending;
        if (document.getElementById('inh_count_ready')) document.getElementById('inh_count_ready').innerText = ready;
        if (document.getElementById('inh_count_delivered')) document.getElementById('inh_count_delivered').innerText = delivered;
    }

    renderTable();
}

// Render Master Table
function renderTable() {
    var thead = document.getElementById('mainTableHead');
    var tbody = document.getElementById('mainTableBody');
    if (!thead || !tbody) return;

    var list = [];
    if (currentTab === 'company') list = companyBookings;
    else if (currentTab === 'external') list = externalBookings;
    else if (currentTab === 'inhouse') list = inhouseBookings;

    // Apply Search Filter
    if (searchQuery !== '') {
        list = list.filter(function(item) {
            return item.name.toLowerCase().includes(searchQuery) ||
                   item.phone.toLowerCase().includes(searchQuery) ||
                   item.product.toLowerCase().includes(searchQuery);
        });
    }

    // Apply Dashboard Filter
    if (currentFilter !== 'all') {
        if (currentTab === 'company') {
            if (currentFilter === 'nocase') list = list.filter(function(b) { return !b.case_id_1; });
            else if (currentFilter === 'visited') list = list.filter(function(b) { return b.status === 'Engineer Visited'; });
            else if (currentFilter === 'completed') list = list.filter(function(b) { return b.status === 'Completed'; });
        } else if (currentTab === 'external') {
            if (currentFilter === 'pending') list = list.filter(function(b) { return b.status === 'Pending'; });
            else if (currentFilter === 'sent') list = list.filter(function(b) { return b.status === 'Sent to Center'; });
            else if (currentFilter === 'back') list = list.filter(function(b) { return b.status === 'Returned from Center'; });
            else if (currentFilter === 'ready') list = list.filter(function(b) { return b.status === 'Ready/Back'; });
            else if (currentFilter === 'delivered') list = list.filter(function(b) { return b.status === 'Delivered'; });
        } else {
            if (currentFilter === 'pending') list = list.filter(function(b) { return b.status === 'Pending'; });
            else if (currentFilter === 'ready') list = list.filter(function(b) { return b.status === 'Ready/Repaired'; });
            else if (currentFilter === 'delivered') list = list.filter(function(b) { return b.status === 'Delivered'; });
        }
    }

    // Table Header
    if (currentTab === 'company') {
        thead.innerHTML = '<tr class="bg-blue-50 border-b text-blue-900">' +
            '<th class="p-3">আইডি</th><th class="p-3">কাস্টমার ও ফোন</th><th class="p-3">প্রোডাক্ট</th><th class="p-3">কেস আইডি</th><th class="p-3">কল বুকিং তারিখ</th><th class="p-3">কল বুকের বয়স (Aging)</th><th class="p-3">স্ট্যাটাস</th><th class="p-3 text-center">অ্যাকশন</th></tr>';
    } else if (currentTab === 'external') {
        thead.innerHTML = '<tr class="bg-orange-50 border-b text-orange-900">' +
            '<th class="p-3">আইডি</th><th class="p-3">কাস্টমার ও ফোন</th><th class="p-3">প্রোডাক্ট</th><th class="p-3">ভেন্ডর</th><th class="p-3">কতদিন হলো (Aging)</th><th class="p-3">ফাইনাল চার্জ</th><th class="p-3">স্ট্যাটাস (ড্রপডাউন)</th><th class="p-3 text-center">অ্যাকশন</th></tr>';
    } else if (currentTab === 'inhouse') {
        thead.innerHTML = '<tr class="bg-green-50 border-b text-green-900">' +
            '<th class="p-3">আইডি</th><th class="p-3">কাস্টমার ও ফোন</th><th class="p-3">প্রোডাক্ট/সমস্যা</th><th class="p-3">কতদিন হলো (Aging)</th><th class="p-3">এস্টিমেট / ফাইনাল চার্জ</th><th class="p-3">স্ট্যাটাস (ড্রপডাউন)</th><th class="p-3 text-center">অ্যাকশন</th></tr>';
    }

    if (list.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center p-6 text-gray-400">কোনো তথ্য পাওয়া যায়নি।</td></tr>';
        return;
    }

    var html = '';
    for (var i = 0; i < list.length; i++) {
        var item = list[i];

        if (currentTab === 'company') {
            var days = calculateDays(item.booking_date || item.created_at);
            var caseBtn = item.case_id_1 ?
                '<span class="bg-blue-100 text-blue-800 font-bold px-2 py-1 rounded text-xs">' + item.case_id_1 + '</span>' :
                '<button onclick="editField(\'' + item.id + '\', \'case_id_1\')" class="text-xs bg-red-100 text-red-600 hover:bg-red-200 px-2 py-1 rounded font-bold">➕ কেস আইডি দিন</button>';

            html += '<tr class="border-b hover:bg-gray-50">' +
                '<td class="p-3 font-semibold text-xs">' + item.id + '</td>' +
                '<td class="p-3"><div class="font-bold text-gray-800">' + item.name + '</div><div class="text-xs text-gray-500">📞 ' + item.phone + '</div>' + (item.address || item.pincode ? '<div class="text-xs text-gray-600 mt-0.5">📍 ' + (item.address || '') + (item.pincode ? ' (PIN: ' + item.pincode + ')' : '') + '</div>' : '') + '</td>' +
                '<td class="p-3"><div class="text-xs font-semibold">' + item.product + '</div><div class="text-xs text-gray-500">S/N: ' + item.serial + '</div></td>' +
                '<td class="p-3">' + caseBtn + '</td>' +
                '<td class="p-3 text-xs font-medium text-gray-700">' + (item.case_id_date || item.booking_date || 'N/A') + '</td>' +
                '<td class="p-3 text-xs"><span class="bg-blue-100 text-blue-800 font-bold px-2.5 py-1 rounded-full">' + days + '</span></td>' +
                '<td class="p-3">' +
                    '<select onchange="updateStatus(\'' + item.id + '\', this.value)" class="text-xs border rounded p-1 font-semibold outline-none bg-white shadow-sm">' +
                        '<option value="Pending" ' + (item.status === 'Pending' ? 'selected' : '') + '>পেন্ডিং</option>' +
                        '<option value="Engineer Visited" ' + (item.status === 'Engineer Visited' ? 'selected' : '') + '>ইঞ্জিনিয়ার ভিজিটেড</option>' +
                        '<option value="Completed" ' + (item.status === 'Completed' ? 'selected' : '') + '>কমপ্লিট</option>' +
                    '</select>' +
                '</td>' +
                '<td class="p-3 text-center"><button onclick="openEditModal(\'' + item.id + '\')" class="text-amber-600 hover:text-amber-800 hover:underline text-xs font-bold mr-2">✏️ এডিট</button><button onclick="printReceipt(\'' + item.id + '\')" class="text-blue-600 hover:underline text-xs font-bold mr-2">🖨️ প্রিন্ট</button><button onclick="deleteRecord(\'' + item.id + '\')" class="text-red-500 hover:text-red-700 text-xs font-bold">🗑️ ডিলিট</button></td>' +
            '</tr>';
        }
        else if (currentTab === 'external') {
            var days = calculateDays(item.receive_date);
            html += '<tr class="border-b hover:bg-gray-50">' +
                '<td class="p-3 font-semibold text-xs">' + item.id + '</td>' +
                '<td class="p-3"><div class="font-bold text-gray-800">' + item.name + '</div><div class="text-xs text-gray-500">📞 ' + item.phone + '</div>' + (item.address || item.pincode ? '<div class="text-xs text-gray-600 mt-0.5">📍 ' + (item.address || '') + (item.pincode ? ' (PIN: ' + item.pincode + ')' : '') + '</div>' : '') + '</td>' +
                '<td class="p-3 text-xs font-semibold">' + item.product + '</td>' +
                '<td class="p-3 text-xs"><span class="font-bold text-gray-700">' + item.vendor + '</span> <button onclick="editField(\'' + item.id + '\', \'vendor\')" class="text-blue-500 hover:underline text-[10px]">✏️ এডিট</button></td>' +
                '<td class="p-3 text-xs"><span class="bg-orange-100 text-orange-800 font-bold px-2 py-0.5 rounded">' + days + '</span></td>' +
                '<td class="p-3 text-xs font-bold text-green-700">₹' + item.final_cost + ' <button onclick="editField(\'' + item.id + '\', \'final_cost\')" class="text-blue-500 hover:underline text-[10px]">✏️ এডিট</button></td>' +
                '<td class="p-3">' +
                    '<select onchange="updateStatus(\'' + item.id + '\', this.value)" class="text-xs border rounded p-1 font-semibold outline-none bg-white shadow-sm">' +
                        '<option value="Pending" ' + (item.status === 'Pending' ? 'selected' : '') + '>পেন্ডিং</option>' +
                        '<option value="Sent to Center" ' + (item.status === 'Sent to Center' ? 'selected' : '') + '>সার্ভিস সেন্টারে পাঠানো হয়েছে</option>' +
                        '<option value="Returned from Center" ' + (item.status === 'Returned from Center' ? 'selected' : '') + '>সার্ভিস সেন্টার থেকে চলে এসেছে</option>' +
                        '<option value="Ready/Back" ' + (item.status === 'Ready/Back' ? 'selected' : '') + '>ফেরত এসেছে (ডেলিভারি বাকি)</option>' +
                        '<option value="Delivered" ' + (item.status === 'Delivered' ? 'selected' : '') + '>ডেলিভারি দেওয়া হয়েছে</option>' +
                    '</select>' +
                '</td>' +
                '<td class="p-3 text-center"><button onclick="openEditModal(\'' + item.id + '\')" class="text-amber-600 hover:text-amber-800 hover:underline text-xs font-bold mr-2">✏️ এডিট</button><button onclick="printReceipt(\'' + item.id + '\')" class="text-blue-600 hover:underline text-xs font-bold mr-2">🖨️ প্রিন্ট</button><button onclick="deleteRecord(\'' + item.id + '\')" class="text-red-500 hover:text-red-700 text-xs font-bold">🗑️ ডিলিট</button></td>' +
            '</tr>';
        }
        else if (currentTab === 'inhouse') {
            var days = calculateDays(item.receive_date);
            html += '<tr class="border-b hover:bg-gray-50">' +
                '<td class="p-3 font-semibold text-xs">' + item.id + '</td>' +
                '<td class="p-3"><div class="font-bold text-gray-800">' + item.name + '</div><div class="text-xs text-gray-500">📞 ' + item.phone + '</div>' + (item.address || item.pincode ? '<div class="text-xs text-gray-600 mt-0.5">📍 ' + (item.address || '') + (item.pincode ? ' (PIN: ' + item.pincode + ')' : '') + '</div>' : '') + '</td>' +
                '<td class="p-3 text-xs font-semibold">' + item.product + '</td>' +
                '<td class="p-3 text-xs"><span class="bg-green-100 text-green-800 font-bold px-2 py-0.5 rounded">' + days + '</span></td>' +
                '<td class="p-3 text-xs">Est: ₹' + item.estimate + ' | <span class="font-bold text-green-700">Final: ₹' + item.final_amount + '</span> <button onclick="editField(\'' + item.id + '\', \'final_amount\')" class="text-blue-500 hover:underline text-[10px]">✏️ এডিট</button></td>' +
                '<td class="p-3">' +
                    '<select onchange="updateStatus(\'' + item.id + '\', this.value)" class="text-xs border rounded p-1 font-semibold outline-none bg-white shadow-sm">' +
                        '<option value="Pending" ' + (item.status === 'Pending' ? 'selected' : '') + '>কাজ চলছে/পেন্ডিং</option>' +
                        '<option value="Ready/Repaired" ' + (item.status === 'Ready/Repaired' ? 'selected' : '') + '>কাজ কমপ্লিট হয়েছে</option>' +
                        '<option value="Delivered" ' + (item.status === 'Delivered' ? 'selected' : '') + '>ডেলিভারি দেওয়া হয়েছে</option>' +
                    '</select>' +
                '</td>' +
                '<td class="p-3 text-center"><button onclick="openEditModal(\'' + item.id + '\')" class="text-amber-600 hover:text-amber-800 hover:underline text-xs font-bold mr-2">✏️ এডিট</button><button onclick="printReceipt(\'' + item.id + '\')" class="text-blue-600 hover:underline text-xs font-bold mr-2">🖨️ প্রিন্ট</button><button onclick="deleteRecord(\'' + item.id + '\')" class="text-red-500 hover:text-red-700 text-xs font-bold">🗑️ ডিলিট</button></td>' +
            '</tr>';
        }
    }
    tbody.innerHTML = html;
}

// Editable Fields (Vendor, Final Amount, Case ID)
function editField(id, fieldName) {
    var newValue = prompt("নতুন তথ্য প্রবেশ করান:");
    if (newValue !== null && newValue.trim() !== "") {
        var list = (currentTab === 'company') ? companyBookings : (currentTab === 'external') ? externalBookings : inhouseBookings;
        for (var i = 0; i < list.length; i++) {
            if (list[i].id === id) {
                list[i][fieldName] = newValue.trim();
                break;
            }
        }
        updateDashboardAndTable();
    }
}

// Dynamic Status Updater
function updateStatus(id, newStatus) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'স্ট্যাটাস পরিবর্তন নিশ্চিতকরণ',
            text: `আপনি কি স্ট্যাটাস পরিবর্তন করে "${newStatus}" করতে চান?`,
            icon: (newStatus === 'Canceled' || newStatus === 'canceled') ? 'error' : 'warning',
            showCancelButton: true,
            confirmButtonText: 'হ্যাঁ, নিশ্চিত করুন',
            cancelButtonText: 'বাতিল',
            confirmButtonColor: (newStatus === 'Canceled' || newStatus === 'canceled') ? '#ef4444' : '#2563eb',
            cancelButtonColor: '#64748b',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-2xl shadow-2xl border border-slate-100',
                confirmButton: 'px-5 py-2.5 rounded-xl font-bold text-sm',
                cancelButton: 'px-5 py-2.5 rounded-xl font-bold text-sm'
            }
        }).then(function (result) {
            if (result.isConfirmed) {
                var list = (currentTab === 'company') ? companyBookings : (currentTab === 'external') ? externalBookings : inhouseBookings;
                for (var i = 0; i < list.length; i++) {
                    if (list[i].id === id) {
                        list[i].status = newStatus;
                        break;
                    }
                }
                updateDashboardAndTable();
            } else {
                updateDashboardAndTable();
            }
        });
    } else {
        var list = (currentTab === 'company') ? companyBookings : (currentTab === 'external') ? externalBookings : inhouseBookings;
        for (var i = 0; i < list.length; i++) {
            if (list[i].id === id) {
                list[i].status = newStatus;
                break;
            }
        }
        updateDashboardAndTable();
    }
}

// Delete Record
function deleteRecord(id) {
    var tab = typeof currentTab !== 'undefined' ? currentTab : 'company';
    var deleteUrl = '';
    if (tab === 'company') deleteUrl = '/admin/service-booking/' + encodeURIComponent(id);
    else if (tab === 'external') deleteUrl = '/service/external/' + encodeURIComponent(id);
    else if (tab === 'inhouse') deleteUrl = '/service/inhouse/' + encodeURIComponent(id);

    if (!deleteUrl) return;

    var doDelete = function() {
        var csrfElement = document.querySelector('meta[name="csrf-token"]');
        var csrfToken = csrfElement ? csrfElement.getAttribute('content') : '';

        fetch(deleteUrl, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.success) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'success', title: 'ডিলিট সফল!', text: data.message || 'রেকর্ডটি ডিলিট করা হয়েছে।', timer: 1500, showConfirmButton: false });
                } else {
                    alert('✅ ' + (data.message || 'রেকর্ডটি ডিলিট করা হয়েছে।'));
                }
                if (typeof companyBookings !== 'undefined') companyBookings = companyBookings.filter(function(b) { return String(b.id) !== String(id); });
                if (typeof externalBookings !== 'undefined') externalBookings = externalBookings.filter(function(b) { return String(b.id) !== String(id); });
                if (typeof inhouseBookings !== 'undefined') inhouseBookings = inhouseBookings.filter(function(b) { return String(b.id) !== String(id); });
                updateDashboardAndTable();
            } else {
                alert('❌ ' + (data.message || 'ডিলিট করতে সমস্যা হয়েছে।'));
            }
        })
        .catch(function(err) {
            console.error('Delete Error:', err);
            alert('❌ ডিলিট করতে সমস্যা হয়েছে।');
        });
    };

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: '🗑️ আপনি কি নিশ্চিত?',
            text: 'এই সার্ভিস রেকর্ডটি স্থায়ীভাবে ডিলিট হয়ে যাবে!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'হ্যাঁ, ডিলিট করুন!',
            cancelButtonText: 'বাতিল',
            reverseButtons: true
        }).then(function(result) {
            if (result.isConfirmed) doDelete();
        });
    } else if (confirm('আপনি কি এই সার্ভিস রেকর্ডটি ডিলিট করতে চান?')) {
        doDelete();
    }
}

// Print Receipt Modal Logic
function printReceipt(id) {
    var item = null;
    var list = (currentTab === 'company') ? companyBookings : (currentTab === 'external') ? externalBookings : inhouseBookings;
    for (var i = 0; i < list.length; i++) {
        if (list[i].id === id) { item = list[i]; break; }
    }

    if (!item) return;

    document.getElementById('pr_id').innerText = item.id;
    document.getElementById('pr_date').innerText = item.created_at || new Date().toISOString().split('T')[0];
    document.getElementById('pr_name').innerText = item.name;
    document.getElementById('pr_phone').innerText = item.phone;
    document.getElementById('pr_address').innerText = item.address || 'N/A';
    document.getElementById('pr_product').innerText = item.product;
    document.getElementById('pr_serial').innerText = item.serial || 'N/A';
    document.getElementById('pr_amount').innerText = item.final_amount || item.final_cost || item.estimate || item.budget || '0';

    document.getElementById('printModal').classList.remove('hidden');
}

function closePrintModal() {
    document.getElementById('printModal').classList.add('hidden');
}

function triggerPrint() {
    var printContents = document.getElementById('printableReceipt').innerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = '<div style="width: 350px; margin: 0 auto; font-family: sans-serif;">' + printContents + '</div>';
    window.print();
    document.body.innerHTML = originalContents;
    window.location.reload();
}

// ================================
// OCR Scan Bill Functionality
// (single, consolidated implementation — Tesseract.js + pdf.js)
// ================================
async function scanBill(evt) {
    var fileInput = document.getElementById('invoiceFile');
    if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
        alert("অনুগ্রহ করে পারচেজ বিলের একটি ছবি বা PDF নির্বাচন করুন!");
        return;
    }

    var file = fileInput.files[0];
    var scanBtn = evt ? evt.target : null;
    var originalBtnText = scanBtn ? scanBtn.innerText : null;
    if (scanBtn) {
        scanBtn.innerText = "⏳ স্ক্যান করা হচ্ছে...";
        scanBtn.disabled = true;
    }

    try {
        var imageSource = file;

        // Handle PDF files by rendering the first page to a canvas
        if (file.type === "application/pdf") {
            var arrayBuffer = await file.arrayBuffer();
            var pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;
            var page = await pdf.getPage(1);
            var viewport = page.getViewport({ scale: 2.0 });

            var canvas = document.createElement('canvas');
            var context = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;

            await page.render({ canvasContext: context, viewport: viewport }).promise;
            imageSource = canvas;
        }

        // Run Tesseract OCR
        var result = await Tesseract.recognize(imageSource, 'eng');
        var text = result.data.text;

        if (text && text.trim().length > 0) {
            parseAndFillInvoiceData(text);
            alert("✅ বিল সফলভাবে স্ক্যান করা হয়েছে এবং ফর্ম ফিল-আপ হয়েছে!");
        } else {
            alert("⚠️ ফাইল থেকে কোনো টেক্সট পড়া সম্ভব হয়নি। পরিষ্কার ছবি ব্যবহার করুন।");
        }

    } catch (err) {
        console.error(err);
        alert("❌ স্ক্যান করার সময় সমস্যা হয়েছে।");
    } finally {
        if (scanBtn) {
            scanBtn.innerText = originalBtnText;
            scanBtn.disabled = false;
        }
    }
}

function parseAndFillInvoiceData(text) {
    // ১. ফোন নম্বর
    var phoneMatch = text.match(/(?:Mobile\s*No|WA\s*No|Reward\s*Mobile)\s*:\s*(\d{10})/i) || text.match(/\b[6-9]\d{9}\b/);
    if (phoneMatch && document.getElementById('comp_phone')) {
        document.getElementById('comp_phone').value = phoneMatch[1] || phoneMatch[0];
    }

    // ২. কাস্টমারের নাম
    var nameMatch = text.match(/Bill\s*To[\s\S]*?[•\-]\s*(?:IC\s+)?([A-Z\s]{3,30})/i);
    if (nameMatch && document.getElementById('comp_name')) {
        var cleanName = nameMatch[1].replace(/Address|Mobile|Pin|Reward/gi, '').trim();
        document.getElementById('comp_name').value = cleanName;
    }

    // ৩. কাস্টমারের অ্যাড্রেস
    var addressMatch = text.match(/Address\s*:\s*([^,\n]+(?:,[^,\n]+)*)/i);
    if (addressMatch && document.getElementById('comp_address')) {
        document.getElementById('comp_address').value = addressMatch[1].trim();
    }

    // ৪. পিন কোড
    var pinMatch = text.match(/Pin\s*-\s*(\d{6})/i) || text.match(/\b7\d{5}\b/);
    if (pinMatch && document.getElementById('comp_pincode')) {
        document.getElementById('comp_pincode').value = pinMatch[1] || pinMatch[0];
    }

    // ৫. বিল / সেল ডেট
    var dateMatch = text.match(/(?:Sale\s*Date|Date)\s*[:\-]\s*(\d{1,2}-[A-Za-z]{3}-\d{4})/i);
    if (dateMatch && document.getElementById('comp_bill_date')) {
        var d = new Date(dateMatch[1]);
        if (!isNaN(d.getTime())) {
            document.getElementById('comp_bill_date').value = d.toISOString().split('T')[0];
        }
    }

    // ৬. প্রোডাক্ট নাম ও মডেল
    var productMatch = text.match(/(HAIER|HITACHI|CANON|HP|LENV|EPSON)\s*AC/i) || text.match(/Product[\s\S]*?([A-Z0-9\s]+AC)/i);
    var modelMatch = text.match(/Model:\s*([A-Z0-9\.\(\)]+)/i);
    if (document.getElementById('comp_product')) {
        var prodStr = "";
        if (productMatch) prodStr += productMatch[1].trim();
        if (modelMatch) prodStr += " (" + modelMatch[1].trim() + ")";
        if (prodStr) document.getElementById('comp_product').value = prodStr;
    }

    // ৭. সিরিয়াল নম্বর (একাধিক হতে পারে)
    var serialMatches = [].concat(
        [...text.matchAll(/Sl\s*no\s*[:\-]\s*([A-Za-z0-9]+)/gi)],
        [...text.matchAll(/S\/N\s*:\s*([A-Za-z0-9]+)/gi)]
    );
    if (serialMatches.length > 0 && document.getElementById('comp_serial')) {
        var serials = serialMatches.map(function(m) { return m[1]; }).join(', ');
        document.getElementById('comp_serial').value = serials;
    }
}

// Default Selection On Load
window.onload = function() {
    selectService('company');
};
