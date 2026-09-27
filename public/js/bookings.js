"use strict";

let currentTab = "company";
let currentFilter = "all";
let searchQuery = "";


function todayDate() {
    return new Date()
        .toISOString()
        .split("T")[0];
}


function calculateDays(startDate) {

    if (!startDate) {
        return "0 দিন";
    }

    const start = new Date(startDate);
    const today = new Date();

    start.setHours(0, 0, 0, 0);
    today.setHours(0, 0, 0, 0);

    const difference = Math.floor(
        (today - start) /
        (1000 * 60 * 60 * 24)
    );

    return Math.max(0, difference) + " দিন";
}


// ================================
// SERVICE TAB
// ================================

function selectService(type) {

    currentTab = type;
    currentFilter = "all";
    searchQuery = "";

    const area =
        document.getElementById("formArea");

    const tableArea =
        document.getElementById("tableSectionArea");

    if (!area) return;

    if (tableArea) {
        tableArea.classList.remove("hidden");
    }

    document
        .querySelectorAll(".service-tab-btn")
        .forEach(function (btn) {

            btn.classList.remove(
                "ring-4",
                "ring-offset-2",
                "ring-blue-300",
                "ring-orange-300",
                "ring-green-300"
            );
        });


    if (type === "company") {

        document
            .getElementById("btn-company")
            ?.classList.add(
                "ring-4",
                "ring-offset-2",
                "ring-blue-300"
            );

        area.innerHTML =
            document.getElementById(
                "companyFormTemplate"
            ).innerHTML;
    }


    if (type === "external") {

        document
            .getElementById("btn-external")
            ?.classList.add(
                "ring-4",
                "ring-offset-2",
                "ring-orange-300"
            );

        area.innerHTML =
            document.getElementById(
                "externalFormTemplate"
            ).innerHTML;
    }


    if (type === "inhouse") {

        document
            .getElementById("btn-inhouse")
            ?.classList.add(
                "ring-4",
                "ring-offset-2",
                "ring-green-300"
            );

        area.innerHTML =
            document.getElementById(
                "inhouseFormTemplate"
            ).innerHTML;
    }


    const searchInput =
        document.getElementById(
            "globalSearchInput"
        );

    if (searchInput) {
        searchInput.value = "";
    }

    updateDashboardAndTable();
}


// ================================
// COMPANY
// ================================

function saveCompanyBooking(event) {

    event.preventDefault();

    const phone =
        document.getElementById("comp_phone").value.trim();

    const name =
        document.getElementById("comp_name").value.trim();

    const address =
        document.getElementById("comp_address").value.trim();

    const pincode =
        document.getElementById("comp_pincode").value.trim();

    saveCustomerToDatabase(
        phone,
        name,
        address,
        pincode
    );

    const record = {

        id:
            "COMP-" +
            Date.now().toString().slice(-6),

        phone,
        name,
        address,
        pincode,

        product:
            document.getElementById(
                "comp_product"
            ).value.trim(),

        serial:
            document.getElementById(
                "comp_serial"
            ).value.trim(),

        bill_date:
            document.getElementById(
                "comp_bill_date"
            ).value,

        booking_date:
            document.getElementById(
                "comp_booking_date"
            ).value,

        case_id_1:
            document.getElementById("comp_case_1")
                ? document.getElementById("comp_case_1").value.trim()
                : "",

        case_id_2:
            document.getElementById("comp_case_2")
                ? document.getElementById("comp_case_2").value.trim()
                : "",

        remarks:
            document.getElementById(
                "comp_remarks"
            ).value.trim(),

        status: "Pending",

        created_at: todayDate()
    };

    companyBookings.unshift(record);

    saveAllData();

    alert("✅ কোম্পানি কল বুকিং সফলভাবে সেভ হয়েছে!");

    event.target.reset();

    setDefaultDates();

    updateDashboardAndTable();
}


// ================================
// EXTERNAL
// ================================

function saveExternalService(event) {

    event.preventDefault();

    const phone =
        document.getElementById("ext_phone").value.trim();

    const name =
        document.getElementById("ext_name").value.trim();

    const address =
        document.getElementById("ext_address").value.trim();

    const pincode =
        document.getElementById("ext_pincode").value.trim();

    saveCustomerToDatabase(
        phone,
        name,
        address,
        pincode
    );

    const warranty =
        document.querySelector(
            'input[name="ext_warranty"]:checked'
        )?.value || "Out of Warranty";


    externalBookings.unshift({

        id:
            "EXT-" +
            Date.now().toString().slice(-6),

        phone,
        name,
        address,
        pincode,

        product:
            document.getElementById(
                "ext_product"
            ).value.trim(),

        serial:
            document.getElementById(
                "ext_serial"
            ).value.trim(),

        budget:
            document.getElementById(
                "ext_budget"
            ).value || "0",

        final_cost:
            document.getElementById(
                "ext_final_cost"
            ).value || "0",

        receive_date:
            document.getElementById(
                "ext_receive_date"
            ).value,

        vendor:
            document.getElementById(
                "ext_vendor"
            ).value.trim() ||
            "নির্ধারণ করা হয়নি",

        sent_date:
            document.getElementById(
                "ext_sent_date"
            ).value,

        back_date:
            document.getElementById(
                "ext_back_date"
            ).value,

        delivery_date:
            document.getElementById(
                "ext_delivery_date"
            ).value,

        warranty,

        status: "Pending",

        created_at: todayDate()
    });

    saveAllData();

    alert("✅ বাইরের সার্ভিস রেকর্ড সফলভাবে সেভ হয়েছে!");

    event.target.reset();

    const receiveDate =
        document.getElementById(
            "ext_receive_date"
        );

    if (receiveDate) {
        receiveDate.value = todayDate();
    }

    updateDashboardAndTable();
}


// ================================
// INHOUSE
// ================================

function saveInhouseService(event) {

    event.preventDefault();

    const phone =
        document.getElementById("inh_phone").value.trim();

    const name =
        document.getElementById("inh_name").value.trim();

    const address =
        document.getElementById("inh_address").value.trim();

    const pincode =
        document.getElementById("inh_pincode").value.trim();

    saveCustomerToDatabase(
        phone,
        name,
        address,
        pincode
    );

    const warranty =
        document.querySelector(
            'input[name="inh_warranty"]:checked'
        )?.value || "Out of Warranty";


    inhouseBookings.unshift({

        id:
            "INH-" +
            Date.now().toString().slice(-6),

        phone,
        name,
        address,
        pincode,

        alt_phone:
            document.getElementById(
                "inh_alt_phone"
            ).value.trim(),

        product:
            document.getElementById(
                "inh_product"
            ).value.trim(),

        estimate:
            document.getElementById(
                "inh_estimate"
            ).value || "0",

        final_amount:
            document.getElementById(
                "inh_final_amount"
            ).value || "0",

        receive_date:
            document.getElementById(
                "inh_receive_date"
            ).value,

        delivery_date:
            document.getElementById(
                "inh_delivery_date"
            ).value,

        warranty,

        status: "Pending",

        created_at: todayDate()
    });

    saveAllData();

    alert("✅ ইন-হাউস সার্ভিস রেকর্ড সফলভাবে সেভ হয়েছে!");

    event.target.reset();

    const receiveDate =
        document.getElementById(
            "inh_receive_date"
        );

    if (receiveDate) {
        receiveDate.value = todayDate();
    }

    updateDashboardAndTable();
}


// ================================
// DASHBOARD
// ================================

function updateDashboardAndTable() {

    if (currentTab === "company") {

        setText(
            "comp_count_total",
            companyBookings.length
        );

        setText(
            "comp_count_nocase",
            companyBookings.filter(
                b => !b.case_id_1
            ).length
        );

        setText(
            "comp_count_visited",
            companyBookings.filter(
                b => b.status === "Engineer Visited"
            ).length
        );

        setText(
            "comp_count_completed",
            companyBookings.filter(
                b => b.status === "Completed"
            ).length
        );
    }


    if (currentTab === "external") {

        setText(
            "ext_count_total",
            externalBookings.length
        );

        setText(
            "ext_count_pending",
            externalBookings.filter(
                b => b.status === "Pending"
            ).length
        );

        setText(
            "ext_count_sent",
            externalBookings.filter(
                b => b.status === "Sent to Center"
            ).length
        );

        setText(
            "ext_count_back",
            externalBookings.filter(
                b => b.status === "Returned from Center"
            ).length
        );

        setText(
            "ext_count_ready",
            externalBookings.filter(
                b => b.status === "Ready/Back"
            ).length
        );

        setText(
            "ext_count_delivered",
            externalBookings.filter(
                b => b.status === "Delivered"
            ).length
        );
    }


    if (currentTab === "inhouse") {

        setText(
            "inh_count_total",
            inhouseBookings.length
        );

        setText(
            "inh_count_pending",
            inhouseBookings.filter(
                b => b.status === "Pending"
            ).length
        );

        setText(
            "inh_count_ready",
            inhouseBookings.filter(
                b => b.status === "Ready/Repaired"
            ).length
        );

        setText(
            "inh_count_delivered",
            inhouseBookings.filter(
                b => b.status === "Delivered"
            ).length
        );
    }

    renderTable();
}


function setText(id, value) {

    const element =
        document.getElementById(id);

    if (element) {
        element.innerText = value;
    }
}


// ================================
// CURRENT LIST
// ================================

function getCurrentList() {

    if (currentTab === "company") {
        return companyBookings;
    }

    if (currentTab === "external") {
        return externalBookings;
    }

    return inhouseBookings;
}


// ================================
// SEARCH
// ================================

function handleSearch(value) {

    searchQuery =
        String(value || "")
            .trim()
            .toLowerCase();

    renderTable();
}


// ================================
// FILTER
// ================================

function filterData(statusKey) {

    currentFilter = statusKey;

    const badge =
        document.getElementById(
            "activeFilterBadge"
        );

    if (badge) {

        badge.innerText =
            statusKey === "all"
                ? "সব দেখুন"
                : "ফিল্টার: " +
                  statusKey.toUpperCase();
    }

    renderTable();
}


// ================================
// STATUS
// ================================

function updateStatus(id, newStatus) {
    const item = getCurrentList().find(record => record.id === id);
    if (!item) return;

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
                item.status = newStatus;
                saveAllData();
                updateDashboardAndTable();
            } else {
                updateDashboardAndTable();
            }
        });
    } else {
        item.status = newStatus;
        saveAllData();
        updateDashboardAndTable();
    }
}


// ================================
// EDIT
// ================================

function editField(id, fieldName) {

    const item =
        getCurrentList().find(
            record => record.id === id
        );

    if (!item) return;

    const newValue =
        prompt(
            "নতুন তথ্য প্রবেশ করান:",
            item[fieldName] || ""
        );

    if (
        newValue !== null &&
        newValue.trim() !== ""
    ) {

        item[fieldName] =
            newValue.trim();

        saveAllData();

        updateDashboardAndTable();
    }
}


// ================================
// DELETE
// ================================

function deleteRecord(id) {

    if (!confirm(
        "আপনি কি এই সার্ভিস রেকর্ডটি ডিলিট করতে চান?"
    )) {
        return;
    }

    if (currentTab === "company") {

        companyBookings =
            companyBookings.filter(
                item => item.id !== id
            );
    }

    if (currentTab === "external") {

        externalBookings =
            externalBookings.filter(
                item => item.id !== id
            );
    }

    if (currentTab === "inhouse") {

        inhouseBookings =
            inhouseBookings.filter(
                item => item.id !== id
            );
    }

    saveAllData();

    updateDashboardAndTable();
}


// ================================
// DEFAULT DATES
// ================================

function setDefaultDates() {

    const today = todayDate();

    const bill =
        document.getElementById(
            "comp_bill_date"
        );

    const booking =
        document.getElementById(
            "comp_booking_date"
        );

    if (bill) bill.value = today;

    if (booking) booking.value = today;
}