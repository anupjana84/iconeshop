"use strict";

function renderTable() {

    const thead =
        document.getElementById(
            "mainTableHead"
        );

    const tbody =
        document.getElementById(
            "mainTableBody"
        );

    if (!thead || !tbody) {
        return;
    }

    let list =
        getCurrentList().slice();


    // SEARCH
    if (searchQuery !== "") {

        list = list.filter(function (item) {

            const searchable = [

                item.name,
                item.phone,
                item.product,
                item.serial,
                item.id,
                item.vendor

            ]
                .filter(Boolean)
                .join(" ")
                .toLowerCase();

            return searchable.includes(
                searchQuery
            );
        });
    }


    // FILTER
    if (currentFilter !== "all") {

        if (currentTab === "company") {

            if (currentFilter === "nocase") {

                list =
                    list.filter(
                        b => !b.case_id_1
                    );
            }

            if (currentFilter === "visited") {

                list =
                    list.filter(
                        b =>
                            b.status ===
                            "Engineer Visited"
                    );
            }

            if (currentFilter === "completed") {

                list =
                    list.filter(
                        b =>
                            b.status ===
                            "Completed"
                    );
            }
        }


        if (currentTab === "external") {

            const statusMap = {

                pending: "Pending",
                sent: "Sent to Center",
                back: "Returned from Center",
                ready: "Ready/Back",
                delivered: "Delivered"
            };

            if (statusMap[currentFilter]) {

                list =
                    list.filter(
                        b =>
                            b.status ===
                            statusMap[currentFilter]
                    );
            }
        }


        if (currentTab === "inhouse") {

            const statusMap = {

                pending: "Pending",
                ready: "Ready/Repaired",
                delivered: "Delivered"
            };

            if (statusMap[currentFilter]) {

                list =
                    list.filter(
                        b =>
                            b.status ===
                            statusMap[currentFilter]
                    );
            }
        }
    }


    // HEADER
    if (currentTab === "company") {

        thead.innerHTML = `
            <tr class="bg-blue-50 border-b text-blue-900">
                <th class="p-3">আইডি</th>
                <th class="p-3">কাস্টমার ও ফোন</th>
                <th class="p-3">প্রোডাক্ট</th>
                <th class="p-3">কেস আইডি</th>
                <th class="p-3">বুকিং তারিখ</th>
                <th class="p-3">Aging</th>
                <th class="p-3">স্ট্যাটাস</th>
                <th class="p-3 text-center">অ্যাকশন</th>
            </tr>
        `;
    }


    if (currentTab === "external") {

        thead.innerHTML = `
            <tr class="bg-orange-50 border-b text-orange-900">
                <th class="p-3">আইডি</th>
                <th class="p-3">কাস্টমার ও ফোন</th>
                <th class="p-3">প্রোডাক্ট</th>
                <th class="p-3">ভেন্ডর</th>
                <th class="p-3">Aging</th>
                <th class="p-3">ফাইনাল চার্জ</th>
                <th class="p-3">স্ট্যাটাস</th>
                <th class="p-3 text-center">অ্যাকশন</th>
            </tr>
        `;
    }


    if (currentTab === "inhouse") {

        thead.innerHTML = `
            <tr class="bg-green-50 border-b text-green-900">
                <th class="p-3">আইডি</th>
                <th class="p-3">কাস্টমার ও ফোন</th>
                <th class="p-3">প্রোডাক্ট/সমস্যা</th>
                <th class="p-3">Aging</th>
                <th class="p-3">এস্টিমেট / ফাইনাল</th>
                <th class="p-3">স্ট্যাটাস</th>
                <th class="p-3 text-center">অ্যাকশন</th>
            </tr>
        `;
    }


    if (list.length === 0) {

        tbody.innerHTML = `
            <tr>
                <td colspan="8"
                    class="text-center p-8 text-gray-400">
                    কোনো তথ্য পাওয়া যায়নি।
                </td>
            </tr>
        `;

        return;
    }


    let html = "";


    list.forEach(function (item) {


        // COMPANY
        if (currentTab === "company") {

            html += `
                <tr class="border-b hover:bg-gray-50">

                    <td class="p-3 font-semibold text-xs">
                        ${escapeHtml(item.id)}
                    </td>

                    <td class="p-3">
                        <div class="font-bold">
                            ${escapeHtml(item.name)}
                        </div>

                        <div class="text-xs text-gray-500">
                            📞 ${escapeHtml(item.phone)}
                        </div>

                        ${(item.address || item.pincode) ? `
                            <div class="text-xs text-gray-600 mt-0.5">
                                📍 ${escapeHtml(item.address || '')}${item.pincode ? ` (PIN: ${escapeHtml(item.pincode)})` : ''}
                            </div>
                        ` : ''}
                    </td>

                    <td class="p-3">
                        <div class="text-xs font-semibold">
                            ${escapeHtml(item.product)}
                        </div>

                        <div class="text-xs text-gray-500">
                            S/N:
                            ${escapeHtml(item.serial)}
                        </div>
                    </td>

                    <td class="p-3">
                        ${
                            item.case_id_1
                            ? `
                                <span class="bg-blue-100 text-blue-800 font-bold px-2 py-1 rounded text-xs">
                                    ${escapeHtml(item.case_id_1)}
                                </span>
                                ${item.case_id_2 ? `
                                    <span class="bg-purple-100 text-purple-800 font-bold px-2 py-1 rounded text-xs mt-1 block">
                                        ${escapeHtml(item.case_id_2)}
                                    </span>
                                ` : ''}
                            `
                            : `
                                <button
                                    onclick="editField('${item.id}','case_id_1')"
                                    class="text-xs bg-red-100 text-red-600 px-2 py-1 rounded font-bold">
                                    ➕ কেস আইডি দিন
                                </button>
                            `
                        }
                    </td>

                    <td class="p-3 text-xs">
                        ${escapeHtml(item.case_id_date || item.booking_date || '-')}
                    </td>

                    <td class="p-3 text-xs">
                        <span class="bg-blue-100 text-blue-800 font-bold px-2 py-1 rounded-full">
                            ${calculateDays(item.booking_date)}
                        </span>
                    </td>

                    <td class="p-3">
                        ${companyStatusSelect(item)}
                    </td>

                    <td class="p-3 text-center">

                        <button
                            onclick="printReceipt('${item.id}')"
                            class="text-blue-600 text-xs mr-2">
                            🖨️
                        </button>

                        <button
                            onclick="deleteRecord('${item.id}')"
                            class="text-red-500 text-xs">
                            🗑️
                        </button>

                    </td>

                </tr>
            `;
        }


        // EXTERNAL
        if (currentTab === "external") {

            html += `
                <tr class="border-b hover:bg-gray-50">

                    <td class="p-3 text-xs font-semibold">
                        ${escapeHtml(item.id)}
                    </td>

                    <td class="p-3">
                        <div class="font-bold">
                            ${escapeHtml(item.name)}
                        </div>

                        <div class="text-xs text-gray-500">
                            📞 ${escapeHtml(item.phone)}
                        </div>

                        ${(item.address || item.pincode) ? `
                            <div class="text-xs text-gray-600 mt-0.5">
                                📍 ${escapeHtml(item.address || '')}${item.pincode ? ` (PIN: ${escapeHtml(item.pincode)})` : ''}
                            </div>
                        ` : ''}
                    </td>

                    <td class="p-3 text-xs font-semibold">
                        ${escapeHtml(item.product)}
                    </td>

                    <td class="p-3 text-xs">
                        ${escapeHtml(item.vendor)}

                        <button
                            onclick="editField('${item.id}','vendor')"
                            class="text-blue-500 text-[10px]">
                            ✏️
                        </button>
                    </td>

                    <td class="p-3 text-xs">
                        <span class="bg-orange-100 text-orange-800 font-bold px-2 py-1 rounded">
                            ${calculateDays(item.receive_date)}
                        </span>
                    </td>

                    <td class="p-3 text-xs font-bold text-green-700">
                        ₹${escapeHtml(item.final_cost || "0")}

                        <button
                            onclick="editField('${item.id}','final_cost')"
                            class="text-blue-500 text-[10px]">
                            ✏️
                        </button>
                    </td>

                    <td class="p-3">
                        ${externalStatusSelect(item)}
                    </td>

                    <td class="p-3 text-center">

                        <button
                            onclick="openEditModal('${item.id}')"
                            class="text-amber-600 hover:text-amber-800 hover:underline text-xs font-bold mr-2">
                            ✏️
                        </button>

                        <button
                            onclick="printReceipt('${item.id}')"
                            class="text-blue-600 text-xs mr-2">
                            🖨️
                        </button>

                        <button
                            onclick="deleteRecord('${item.id}')"
                            class="text-red-500 text-xs">
                            🗑️
                        </button>

                    </td>

                </tr>
            `;
        }


        // INHOUSE
        if (currentTab === "inhouse") {

            html += `
                <tr class="border-b hover:bg-gray-50">

                    <td class="p-3 text-xs font-semibold">
                        ${escapeHtml(item.id)}
                    </td>

                    <td class="p-3">

                        <div class="font-bold">
                            ${escapeHtml(item.name)}
                        </div>

                        <div class="text-xs text-gray-500">
                            📞 ${escapeHtml(item.phone)}
                        </div>

                        ${(item.address || item.pincode) ? `
                            <div class="text-xs text-gray-600 mt-0.5">
                                📍 ${escapeHtml(item.address || '')}${item.pincode ? ` (PIN: ${escapeHtml(item.pincode)})` : ''}
                            </div>
                        ` : ''}

                    </td>

                    <td class="p-3 text-xs font-semibold">
                        ${escapeHtml(item.product)}
                    </td>

                    <td class="p-3 text-xs">

                        <span class="bg-green-100 text-green-800 font-bold px-2 py-1 rounded">
                            ${calculateDays(item.receive_date)}
                        </span>

                    </td>

                    <td class="p-3 text-xs">

                        Est:
                        ₹${escapeHtml(item.estimate || "0")}

                        |

                        <span class="font-bold text-green-700">
                            Final:
                            ₹${escapeHtml(item.final_amount || "0")}
                        </span>

                        <button
                            onclick="editField('${item.id}','final_amount')"
                            class="text-blue-500 text-[10px]">
                            ✏️
                        </button>

                    </td>

                    <td class="p-3">
                        ${inhouseStatusSelect(item)}
                    </td>

                    <td class="p-3 text-center">

                        <button
                            onclick="openEditModal('${item.id}')"
                            class="text-amber-600 hover:text-amber-800 hover:underline text-xs font-bold mr-2">
                            ✏️
                        </button>

                        <button
                            onclick="printReceipt('${item.id}')"
                            class="text-blue-600 text-xs mr-2">
                            🖨️
                        </button>

                        <button
                            onclick="deleteRecord('${item.id}')"
                            class="text-red-500 text-xs">
                            🗑️
                        </button>

                    </td>

                </tr>
            `;
        }

    });


    tbody.innerHTML = html;
}


// ================================
// STATUS SELECTS
// ================================

function companyStatusSelect(item) {

    return `
        <select
            onchange="updateStatus('${item.id}',this.value)"
            class="text-xs border rounded p-1 font-semibold bg-white">

            <option value="Pending"
                ${item.status === "Pending" ? "selected" : ""}>
                পেন্ডিং
            </option>

            <option value="Engineer Visited"
                ${item.status === "Engineer Visited" ? "selected" : ""}>
                ইঞ্জিনিয়ার ভিজিটেড
            </option>

            <option value="Completed"
                ${item.status === "Completed" ? "selected" : ""}>
                কমপ্লিট
            </option>

        </select>
    `;
}


function externalStatusSelect(item) {

    return `
        <select
            onchange="updateStatus('${item.id}',this.value)"
            class="text-xs border rounded p-1 font-semibold bg-white">

            <option value="Pending"
                ${item.status === "Pending" ? "selected" : ""}>
                পেন্ডিং
            </option>

            <option value="Sent to Center"
                ${item.status === "Sent to Center" ? "selected" : ""}>
                পাঠানো হয়েছে
            </option>

            <option value="Returned from Center"
                ${item.status === "Returned from Center" ? "selected" : ""}>
                ফিরে এসেছে
            </option>

            <option value="Ready/Back"
                ${item.status === "Ready/Back" ? "selected" : ""}>
                ডেলিভারি বাকি
            </option>

            <option value="Delivered"
                ${item.status === "Delivered" ? "selected" : ""}>
                ডেলিভারি সম্পন্ন
            </option>

        </select>
    `;
}


function inhouseStatusSelect(item) {

    return `
        <select
            onchange="updateStatus('${item.id}',this.value)"
            class="text-xs border rounded p-1 font-semibold bg-white">

            <option value="Pending"
                ${item.status === "Pending" ? "selected" : ""}>
                পেন্ডিং
            </option>

            <option value="Ready/Repaired"
                ${item.status === "Ready/Repaired" ? "selected" : ""}>
                কাজ কমপ্লিট
            </option>

            <option value="Delivered"
                ${item.status === "Delivered" ? "selected" : ""}>
                ডেলিভারি
            </option>

        </select>
    `;
}


// ================================
// HTML ESCAPE
// ================================

function escapeHtml(value) {

    return String(value ?? "")
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}