
// ============================================================
// common.js
// Shared State
// Customer Search
// Tab Switching
// Search
// Filter
// Company / External / In-House Dispatcher
// ============================================================


// ============================================================
// CUSTOMER DATABASE
// ============================================================

var customerDatabase = {

    "8597753337": {
        name: "MS APARNA DEY",
        address: "BETHUADAHARI NADIA",
        pincode: "741126"
    },

    "9800000000": {
        name: "RITESH ROY",
        address: "KRISHNANAGAR NADIA",
        pincode: "741101"
    }

};


// ============================================================
// GLOBAL STATE
// ============================================================

var currentTab = 'company';

var currentFilter = 'all';

var searchQuery = '';


// ============================================================
// CALCULATE DAYS
// ============================================================

function calculateDays(startDate) {

    if (!startDate) {
        return '0 দিন';
    }

    var start = new Date(startDate);

    var today = new Date();

    var diffTime = Math.abs(today - start);

    var diffDays =
        Math.ceil(
            diffTime /
            (1000 * 60 * 60 * 24)
        ) - 1;

    if (diffDays < 0) {
        diffDays = 0;
    }

    return diffDays + ' দিন';
}


// ============================================================
// SEARCH FILTER
// Company / External-এর জন্য
// ============================================================

function applySearchFilter(list) {

    if (
        !searchQuery ||
        searchQuery === ''
    ) {
        return list;
    }

    return list.filter(function (item) {

        var name =
            String(item.name || '')
                .toLowerCase();

        var phone =
            String(item.phone || '')
                .toLowerCase();

        var product =
            String(item.product || '')
                .toLowerCase();

        return (
            name.includes(searchQuery) ||
            phone.includes(searchQuery) ||
            product.includes(searchQuery)
        );
    });
}


// ============================================================
// SELECT SERVICE / TAB
// ============================================================

function selectService(type) {

    currentTab = type;

    currentFilter = 'all';

    searchQuery = '';


    var area =
        document.getElementById(
            'formArea'
        );

    var tableArea =
        document.getElementById(
            'tableSectionArea'
        );


    if (!area) {

        console.error(
            'formArea পাওয়া যায়নি!'
        );

        return;
    }


    if (tableArea) {

        tableArea.classList.remove(
            'hidden'
        );
    }


    // ========================================================
    // RESET TAB STYLE
    // ========================================================

    document
        .querySelectorAll(
            '.service-tab-btn'
        )
        .forEach(function (btn) {

            btn.classList.remove(
                'ring-4',
                'ring-offset-2'
            );

        });


    // ========================================================
    // COMPANY
    // ========================================================

    if (type === 'company') {

        var companyBtn =
            document.getElementById(
                'btn-company'
            );

        if (companyBtn) {

            companyBtn.classList.add(
                'ring-4',
                'ring-blue-300'
            );
        }


        var companyTemplate =
            document.getElementById(
                'companyFormTemplate'
            );

        if (!companyTemplate) {
            return;
        }

        area.innerHTML =
            companyTemplate.innerHTML;


        if (
            typeof initCompanyForm ===
            'function'
        ) {

            initCompanyForm();
        }


        if (
            typeof updateCompanyDashboard ===
            'function'
        ) {

            updateCompanyDashboard();
        }


        renderTable();

        return;
    }


    // ========================================================
    // EXTERNAL
    // ========================================================

    if (type === 'external') {

        var externalBtn =
            document.getElementById(
                'btn-external'
            );

        if (externalBtn) {

            externalBtn.classList.add(
                'ring-4',
                'ring-orange-300'
            );
        }


        var externalTemplate =
            document.getElementById(
                'externalFormTemplate'
            );

        if (externalTemplate) {

            area.innerHTML =
                externalTemplate.innerHTML;
        }


        if (typeof initExternalForm === 'function') {
            initExternalForm();
        }


        // Clear search
        var searchInput =
            document.getElementById(
                'globalSearchInput'
            );

        if (searchInput) {
            searchInput.value = '';
        }


        if (typeof loadExternalCounts === 'function') {
            loadExternalCounts();
        }


        if (typeof loadExternalServices === 'function') {
            loadExternalServices('all', '');
        }


        return;
    }


    // ========================================================
    // IN-HOUSE
    // ========================================================

    if (type === 'inhouse') {

        var inhouseBtn =
            document.getElementById(
                'btn-inhouse'
            );

        if (inhouseBtn) {

            inhouseBtn.classList.add(
                'ring-4',
                'ring-green-300'
            );
        }


        var inhouseTemplate =
            document.getElementById(
                'inhouseFormTemplate'
            );

        if (inhouseTemplate) {

            area.innerHTML =
                inhouseTemplate.innerHTML;
        }

        if (typeof initInhouseForm === 'function') {
            initInhouseForm();
        }


        // Clear search
        var searchInput =
            document.getElementById(
                'globalSearchInput'
            );

        if (searchInput) {

            searchInput.value = '';
        }


        // ====================================================
        // LOAD DATABASE COUNT
        // ====================================================

        if (
            typeof loadInhouseCounts ===
            'function'
        ) {

            loadInhouseCounts();
        }


        // ====================================================
        // LOAD DATABASE DATA
        // ====================================================

        if (
            typeof loadInhouseServices ===
            'function'
        ) {

            loadInhouseServices(
                'all',
                ''
            );
        }


        return;
    }

}


// ============================================================
// CUSTOMER AUTO FILL
// ============================================================

function searchCustomer(
    phone,
    prefix
) {

    var cleanPhone =
        String(phone || '').trim();

    if (prefix === 'inh') {
        if (!/^\d{10}$/.test(cleanPhone)) {
            return;
        }

        fetch('/get-customer-by-phone/' + encodeURIComponent(cleanPhone), {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (result) {
                if (!result.success || !result.data) {
                    return;
                }

                var customer = result.data;
                var name = document.getElementById('inh_name');
                var address = document.getElementById('inh_address');
                var pincode = document.getElementById('inh_pincode');

                if (name && customer.name) {
                    name.value = customer.name;
                }

                if (address && customer.address) {
                    address.value = customer.address;
                }

                if (pincode && customer.pin) {
                    pincode.value = customer.pin;
                }
            })
            .catch(function (error) {
                console.error('In-House customer lookup error:', error);
            });

        return;
    }

    if (prefix === 'ext') {
        if (!/^\d{10}$/.test(cleanPhone)) {
            return;
        }

        fetch('/get-customer-by-phone/' + encodeURIComponent(cleanPhone), {
            method: 'GET',
            headers: { 'Accept': 'application/json' }
        })
            .then(function (response) { return response.json(); })
            .then(function (result) {
                if (!result.success || !result.data) return;
                var customer = result.data;
                var name = document.getElementById('ext_name');
                var address = document.getElementById('ext_address');
                var pincode = document.getElementById('ext_pincode');
                if (name && customer.name) name.value = customer.name;
                if (address && customer.address) address.value = customer.address;
                if (pincode && customer.pin) pincode.value = customer.pin;
            })
            .catch(function (error) {
                console.error('External customer lookup error:', error);
            });

        return;
    }


    if (
        customerDatabase[cleanPhone]
    ) {

        var customer =
            customerDatabase[
            cleanPhone
            ];


        var name =
            document.getElementById(
                prefix + '_name'
            );

        var address =
            document.getElementById(
                prefix + '_address'
            );

        var pincode =
            document.getElementById(
                prefix + '_pincode'
            );


        if (name) {
            name.value =
                customer.name;
        }

        if (address) {
            address.value =
                customer.address;
        }

        if (pincode) {
            pincode.value =
                customer.pincode;
        }

    }

}


// ============================================================
// SAVE CUSTOMER
// ============================================================

function saveCustomerToDatabase(
    phone,
    name,
    address,
    pincode
) {

    if (
        phone &&
        !customerDatabase[phone]
    ) {

        customerDatabase[phone] = {

            name: name,

            address: address,

            pincode: pincode

        };

    }

}


// ============================================================
// FILTER DATA
// ============================================================

function filterData(statusKey) {

    currentFilter =
        statusKey;


    var badge =
        document.getElementById(
            'activeFilterBadge'
        );


    if (badge) {

        var labels = {

            all: 'সব দেখুন',

            pending:
                'কাজ চলছে / Pending',

            ready:
                'কাজ কমপ্লিট / Delivery বাকি',

            delivered:
                'ডেলিভারি দেওয়া হয়েছে'

        };


        badge.innerText =
            'ফিল্টার: ' +
            (
                labels[statusKey]
                || statusKey
            );
    }


    // ========================================================
    // IN-HOUSE DATABASE FILTER
    // ========================================================

    if (
        currentTab === 'inhouse'
    ) {

        if (
            typeof loadInhouseServices ===
            'function'
        ) {

            loadInhouseServices(
                statusKey,
                searchQuery
            );
        }

        return;
    }


    // ========================================================
    // EXTERNAL DATABASE FILTER
    // ========================================================

    if (currentTab === 'external') {

        if (typeof loadExternalServices === 'function') {

            loadExternalServices(
                statusKey,
                searchQuery
            );
        }

        return;
    }


    // ========================================================
    // COMPANY
    // ========================================================

    renderTable();

}


// ============================================================
// GLOBAL SEARCH
// ============================================================

function handleSearch(value) {

    searchQuery =
        String(value || '')
            .trim()
            .toLowerCase();


    // ========================================================
    // IN-HOUSE DATABASE SEARCH
    // ========================================================

    if (
        currentTab === 'inhouse'
    ) {

        if (
            typeof loadInhouseServices ===
            'function'
        ) {

            loadInhouseServices(
                currentFilter,
                searchQuery
            );
        }

        return;
    }


    // ========================================================
    // EXTERNAL DATABASE SEARCH
    // ========================================================

    if (currentTab === 'external') {

        if (typeof loadExternalServices === 'function') {
            loadExternalServices(currentFilter, searchQuery);
        }

        return;
    }


    // ========================================================
    // COMPANY
    // ========================================================

    renderTable();

}


// ============================================================
// EDIT FIELD
// Company / External only
// ============================================================

function editField(id, fieldName) {
    if (currentTab === 'inhouse') {
        var currentItem = typeof inhouseBookings !== 'undefined' ? inhouseBookings.find(function(b) { return String(b.id) === String(id); }) : null;
        var currentVal = currentItem && currentItem[fieldName] ? currentItem[fieldName] : '';
        var newValue = prompt('নতুন তথ্য প্রবেশ করান:', currentVal);
        if (newValue !== null && newValue.trim() !== '') {
            var csrfElement = document.querySelector('meta[name="csrf-token"]');
            var csrfToken = csrfElement ? csrfElement.getAttribute('content') : '';
            fetch('/service/inhouse/' + id + '/field', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ field: fieldName, value: newValue.trim() })
            })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (res.success) {
                    if (typeof loadInhouseCounts === 'function') loadInhouseCounts();
                    if (typeof loadInhouseServices === 'function') loadInhouseServices(typeof currentFilter !== 'undefined' ? currentFilter : 'all', typeof searchQuery !== 'undefined' ? searchQuery : '');
                } else {
                    alert('❌ আপডেট করা সম্ভব হয়নি!');
                }
            })
            .catch(function(err) { console.error(err); alert('❌ সার্ভার সমস্যা।'); });
        }
        return;
    }

    if (currentTab === 'external') {
        if (typeof extEditField === 'function') {
            extEditField(id, fieldName);
        }
        return;
    }

    var list = companyBookings;
    var currentItem = list.find(function(b) { return String(b.id) === String(id); });
    var currentVal = currentItem && currentItem[fieldName] ? currentItem[fieldName] : '';

    if ((fieldName === 'case_id_1' || fieldName === 'case_id_2') && typeof Swal !== 'undefined') {
        Swal.fire({
            title: '🆔 কেস আইডি ও রিমার্কস যোগ করুন',
            html:
                '<div class="text-left space-y-3 mt-2">' +
                    '<div>' +
                        '<label class="block text-xs font-bold text-gray-700 mb-1">কেস আইডি নম্বর <span class="text-red-500">*</span></label>' +
                        '<input id="swal_case_id" type="text" class="swal2-input !w-full !m-0" placeholder="উদাহরণ: CASE-10234" value="' + currentVal + '">' +
                    '</div>' +
                    '<div>' +
                        '<label class="block text-xs font-bold text-gray-700 mb-1">রিমার্কস (Remarks, অপশনাল)</label>' +
                        '<textarea id="swal_case_remarks" rows="2" class="w-full border rounded-xl p-2.5 text-sm outline-none focus:ring-2 focus:ring-blue-400 border-gray-300" placeholder="অতিরিক্ত মন্তব্য লিখুন...">' + (currentItem && currentItem.remarks ? currentItem.remarks : '') + '</textarea>' +
                    '</div>' +
                '</div>',
            showCancelButton: true,
            confirmButtonText: '💾 সেভ করুন',
            cancelButtonText: 'বাতিল',
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#64748b',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-2xl shadow-2xl border border-slate-100 p-6',
                confirmButton: 'px-5 py-2.5 rounded-xl font-bold text-sm',
                cancelButton: 'px-5 py-2.5 rounded-xl font-bold text-sm'
            },
            preConfirm: function() {
                var caseIdVal = document.getElementById('swal_case_id').value.trim();
                var remarksVal = document.getElementById('swal_case_remarks').value.trim();
                if (!caseIdVal) {
                    Swal.showValidationMessage('অনুগ্রহ করে কেস আইডি নম্বর লিখুন!');
                    return false;
                }
                return { caseId: caseIdVal, remarks: remarksVal };
            }
        }).then(function(result) {
            if (result.isConfirmed && result.value) {
                for (var i = 0; i < list.length; i++) {
                    if (String(list[i].id) === String(id)) {
                        list[i][fieldName] = result.value.caseId;
                        list[i].case_id_date = new Date().toISOString().split('T')[0];
                        if (result.value.remarks) {
                            list[i].remarks = result.value.remarks;
                        }
                        break;
                    }
                }
                if (typeof saveAllData === 'function') saveAllData();
                if (typeof updateDashboardAndTable === 'function') updateDashboardAndTable();
                else if (typeof renderTable === 'function') renderTable();

                Swal.fire({
                    icon: 'success',
                    title: 'সেভ সফল!',
                    text: 'কেস আইডি ও রিমার্কস সফলভাবে সেভ করা হয়েছে।',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
        return;
    }

    var fieldTitle = '✏️ নতুন তথ্য প্রবেশ করান';
    var placeholderText = 'নতুন তথ্য লিখুন...';

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: fieldTitle,
            input: 'text',
            inputValue: currentVal,
            inputPlaceholder: placeholderText,
            showCancelButton: true,
            confirmButtonText: '💾 সেভ করুন',
            cancelButtonText: 'বাতিল',
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#64748b',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-2xl shadow-2xl border border-slate-100',
                confirmButton: 'px-5 py-2.5 rounded-xl font-bold text-sm',
                cancelButton: 'px-5 py-2.5 rounded-xl font-bold text-sm'
            },
            inputValidator: function(value) {
                if (!value || !value.trim()) {
                    return 'অনুগ্রহ করে তথ্য প্রদান করুন!';
                }
            }
        }).then(function(result) {
            if (result.isConfirmed && result.value) {
                var newValue = result.value.trim();
                for (var i = 0; i < list.length; i++) {
                    if (String(list[i].id) === String(id)) {
                        list[i][fieldName] = newValue;
                        break;
                    }
                }
                updateDashboardAndTable();
                Swal.fire({
                    icon: 'success',
                    title: 'সেভ সফল!',
                    text: 'তথ্য সফলভাবে সেভ করা হয়েছে।',
                    timer: 1200,
                    showConfirmButton: false
                });
            }
        });
    } else {
        var newValue = prompt(fieldTitle, currentVal);
        if (newValue !== null && newValue.trim() !== '') {
            for (var i = 0; i < list.length; i++) {
                if (String(list[i].id) === String(id)) {
                    list[i][fieldName] = newValue.trim();
                    break;
                }
            }
            updateDashboardAndTable();
        }
    }
}


// ============================================================
// UPDATE STATUS
// Company / External / Inhouse
// ============================================================

function executeCompanyStatusUpdate(id, newStatus) {
    fetch('/service-booking/' + encodeURIComponent(id) + '/status', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(function (response) {
        return response.json().then(function (data) {
            if (!response.ok || !data.success) {
                throw new Error(data.message || 'স্ট্যাটাস আপডেট করা যায়নি');
            }
            return data;
        });
    })
    .then(function (data) {
        var booking = companyBookings.find(function (item) { return String(item.id) === String(id); });
        if (booking) {
            booking.status = data.status;
        }
        updateDashboardAndTable();
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'স্ট্যাটাস আপডেট সফল!',
                text: 'স্ট্যাটাস সফলভাবে আপডেট করা হয়েছে।',
                timer: 1500,
                showConfirmButton: false
            });
        }
    })
    .catch(function (error) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'আপডেট ত্রুটি',
                text: error.message
            });
        } else {
            alert(error.message);
        }
        renderTable();
    });
}

function updateStatus(id, newStatus) {
    if (currentTab === 'inhouse') {
        var csrfElement = document.querySelector('meta[name="csrf-token"]');
        var csrfToken = csrfElement ? csrfElement.getAttribute('content') : '';

        fetch('/service/inhouse/' + id + '/status', {
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
                if (typeof loadInhouseCounts === 'function') loadInhouseCounts();
                if (typeof loadInhouseServices === 'function') loadInhouseServices(typeof currentFilter !== 'undefined' ? currentFilter : 'all', typeof searchQuery !== 'undefined' ? searchQuery : '');
            } else {
                alert('❌ স্ট্যাটাস আপডেট সম্ভব হয়নি: ' + (res.message || ''));
            }
        })
        .catch(function (err) {
            console.error('Update inhouse status error:', err);
            alert('❌ সার্ভার ত্রুটি হয়েছে।');
        });
        return;
    }

    if (currentTab === 'external') {
        if (typeof extUpdateStatus === 'function') {
            extUpdateStatus(id, newStatus);
        }
        return;
    }

    if (currentTab === 'company') {
        var booking = companyBookings.find(function (item) {
            return String(item.id) === String(id);
        });

        if (!booking) {
            return;
        }

        if (booking.status === 'Completed') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'পরিবর্তন অসম্ভব',
                    text: 'কমপ্লিট হওয়া বুকিং আর পরিবর্তন করা যাবে না।'
                });
            } else {
                alert('কমপ্লিট হওয়া বুকিং আর পরিবর্তন করা যাবে না।');
            }
            renderTable();
            return;
        }

        if (newStatus === 'Pending' && booking.status !== 'Pending') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'পরিবর্তন অসম্ভব',
                    text: 'আগের স্ট্যাটাসে Pending ফিরিয়ে নেওয়া যাবে না।'
                });
            } else {
                alert('আগের স্ট্যাটাসে Pending ফিরিয়ে নেওয়া যাবে না।');
            }
            renderTable();
            return;
        }

        // SweetAlert কনফার্মেশন প্রম্পট (সকল স্ট্যাটাস পরিবর্তনের জন্য)
        if (typeof Swal !== 'undefined') {
            let titleText = 'স্ট্যাটাস পরিবর্তন নিশ্চিতকরণ';
            let textMessage = `আপনি কি স্ট্যাটাস পরিবর্তন করে "${newStatus}" করতে চান?`;
            let iconType = 'warning';
            let confirmBtnColor = '#2563eb';

            if (newStatus === 'Canceled' || newStatus === 'canceled') {
                titleText = '❌ সার্ভিস ক্যানসেল নিশ্চিতকরণ';
                textMessage = 'আপনি কি নিশ্চিত যে এই সার্ভিসটি "Canceled" (বাতিল) করতে চান?';
                iconType = 'error';
                confirmBtnColor = '#ef4444';
            } else if (newStatus === 'Engineer Visited') {
                titleText = '👨‍🔧 ইঞ্জিনিয়ার ভিজিট নিশ্চিতকরণ';
                textMessage = 'আপনি কি স্ট্যাটাস পরিবর্তন করে "Engineer Visited" হিসেবে সেট করতে চান?';
                iconType = 'info';
                confirmBtnColor = '#2563eb';
            } else if (newStatus === 'Pending' || newStatus === 'pending') {
                titleText = '⏳ পেন্ডিং স্ট্যাটাস নিশ্চিতকরণ';
                textMessage = 'আপনি কি স্ট্যাটাস পরিবর্তন করে "Pending" করতে চান?';
                iconType = 'warning';
                confirmBtnColor = '#ea580c';
            } else if (newStatus === 'Completed' || newStatus === 'ready' || newStatus === 'Delivered' || newStatus === 'delivered') {
                titleText = '✅ স্ট্যাটাস পরিবর্তন নিশ্চিতকরণ';
                textMessage = `আপনি কি স্ট্যাটাস পরিবর্তন করে "${newStatus}" করতে চান?`;
                iconType = 'success';
                confirmBtnColor = '#16a34a';
            }

            Swal.fire({
                title: titleText,
                text: textMessage,
                icon: iconType,
                showCancelButton: true,
                confirmButtonText: 'হ্যাঁ, নিশ্চিত করুন',
                cancelButtonText: 'বাতিল',
                confirmButtonColor: confirmBtnColor,
                cancelButtonColor: '#64748b',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-2xl shadow-2xl border border-slate-100',
                    confirmButton: 'px-5 py-2.5 rounded-xl font-bold text-sm',
                    cancelButton: 'px-5 py-2.5 rounded-xl font-bold text-sm'
                }
            }).then(function (result) {
                if (result.isConfirmed) {
                    executeCompanyStatusUpdate(id, newStatus);
                } else {
                    renderTable();
                }
            });
        } else {
            executeCompanyStatusUpdate(id, newStatus);
        }

        return;
    }
}


// ============================================================
// DELETE RECORD
// Company / External only
// ============================================================

function deleteRecord(id) {
    var tab = typeof currentTab !== 'undefined' ? currentTab : 'company';
    var deleteUrl = '';

    if (tab === 'company') {
        deleteUrl = '/admin/service-booking/' + encodeURIComponent(id);
    } else if (tab === 'external') {
        deleteUrl = '/service/external/' + encodeURIComponent(id);
    } else if (tab === 'inhouse') {
        deleteUrl = '/service/inhouse/' + encodeURIComponent(id);
    }

    if (!deleteUrl) {
        if (typeof showToast === 'function') showToast('❌ অজানা ক্যাটাগরি!', 'error');
        return;
    }

    var doDelete = function() {
        var csrfElement = document.querySelector('meta[name="csrf-token"]');
        var csrfToken = csrfElement ? csrfElement.getAttribute('content') : '';

        fetch(deleteUrl, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            }
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (data.success) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'ডিলিট সফল!',
                        text: data.message || 'রেকর্ডটি স্থায়ীভাবে ডিলিট করা হয়েছে।',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else if (typeof showToast === 'function') {
                    showToast('✅ ' + (data.message || 'রেকর্ড ডিলিট করা হয়েছে!'), 'success');
                } else {
                    alert('✅ ' + (data.message || 'রেকর্ড ডিলিট করা হয়েছে!'));
                }

                if (typeof companyBookings !== 'undefined') {
                    companyBookings = companyBookings.filter(function(b) { return String(b.id) !== String(id); });
                }
                if (typeof externalBookings !== 'undefined') {
                    externalBookings = externalBookings.filter(function(b) { return String(b.id) !== String(id); });
                }
                if (typeof inhouseBookings !== 'undefined') {
                    inhouseBookings = inhouseBookings.filter(function(b) { return String(b.id) !== String(id); });
                }

                var row = document.querySelector('tr[data-booking-id="' + id + '"]');
                if (row) row.remove();

                if (typeof updateDashboardAndTable === 'function') {
                    updateDashboardAndTable();
                } else if (typeof renderTable === 'function') {
                    renderTable();
                } else if (typeof loadCompanyBookings === 'function') {
                    loadCompanyBookings();
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire('❌ ত্রুটি!', data.message || 'ডিলিট করতে সমস্যা হয়েছে।', 'error');
                } else {
                    alert('❌ ' + (data.message || 'ডিলিট করতে সমস্যা হয়েছে।'));
                }
            }
        })
        .catch(function(err) {
            console.error('Delete Error:', err);
            if (typeof Swal !== 'undefined') {
                Swal.fire('❌ ত্রুটি!', 'সার্ভারে কানেক্ট করতে ব্যর্থ হয়েছে।', 'error');
            } else {
                alert('❌ সার্ভারে কানেক্ট করতে ব্যর্থ হয়েছে।');
            }
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
            if (result.isConfirmed) {
                doDelete();
            }
        });
    } else if (typeof showConfirm === 'function') {
        showConfirm('ডিলিট কনফার্মেশন', 'আপনি কি নিশ্চিত এই সার্ভিস রেকর্ডটি স্থায়ীভাবে ডিলিট করতে চান?', doDelete);
    } else {
        if (confirm('আপনি কি নিশ্চিত এই সার্ভিস রেকর্ডটি স্থায়ীভাবে ডিলিট করতে চান?')) {
            doDelete();
        }
    }
}


// ============================================================
// DASHBOARD DISPATCHER
// ============================================================

function updateDashboardAndTable() {


    // ========================================================
    // COMPANY
    // ========================================================

    if (
        currentTab === 'company'
    ) {

        if (
            typeof updateCompanyDashboard ===
            'function'
        ) {

            updateCompanyDashboard();
        }

        renderTable();

        return;
    }


    // ========================================================
    // EXTERNAL
    // ========================================================

    if (
        currentTab === 'external'
    ) {

        if (
            typeof updateExternalDashboard ===
            'function'
        ) {

            updateExternalDashboard();
        }

        renderTable();

        return;
    }


    // ========================================================
    // IN-HOUSE
    // ========================================================

    if (
        currentTab === 'inhouse'
    ) {

        if (
            typeof loadInhouseCounts ===
            'function'
        ) {

            loadInhouseCounts();
        }


        if (
            typeof loadInhouseServices ===
            'function'
        ) {

            loadInhouseServices(
                currentFilter,
                searchQuery
            );
        }

        return;
    }

}


// ============================================================
// TABLE DISPATCHER
// ============================================================

function renderTable() {

    var thead =
        document.getElementById(
            'mainTableHead'
        );

    var tbody =
        document.getElementById(
            'mainTableBody'
        );


    if (
        !thead ||
        !tbody
    ) {

        return;
    }


    // ========================================================
    // COMPANY
    // ========================================================

    if (
        currentTab === 'company'
    ) {

        if (
            typeof renderCompanyTable ===
            'function'
        ) {

            renderCompanyTable(
                thead,
                tbody
            );
        }

        return;
    }


    // ========================================================
    // EXTERNAL
    // ========================================================

    if (
        currentTab === 'external'
    ) {

        if (
            typeof renderExternalTable ===
            'function'
        ) {

            renderExternalTable(
                thead,
                tbody
            );
        }

        return;
    }


    // ========================================================
    // IN-HOUSE
    // ========================================================
    // In-House table সরাসরি
    // loadInhouseServices() থেকে render হবে

    if (
        currentTab === 'inhouse'
    ) {

        return;
    }

}


// ============================================================
// DEFAULT TAB
// ============================================================

function enableCompanyOcrCustomerLookup() {

    if (
        window.companyOcrLookupEnabled ||
        typeof window.parseAndFillInvoiceData !== 'function'
    ) {
        return;
    }

    var parseAndFillInvoiceData =
        window.parseAndFillInvoiceData;

    window.parseAndFillInvoiceData = function (text) {
        parseAndFillInvoiceData(text);

        if (currentTab !== 'company' || typeof fetchCustomer !== 'function') {
            return;
        }

        var phoneInput = document.getElementById('comp_phone');
        var phone = phoneInput ? phoneInput.value.replace(/\D/g, '') : '';

        if (phone.length === 10) {
            fetchCustomer(phone);
        }
    };

    window.companyOcrLookupEnabled = true;
}

enableCompanyOcrCustomerLookup();

window.addEventListener(
    'load',
    function () {

        selectService(
            'company'
        );

    }
);

// ============================================================
// EDIT RECORD MODAL HANDLERS
// ============================================================
function openEditModal(id) {
    var item = null;
    var type = (typeof currentTab !== 'undefined') ? currentTab : 'company';

    if (type === 'company' && typeof companyBookings !== 'undefined') {
        item = companyBookings.find(function(b) { return String(b.id) === String(id); });
    } else if (type === 'external' && typeof externalBookings !== 'undefined') {
        item = externalBookings.find(function(b) { return String(b.id) === String(id); });
    } else if (type === 'inhouse' && typeof inhouseBookings !== 'undefined') {
        item = inhouseBookings.find(function(b) { return String(b.id) === String(id); });
    }

    if (!item) {
        if (typeof companyBookings !== 'undefined') item = companyBookings.find(function(b) { return String(b.id) === String(id); });
        if (!item && typeof externalBookings !== 'undefined') { item = externalBookings.find(function(b) { return String(b.id) === String(id); }); type = 'external'; }
        if (!item && typeof inhouseBookings !== 'undefined') { item = inhouseBookings.find(function(b) { return String(b.id) === String(id); }); type = 'inhouse'; }
    }

    if (!item) {
        if (typeof showToast !== 'undefined') showToast('রেকর্ড পাওয়া যায়নি!', 'error');
        else alert('রেকর্ড পাওয়া যায়নি!');
        return;
    }

    // কমপ্লিট বা ডেলিভারি হওয়া সার্ভিস এডিট ব্লককরণ
    var statusLower = String(item.status || '').toLowerCase();
    if (statusLower === 'completed' || statusLower === 'delivered') {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: '🔒 এডিট করা সম্ভব নয়',
                text: 'কমপ্লিট অথবা ডেলিভারি হওয়া সার্ভিস রেকর্ড আর এডিট/পরিবর্তন করা যাবে না।',
                confirmButtonColor: '#2563eb',
                confirmButtonText: 'ঠিক আছে'
            });
        } else if (typeof showToast !== 'undefined') {
            showToast('কমপ্লিট অথবা ডেলিভারি হওয়া সার্ভিস রেকর্ড আর এডিট করা যাবে না।', 'error');
        } else {
            alert('কমপ্লিট অথবা ডেলিভারি হওয়া সার্ভিস রেকর্ড আর এডিট করা যাবে না।');
        }
        return;
    }

    var recordIdInput = document.getElementById('edit_record_id');
    var recordTypeInput = document.getElementById('edit_record_type');
    var modalRecordIdLabel = document.getElementById('editModalRecordId');

    if (recordIdInput) recordIdInput.value = item.id;
    if (recordTypeInput) recordTypeInput.value = type;
    if (modalRecordIdLabel) modalRecordIdLabel.innerText = '#' + item.id;

    if (document.getElementById('edit_phone')) document.getElementById('edit_phone').value = item.phone || '';
    if (document.getElementById('edit_name')) document.getElementById('edit_name').value = item.name || '';
    if (document.getElementById('edit_product')) document.getElementById('edit_product').value = item.product || '';
    if (document.getElementById('edit_serial')) document.getElementById('edit_serial').value = item.serial || item.serial_no || '';
    if (document.getElementById('edit_remarks')) document.getElementById('edit_remarks').value = item.remarks || '';

    var statusSelect = document.getElementById('edit_status');
    if (statusSelect) {
        if (type === 'external') {
            statusSelect.innerHTML = 
                '<option value="pending">পেন্ডিং (Pending)</option>' +
                '<option value="sent">সার্ভিস সেন্টারে পাঠানো হয়েছে</option>' +
                '<option value="back">সার্ভিস সেন্টার থেকে এসেছে</option>' +
                '<option value="delivered">ডেলিভারি সম্পন্ন (Delivered)</option>';
        } else if (type === 'inhouse') {
            statusSelect.innerHTML = 
                '<option value="Pending">কাজ চলছে/পেন্ডিং</option>' +
                '<option value="Ready/Repaired">কাজ কমপ্লিট / রেডি</option>' +
                '<option value="Delivered">ডেলিভারি সম্পন্ন (Delivered)</option>' +
                '<option value="Canceled">ক্যান্সেল (Canceled)</option>';
        } else {
            statusSelect.innerHTML = 
                '<option value="Pending">পেন্ডিং (Pending)</option>' +
                '<option value="Engineer Visited">ইঞ্জিনিয়ার ভিজিটেড</option>' +
                '<option value="Completed">কমপ্লিট (Completed)</option>' +
                '<option value="Canceled">ক্যান্সেল (Canceled)</option>';
        }
        statusSelect.value = item.status || (type === 'external' ? 'pending' : 'Pending');
    }

    var vendorContainer = document.getElementById('edit_vendor_container');
    var addressContainer = document.getElementById('edit_address_container');
    var pincodeContainer = document.getElementById('edit_pincode_container');
    var costContainer = document.getElementById('edit_cost_container');
    var costLabel = document.getElementById('edit_cost_label');
    var dateLabel = document.getElementById('edit_date_label');
    var dateInput = document.getElementById('edit_date');

    if (type === 'external') {
        if (vendorContainer) vendorContainer.classList.remove('hidden');
        if (addressContainer) addressContainer.classList.add('hidden');
        if (pincodeContainer) pincodeContainer.classList.add('hidden');
        if (document.getElementById('edit_vendor')) document.getElementById('edit_vendor').value = item.vendor || '';
        if (costContainer) costContainer.classList.remove('hidden');
        if (costLabel) costLabel.innerText = '💰 ফাইনাল কস্ট (₹)';
        if (document.getElementById('edit_cost')) document.getElementById('edit_cost').value = item.final_cost || item.cost || '';
        if (dateLabel) dateLabel.innerText = '📅 প্রাপ্তির তারিখ (Receive Date)';
        if (dateInput) dateInput.value = item.receive_date || item.created_at || '';
    } else if (type === 'inhouse') {
        if (vendorContainer) vendorContainer.classList.add('hidden');
        if (addressContainer) addressContainer.classList.remove('hidden');
        if (pincodeContainer) pincodeContainer.classList.remove('hidden');
        if (document.getElementById('edit_address')) document.getElementById('edit_address').value = item.address || '';
        if (document.getElementById('edit_pincode')) document.getElementById('edit_pincode').value = item.pincode || '';
        if (costContainer) costContainer.classList.remove('hidden');
        if (costLabel) costLabel.innerText = '💰 ফাইনাল অ্যামাউন্ট (₹)';
        if (document.getElementById('edit_cost')) document.getElementById('edit_cost').value = item.final_amount || item.estimate || '';
        if (dateLabel) dateLabel.innerText = '📅 প্রাপ্তির তারিখ (Receive Date)';
        if (dateInput) dateInput.value = item.receive_date || item.created_at || '';
    } else { // company
        if (vendorContainer) vendorContainer.classList.add('hidden');
        if (addressContainer) addressContainer.classList.remove('hidden');
        if (pincodeContainer) pincodeContainer.classList.remove('hidden');
        if (document.getElementById('edit_address')) document.getElementById('edit_address').value = item.address || '';
        if (document.getElementById('edit_pincode')) document.getElementById('edit_pincode').value = item.pincode || '';
        if (costContainer) costContainer.classList.add('hidden');
        if (dateLabel) dateLabel.innerText = '📅 বুকিং তারিখ (Booking Date)';
        if (dateInput) dateInput.value = item.booking_date || item.created_at || '';
    }

    var modal = document.getElementById('editRecordModal');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
}

function closeEditModal() {
    var modal = document.getElementById('editRecordModal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
}

function saveEditedRecord(event) {
    if (event) event.preventDefault();

    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: '✏️ তথ্য আপডেট নিশ্চিতকরণ',
            text: 'আপনি কি এই রেকর্ডটির পরিবর্তনগুলো সংরক্ষণ করতে চান?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'হ্যাঁ, নিশ্চিত করুন',
            cancelButtonText: 'বাতিল',
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#64748b',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-2xl shadow-2xl border border-slate-100',
                confirmButton: 'px-5 py-2.5 rounded-xl font-bold text-sm',
                cancelButton: 'px-5 py-2.5 rounded-xl font-bold text-sm'
            }
        }).then(function (result) {
            if (result.isConfirmed) {
                executeRecordSave();
            }
        });
    } else {
        if (confirm('আপনি কি এই রেকর্ডটির পরিবর্তনগুলো সংরক্ষণ করতে চান?')) {
            executeRecordSave();
        }
    }
}

function executeRecordSave() {
    var id = document.getElementById('edit_record_id').value;
    var type = document.getElementById('edit_record_type').value;

    var phone = document.getElementById('edit_phone') ? document.getElementById('edit_phone').value.trim() : '';
    var name = document.getElementById('edit_name') ? document.getElementById('edit_name').value.trim() : '';
    var product = document.getElementById('edit_product') ? document.getElementById('edit_product').value.trim() : '';
    var serial = document.getElementById('edit_serial') ? document.getElementById('edit_serial').value.trim() : '';
    var remarks = document.getElementById('edit_remarks') ? document.getElementById('edit_remarks').value.trim() : '';
    var status = document.getElementById('edit_status') ? document.getElementById('edit_status').value : '';
    var csrfElement = document.querySelector('meta[name="csrf-token"]');
    var csrfToken = csrfElement ? csrfElement.getAttribute('content') : '';

    if (type === 'external') {
        var vendor = document.getElementById('edit_vendor') ? document.getElementById('edit_vendor').value.trim() : '';
        var finalCost = document.getElementById('edit_cost') ? document.getElementById('edit_cost').value.trim() : '';
        var receiveDate = document.getElementById('edit_date') ? document.getElementById('edit_date').value : '';

        var payload = {
            phone: phone,
            name: name,
            product: product,
            serial: serial || null,
            vendor: vendor || null,
            final_cost: finalCost || null,
            receive_date: receiveDate || null,
            status: status,
            remarks: remarks || null
        };

        fetch('/service/external/' + id + '/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(payload)
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            closeEditModal();
            if (res.success) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'সফলভাবে আপডেট করা হয়েছে!',
                        text: 'রেকর্ডের তথ্যগুলো সফলভাবে সংরক্ষিত হয়েছে।',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else if (typeof showToast === 'function') {
                    showToast('রেকর্ড আপডেট করা হয়েছে!', 'success');
                }
                if (typeof loadExternalCounts === 'function') loadExternalCounts();
                if (typeof loadExternalServices === 'function') loadExternalServices(typeof currentFilter !== 'undefined' ? currentFilter : 'all', typeof searchQuery !== 'undefined' ? searchQuery : '');
            } else {
                alert('❌ আপডেট ব্যর্থ হয়েছে: ' + (res.message || JSON.stringify(res.errors || '')));
            }
        })
        .catch(function(err) {
            closeEditModal();
            console.error('Update external record error:', err);
            alert('❌ সার্ভারে কানেক্ট করতে ব্যর্থ হয়েছে।');
        });

        return;
    }

    if (type === 'inhouse') {
        var address = document.getElementById('edit_address') ? document.getElementById('edit_address').value.trim() : '';
        var pincode = document.getElementById('edit_pincode') ? document.getElementById('edit_pincode').value.trim() : '';
        var finalAmount = document.getElementById('edit_cost') ? document.getElementById('edit_cost').value.trim() : '';
        var receiveDate = document.getElementById('edit_date') ? document.getElementById('edit_date').value : '';

        var payload = {
            phone: phone,
            name: name,
            product: product,
            serial: serial || null,
            address: address || null,
            pincode: pincode || null,
            final_amount: finalAmount || null,
            receive_date: receiveDate || null,
            status: status,
            remarks: remarks || null
        };

        fetch('/service/inhouse/' + id + '/update', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(payload)
        })
        .then(function(r) { return r.json(); })
        .then(function(res) {
            closeEditModal();
            if (res.success) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'সফলভাবে আপডেট করা হয়েছে!',
                        text: 'রেকর্ডের তথ্যগুলো সফলভাবে সংরক্ষিত হয়েছে।',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else if (typeof showToast === 'function') {
                    showToast('রেকর্ড আপডেট করা হয়েছে!', 'success');
                }
                if (typeof loadInhouseCounts === 'function') loadInhouseCounts();
                if (typeof loadInhouseServices === 'function') loadInhouseServices(typeof currentFilter !== 'undefined' ? currentFilter : 'all', typeof searchQuery !== 'undefined' ? searchQuery : '');
            } else {
                alert('❌ আপডেট ব্যর্থ হয়েছে: ' + (res.message || JSON.stringify(res.errors || '')));
            }
        })
        .catch(function(err) {
            closeEditModal();
            console.error('Update inhouse record error:', err);
            alert('❌ সার্ভারে কানেক্ট করতে ব্যর্থ হয়েছে।');
        });

        return;
    }

    // Company fallback
    var list = typeof companyBookings !== 'undefined' ? companyBookings : [];
    var item = list.find(function(b) { return String(b.id) === String(id); });
    if (item) {
        item.phone = phone;
        item.name = name;
        item.product = product;
        item.serial = serial;
        item.remarks = remarks;
        item.status = status;
        if (document.getElementById('edit_address')) item.address = document.getElementById('edit_address').value.trim();
        if (document.getElementById('edit_pincode')) item.pincode = document.getElementById('edit_pincode').value.trim();
        if (document.getElementById('edit_date') && document.getElementById('edit_date').value) item.booking_date = document.getElementById('edit_date').value;
    }

    if (typeof updateDashboardAndTable === 'function') updateDashboardAndTable();
    else if (typeof renderTable === 'function') renderTable();

    closeEditModal();
}

