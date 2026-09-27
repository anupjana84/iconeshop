@php
    $page_title = 'Service Settings';
@endphp

@extends('layouts.main')

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

        <!-- DASHBOARD DESIGN (ক্লিক করলে নিচের টেবিল ফিল্টার হবে) -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

            <a
                href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->except(['filter','page']), [])) }}"
                style="background-color:#2563eb;color:#ffffff;"
                class="p-4 rounded-lg shadow block transition-transform hover:-translate-y-0.5
                {{ !request('filter') ? 'ring-4 ring-blue-300' : '' }}"
            >
                <p class="text-xs font-semibold opacity-90">মোট কল বুকিং</p>
                <h4 class="text-2xl font-bold mt-1">{{ $totalCount ?? ($bookings->total() ?? 0) }}</h4>
            </a>

            <a
                href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->except(['filter','page']), ['filter' => 'case_id_pending'])) }}"
                style="background-color:#ef4444;color:#ffffff;"
                class="p-4 rounded-lg shadow block transition-transform hover:-translate-y-0.5
                {{ request('filter') === 'case_id_pending' ? 'ring-4 ring-red-300' : '' }}"
            >
                <p class="text-xs font-semibold opacity-90">কেস আইডি বাকি</p>
                <h4 class="text-2xl font-bold mt-1">{{ $caseIdPendingCount ?? 0 }}</h4>
            </a>

            <a
                href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->except(['filter','page']), ['filter' => 'engineer_visited'])) }}"
                style="background-color:#d97706;color:#ffffff;"
                class="p-4 rounded-lg shadow block transition-transform hover:-translate-y-0.5
                {{ request('filter') === 'engineer_visited' ? 'ring-4 ring-orange-300' : '' }}"
            >
                <p class="text-xs font-semibold opacity-90">ইঞ্জিনিয়ার ভিজিটেড</p>
                <h4 class="text-2xl font-bold mt-1">{{ $engineerVisitedCount ?? 0 }}</h4>
            </a>

            <a
                href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->except(['filter','page']), ['filter' => 'completed'])) }}"
                style="background-color:#16a34a;color:#ffffff;"
                class="p-4 rounded-lg shadow block transition-transform hover:-translate-y-0.5
                {{ request('filter') === 'completed' ? 'ring-4 ring-green-300' : '' }}"
            >
                <p class="text-xs font-semibold opacity-90">কমপ্লিট হয়েছে</p>
                <h4 class="text-2xl font-bold mt-1">{{ $completedCount ?? 0 }}</h4>
            </a>

        </div>

        @if (request('filter'))
            <div class="flex items-center justify-between bg-blue-50 border border-blue-200 text-blue-700 text-sm px-4 py-2 rounded-lg">
                <span>
                    🔍 ফিল্টার করা হয়েছে:
                    <strong>
                        @switch(request('filter'))
                            @case('case_id_pending') কেস আইডি বাকি @break
                            @case('engineer_visited') ইঞ্জিনিয়ার ভিজিটেড @break
                            @case('completed') কমপ্লিট হয়েছে @break
                            @default সব
                        @endswitch
                    </strong>
                </span>
                <a href="{{ url()->current() }}?{{ http_build_query(request()->except(['filter','page'])) }}" class="text-blue-600 hover:underline font-semibold">
                    ✕ ফিল্টার মুছুন
                </a>
            </div>
        @endif


        <!-- IMAGE / PDF OCR SCAN -->



        <!-- COMPANY FORM -->


        <!-- ⭐ SAVED BOOKINGS LIST -->
        <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">

            <div class="flex flex-col sm:flex-row justify-between items-center gap-3 border-b pb-3 mb-4">

                <h3 class="text-lg font-bold text-gray-800">
                    📋 সাম্প্রতিক কল বুকিং তালিকা
                </h3>

            </div>

            <!-- ⭐ SEARCH BOX (icon inside input + search button, full width like screenshot) -->
            <form method="GET" class="flex items-stretch gap-2 mb-5">

                @if (request('filter'))
                    <input type="hidden" name="filter" value="{{ request('filter') }}">
                @endif

                <div class="relative flex-1">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="ফোন নম্বর, নাম বা প্রোডাক্ট দিয়ে খুঁজুন..."
                        class="border border-gray-300 rounded-lg pl-10 pr-4 py-3 text-sm w-full
                        focus:ring-2 focus:ring-blue-400 outline-none"
                        onkeydown="if(event.key==='Enter'){this.form.submit();}"
                    >
                    <span class="absolute inset-y-0 left-3 flex items-center text-gray-400">
                        🔍
                    </span>
                </div>

                <button
                    type="submit"
                    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold
                    text-sm px-5 py-3 rounded-lg shadow-sm transition-colors whitespace-nowrap"
                >
                    🔍 খুঁজুন
                </button>

                @if (request('search'))
                    <a
                        href="{{ url()->current() }}"
                        class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold
                        text-sm px-4 py-3 rounded-lg shadow-sm transition-colors whitespace-nowrap"
                    >
                        ✕ মুছুন
                    </a>
                @endif

            </form>


            <div class="overflow-x-auto">

                <table class="w-full text-sm text-left border-separate" style="border-spacing:0;">

                    <thead class="bg-blue-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="p-3 rounded-l-lg">আইডি</th>
                            <th class="p-3">কাস্টমার ও ফোন</th>
                            <th class="p-3">প্রোডাক্ট</th>
                            <th class="p-3">কেস আইডি</th>
                            <th class="p-3">কল বুকিং তারিখ</th>
                            <th class="p-3">কল বুকের বয়স (Aging)</th>
                            <th class="p-3">স্ট্যাটাস</th>
                            <th class="p-3 rounded-r-lg text-center">অ্যাকশন</th>
                        </tr>
                    </thead>

                    <tbody id="bookingsTableBody" class="divide-y">

                        @forelse ($bookings ?? [] as $booking)

                            @php
                                // ⭐ call_date মডেলে Carbon cast না থাকলে এটা plain string হতে পারে,
                                // তাই নিরাপদে Carbon-এ parse করা হচ্ছে যাতে ->format() এরর না দেয়
                                $rawCallDate = $booking->call_date;

                                if ($rawCallDate && !($rawCallDate instanceof \Illuminate\Support\Carbon)) {
                                    try {
                                        $rawCallDate = \Illuminate\Support\Carbon::parse($rawCallDate);
                                    } catch (\Exception $e) {
                                        $rawCallDate = null;
                                    }
                                }

                                $rawCreatedAt = $booking->created_at;

                                if ($rawCreatedAt && !($rawCreatedAt instanceof \Illuminate\Support\Carbon)) {
                                    try {
                                        $rawCreatedAt = \Illuminate\Support\Carbon::parse($rawCreatedAt);
                                    } catch (\Exception $e) {
                                        $rawCreatedAt = null;
                                    }
                                }

                                // ⭐ প্রদর্শনের তারিখ (কল বুকিং তারিখ কলামের জন্য)
                                $displayDate = $rawCallDate ?: $rawCreatedAt;

                                // ⭐ Aging সবসময় created_at থেকে হিসাব হবে, কারণ call_date-এ সঠিক সময় (ঘন্টা/মিনিট) থাকে না
                                $bookingDate = $rawCreatedAt;

                                $agingLabel = '-';
                                $agingUrgent = false; // ৩ দিনের বেশি হলে লাল করে দেখাবে

                                if ($bookingDate) {
                                    // ⭐ Carbon 3 (Laravel 12) ডিফল্টভাবে ফ্লোট (দশমিক) রিটার্ন করে, তাই পূর্ণ সংখ্যায় নামানো হচ্ছে
                                    $diffInMinutes = (int) floor($bookingDate->diffInMinutes(now()));
                                    $diffInHours   = (int) floor($bookingDate->diffInHours(now()));
                                    $diffInDays    = (int) floor($bookingDate->diffInDays(now()));

                                    if ($diffInMinutes < 1) {
                                        $agingLabel = 'এইমাত্র';
                                    } elseif ($diffInMinutes < 60) {
                                        $agingLabel = $diffInMinutes . ' মিনিট আগে';
                                    } elseif ($diffInHours < 24) {
                                        $agingLabel = $diffInHours . ' ঘন্টা আগে';
                                    } elseif ($diffInDays < 30) {
                                        $agingLabel = $diffInDays . ' দিন আগে';
                                    } elseif ($diffInDays < 365) {
                                        $agingLabel = intdiv($diffInDays, 30) . ' মাস আগে';
                                    } else {
                                        $agingLabel = intdiv($diffInDays, 365) . ' বছর আগে';
                                    }

                                    $agingUrgent = $diffInDays >= 3 && $booking->status !== 'Completed';
                                }

                                $statusColor = match($booking->status) {
                                    'Completed'        => 'bg-green-100 text-green-700',
                                    'Canceled'         => 'bg-red-100 text-red-700',
                                    'Engineer Visited' => 'bg-blue-100 text-blue-700',
                                    default            => 'bg-orange-100 text-orange-700',
                                };
                            @endphp

                            <tr class="hover:bg-gray-50" data-booking-id="{{ $booking->id }}">

                                <!-- আইডি -->
                                <td class="p-3 font-semibold text-gray-500 whitespace-nowrap">
                                    COMP-{{ $booking->id }}
                                </td>

                                <!-- কাস্টমার ও ফোন -->
                                <td class="p-3">
                                    <div class="font-semibold text-gray-800">{{ $booking->name }}</div>
                                    <div class="text-gray-500 text-xs flex items-center gap-1 mt-0.5">
                                        📞 {{ $booking->phone }}
                                    </div>
                                    @if ($booking->address || $booking->pincode)
                                        <div class="text-gray-600 text-xs mt-0.5">
                                            📍 {{ $booking->address }}{{ $booking->pincode ? ' (PIN: ' . $booking->pincode . ')' : '' }}
                                        </div>
                                    @endif
                                </td>

                                <!-- প্রোডাক্ট -->
                                <td class="p-3">
                                    <div class="text-gray-800">{{ $booking->product_name ?? $booking->sl_no ?? '-' }}</div>
                                    <div class="text-gray-400 text-xs">
                                        S/N: {{ $booking->serial_no ?? '-' }}
                                    </div>
                                    @if ($booking->product_name_2 || $booking->sl_no_2)
                                        <div class="text-gray-500 text-xs mt-1">
                                            {{ $booking->product_name_2 ?? $booking->sl_no_2 }}
                                        </div>
                                    @endif
                                </td>

                                <!-- কেস আইডি -->
                                <td class="p-3">
                                    <div class="flex flex-col gap-1 items-start case-id-cell">

                                        @if ($booking->case_id_1)
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                                {{ $booking->case_id_1 }}
                                            </span>
                                        @endif

                                        @if ($booking->case_id_2)
                                            <span class="px-2 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">
                                                {{ $booking->case_id_2 }}
                                            </span>
                                        @endif

                                        @if (!$booking->case_id_1)
                                            <button
                                                type="button"
                                                class="add-case-id-btn px-3 py-1 rounded-full text-xs font-semibold
                                                bg-red-50 text-red-600 hover:bg-red-100 whitespace-nowrap"
                                                data-booking-id="{{ $booking->id }}"
                                            >
                                                ➕ কেস আইডি দিন
                                            </button>
                                        @endif

                                    </div>
                                </td>

                                <!-- কল বুকিং তারিখ -->
                                <td class="p-3 whitespace-nowrap text-gray-700">
                                    {{ $booking->case_id_date ? \Carbon\Carbon::parse($booking->case_id_date)->format('Y-m-d') : ($displayDate ? $displayDate->format('Y-m-d') : '-') }}
                                </td>

                                <!-- কল বুকের বয়স (Aging) -->
                                <td class="p-3 whitespace-nowrap">
                                    <span
                                        class="font-semibold {{ $agingUrgent ? 'text-red-600' : 'text-blue-600' }}"
                                        title="{{ $bookingDate ? $bookingDate->format('Y-m-d h:i A') : '-' }}"
                                    >
                                        {{ $agingUrgent ? '⚠️ ' : '' }}{{ $agingLabel }}
                                    </span>
                                </td>

                                <!-- স্ট্যাটাস -->
                                <td class="p-3">
                                    <select
                                        class="status-select border rounded-lg text-xs px-2 py-1.5 outline-none
                                        focus:ring-2 focus:ring-blue-300 {{ $statusColor }}"
                                        data-booking-id="{{ $booking->id }}"
                                        data-current-status="{{ $booking->status }}"
                                    >
                                        <option value="Pending" {{ $booking->status === 'Pending' ? 'selected' : '' }} disabled>পেন্ডিং</option>
                                        <option value="Engineer Visited" {{ $booking->status === 'Engineer Visited' ? 'selected' : '' }}>ইঞ্জিনিয়ার ভিজিটেড</option>
                                        <option value="Completed"        {{ $booking->status === 'Completed'        ? 'selected' : '' }}>কমপ্লিট</option>
                                        <option value="Canceled"         {{ $booking->status === 'Canceled'         ? 'selected' : '' }}>ক্যান্সেল</option>
                                    </select>
                                </td>

                                <!-- অ্যাকশন -->
                                <td class="p-3">
                                    <div class="flex items-center justify-center gap-3 whitespace-nowrap">

                                        <a
                                            href="{{ url('/admin/service-booking/'.$booking->id.'/print') }}"
                                            target="_blank"
                                            class="text-blue-600 hover:text-blue-800 text-xs font-medium flex items-center gap-1"
                                        >
                                            🖨️ প্রিন্ট
                                        </a>

                                        <button
                                            type="button"
                                            class="delete-booking-btn text-red-500 hover:text-red-700 text-xs font-medium flex items-center gap-1"
                                            data-booking-id="{{ $booking->id }}"
                                        >
                                            🗑️ ডিলিট
                                        </button>

                                    </div>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="8" class="p-6 text-center text-gray-400">
                                    এখনো কোনো কল বুকিং সেভ হয়নি।
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            <!-- ⭐ PAGINATION -->
            @if (isset($bookings) && $bookings->hasPages())
                <div class="mt-4">
                    {{ $bookings->appends(request()->only(['search', 'filter']))->links() }}
                </div>
            @endif

        </div>

    </div>

</div>


<!-- ⭐ CASE ID MODAL (Premium Redesign) -->
<div
    id="caseIdModal"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 backdrop-blur-md px-4 transition-opacity duration-300"
>
    <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl overflow-hidden border border-slate-100 transform transition-all duration-300 scale-95">

        <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-700 px-6 py-5 flex items-center justify-between relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white text-lg shadow-inner">
                    🆔
                </div>
                <div>
                    <h3 class="text-white font-bold text-base tracking-tight">কেস আইডি যোগ করুন</h3>
                    <p class="text-blue-100 text-xs">অফিশিয়াল সার্ভিস ট্র্যাকিং কেস রেফারেন্স এন্ট্রি</p>
                </div>
            </div>
            <button type="button" id="caseIdModalClose" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center text-xl leading-none transition-colors">
                &times;
            </button>
        </div>

        <div class="p-6 space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">কেস আইডি নম্বর <span class="text-red-500">*</span></label>
                <div class="relative">
                    <input
                        type="text"
                        id="caseIdInput"
                        placeholder="উদাহরণ: CASE-10234"
                        class="w-full border border-slate-200 bg-slate-50/50 focus:bg-white rounded-xl px-4 py-3 text-sm text-slate-800 font-semibold focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition"
                    >
                    <span class="absolute right-3.5 top-3.5 text-slate-400 text-sm">🔖</span>
                </div>
                <p id="caseIdError" class="hidden text-xs text-rose-500 mt-2 font-medium flex items-center gap-1">
                    ⚠️ <span>অনুগ্রহ করে একটি কেস আইডি টাইপ করুন।</span>
                </p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">রিমার্কস (Remarks, অপশনাল)</label>
                <div class="relative">
                    <textarea
                        id="caseRemarksInput"
                        rows="2"
                        placeholder="অতিরিক্ত মন্তব্য লিখুন..."
                        class="w-full border border-slate-200 bg-slate-50/50 focus:bg-white rounded-xl px-4 py-3 text-sm text-slate-800 focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 outline-none transition"
                    ></textarea>
                </div>
            </div>
        </div>

        <div class="px-6 pb-6 pt-3 flex items-center justify-end gap-3 bg-slate-50/50 border-t border-slate-100">
            <button
                type="button"
                id="caseIdCancelBtn"
                class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-200/70 bg-slate-100 transition"
            >
                বাতিল
            </button>
            <button
                type="button"
                id="caseIdSaveBtn"
                class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 shadow-md shadow-blue-600/20 active:scale-[0.98] transition flex items-center gap-1.5"
            >
                <span>💾</span>
                <span>সেভ করুন</span>
            </button>
        </div>

    </div>
</div>

<style>
@keyframes fadeIn {
    from { opacity: 0; transform: scale(.96); }
    to   { opacity: 1; transform: scale(1); }
}
</style>


<!-- OCR LIBRARIES -->
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>

<script>

/*
|--------------------------------------------------------------------------
| CSRF TOKEN (shared helper for all AJAX calls below)
|--------------------------------------------------------------------------
*/
function getCsrfToken() {
    const meta = document.querySelector('meta[name="csrf-token"]');
    return meta ? meta.content : '';
}


/*
|--------------------------------------------------------------------------
| ⭐ STATUS DROPDOWN -> UPDATE VIA AJAX WITH SWEETALERT CONFIRMATION
|--------------------------------------------------------------------------
*/

const STATUS_COLOR_CLASSES = {
    'Pending':          ['bg-orange-100', 'text-orange-700'],
    'Engineer Visited': ['bg-blue-100', 'text-blue-700'],
    'Completed':        ['bg-green-100', 'text-green-700'],
    'Canceled':         ['bg-red-100', 'text-red-700'],
};

const ALL_STATUS_COLOR_CLASSES = Object.values(STATUS_COLOR_CLASSES).flat();

function applyStatusColor(selectEl, status) {
    selectEl.classList.remove(...ALL_STATUS_COLOR_CLASSES);
    const classes = STATUS_COLOR_CLASSES[status] || STATUS_COLOR_CLASSES['Pending'];
    selectEl.classList.add(...classes);
}

function performStatusUpdate(selectEl, bookingId, newStatus, previousStatus) {
    selectEl.disabled = true;

    fetch(`/service-booking/${bookingId}/status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': getCsrfToken(),
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({ status: newStatus })
    })
    .then(res => res.json())
    .then(data => {
        if (!data.success) {
            throw new Error(data.message || 'Update failed');
        }

        applyStatusColor(selectEl, newStatus);
        selectEl.dataset.currentStatus = newStatus;

        const pendingOption = selectEl.querySelector('option[value="Pending"]');
        if (pendingOption) {
            pendingOption.remove();
        }

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'স্ট্যাটাস আপডেট সফল!',
                text: `স্ট্যাটাস সফলভাবে "${newStatus}" এ সেট করা হয়েছে।`,
                timer: 1500,
                showConfirmButton: false
            });
        }
    })
    .catch(err => {
        console.error('Status update failed:', err);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'আপডেট ব্যর্থ',
                text: '❌ স্ট্যাটাস আপডেট করতে সমস্যা হয়েছে।'
            });
        } else {
            alert('❌ স্ট্যাটাস আপডেট করতে সমস্যা হয়েছে।');
        }

        selectEl.value = previousStatus;
        applyStatusColor(selectEl, previousStatus);
    })
    .finally(() => {
        selectEl.disabled = false;
    });
}

document.querySelectorAll('.status-select').forEach(function (select) {

    select.addEventListener('change', function () {

        const bookingId    = this.dataset.bookingId;
        const newStatus    = this.value;
        const previousStatus = this.dataset.currentStatus || 'Pending';
        const selectEl      = this;

        if (typeof Swal !== 'undefined') {
            let titleText = 'স্ট্যাটাস পরিবর্তন নিশ্চিতকরণ';
            let textMessage = `আপনি কি স্ট্যাটাস পরিবর্তন করে "${newStatus}" করতে চান?`;
            let iconType = 'warning';
            let confirmBtnColor = '#2563eb';

            if (newStatus === 'Canceled') {
                titleText = '❌ সার্ভিস ক্যানসেল নিশ্চিতকরণ';
                textMessage = 'আপনি কি নিশ্চিত যে এই সার্ভিসটি "Canceled" (বাতিল) করতে চান?';
                iconType = 'error';
                confirmBtnColor = '#ef4444';
            } else if (newStatus === 'Engineer Visited') {
                titleText = '👨‍🔧 ইঞ্জিনিয়ার ভিজিট নিশ্চিতকরণ';
                textMessage = 'আপনি কি স্ট্যাটাস পরিবর্তন করে "Engineer Visited" হিসেবে সেট করতে চান?';
                iconType = 'info';
                confirmBtnColor = '#2563eb';
            } else if (newStatus === 'Pending') {
                titleText = '⏳ পেন্ডিং স্ট্যাটাস নিশ্চিতকরণ';
                textMessage = 'আপনি কি স্ট্যাটাস পরিবর্তন করে "Pending" করতে চান?';
                iconType = 'warning';
                confirmBtnColor = '#ea580c';
            } else if (newStatus === 'Completed') {
                titleText = '✅ কমপ্লিট স্ট্যাটাস নিশ্চিতকরণ';
                textMessage = 'আপনি কি স্ট্যাটাস পরিবর্তন করে "Completed" করতে চান?';
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
            }).then((result) => {
                if (result.isConfirmed) {
                    performStatusUpdate(selectEl, bookingId, newStatus, previousStatus);
                } else {
                    selectEl.value = previousStatus;
                    applyStatusColor(selectEl, previousStatus);
                }
            });
        } else {
            performStatusUpdate(selectEl, bookingId, newStatus, previousStatus);
        }

    });

});


/*
|--------------------------------------------------------------------------
| ⭐ "কেস আইডি দিন" বাটন -> সুন্দর মডাল দিয়ে কেস আইডি নেওয়া ও সেভ করা
|--------------------------------------------------------------------------
| Route এন্ডপয়েন্ট নিজের প্রজেক্ট অনুযায়ী বসাতে হবে:
| POST /admin/service-booking/{id}/case-id  -> { case_id_1 }
|--------------------------------------------------------------------------
*/
(function () {

    const modal       = document.getElementById('caseIdModal');
    const input        = document.getElementById('caseIdInput');
    const errorMsg      = document.getElementById('caseIdError');
    const saveBtn        = document.getElementById('caseIdSaveBtn');
    const cancelBtn        = document.getElementById('caseIdCancelBtn');
    const closeBtn           = document.getElementById('caseIdModalClose');

    let activeBtn = null; // যে বাটনে ক্লিক করে মডাল খোলা হয়েছে

    function openModal(btn) {
        activeBtn = btn;
        input.value = '';
        const remarksEl = document.getElementById('caseRemarksInput');
        if (remarksEl) remarksEl.value = '';
        errorMsg.classList.add('hidden');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => input.focus(), 50);
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        activeBtn = null;
    }

    document.querySelectorAll('.add-case-id-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            openModal(this);
        });
    });

    cancelBtn.addEventListener('click', closeModal);
    closeBtn.addEventListener('click', closeModal);

    // মডালের বাইরে ক্লিক করলে বন্ধ হবে
    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });

    // Enter চাপলে সেভ হবে
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') saveBtn.click();
    });

    saveBtn.addEventListener('click', function () {

        const caseId = input.value.trim();
        const caseRemarks = document.getElementById('caseRemarksInput') ? document.getElementById('caseRemarksInput').value.trim() : '';

        if (!caseId) {
            errorMsg.classList.remove('hidden');
            input.focus();
            return;
        }

        if (!activeBtn) return;

        const bookingId = activeBtn.dataset.bookingId;

        saveBtn.disabled = true;
        saveBtn.innerText = 'সেভ হচ্ছে...';

        fetch(`/admin/service-booking/${bookingId}/case-id`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': getCsrfToken(),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ case_id_1: caseId, remarks: caseRemarks })
        })
        .then(res => res.json())
        .then(data => {

            const cell = activeBtn.closest('.case-id-cell');

            const badge = document.createElement('span');
            badge.className = 'px-2 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700';
            badge.innerText = caseId;

            cell.insertBefore(badge, activeBtn);
            activeBtn.remove();

            closeModal();
        })
        .catch(err => {
            console.error('Case ID save failed:', err);
            alert('❌ কেস আইডি সেভ করতে সমস্যা হয়েছে।');
        })
        .finally(() => {
            saveBtn.disabled = false;
            saveBtn.innerText = '💾 সেভ করুন';
        });

    });

})();


/*
|--------------------------------------------------------------------------
| ⭐ ডিলিট বাটন
|--------------------------------------------------------------------------
| Route এন্ডপয়েন্ট নিজের প্রজেক্ট অনুযায়ী বসাতে হবে:
| DELETE /admin/service-booking/{id}
|--------------------------------------------------------------------------
*/
document.querySelectorAll('.delete-booking-btn').forEach(function (btn) {

    btn.addEventListener('click', function () {

        const bookingId = this.dataset.bookingId;

        showConfirm('ডিলিট বুকিং', 'আপনি কি নিশ্চিত এই বুকিংটি ডিলিট করতে চান?', function() {
            fetch(`/admin/service-booking/${bookingId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                const row = document.querySelector(`tr[data-booking-id="${bookingId}"]`);
                if (row) {
                    row.remove();
                }
                showToast('✅ বুকিং ডিলিট করা হয়েছে!', 'success');
            })
            .catch(err => {
                console.error('Delete failed:', err);
                showToast('❌ ডিলিট করতে সমস্যা হয়েছে।', 'error');
            });
        });

    });

});

</script>

@endsection