@extends('layouts.main')

@push('page_title')
    <title>Make Payment</title>
@endpush

@section('content_page')
    <div class="max-w-3xl mx-auto my-8 bg-white p-8 rounded-2xl shadow-lg border border-gray-100">

        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">💳 Create Payment</h2>

        <form action="{{ route('payment.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Entry Member -->
            <div>
                <label for="entry_member" class="block text-sm font-medium text-gray-700 mb-1">
                    Entry Member type<span class="text-red-500">*</span>
                </label>
                <select id="entry_member" name="entry_member"
                    class="w-full p-3 rounded-lg border @error('entry_member') border-red-500 @else border-gray-300 @enderror focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">-- Select Member Type --</option>
                    <option value="customer" {{ old('entry_member') == 'customer' ? 'selected' : '' }}>Customer</option>
                    <option value="company" {{ old('entry_member') == 'company' ? 'selected' : '' }}>Company (Vendor)
                    </option>
                    <option value="manager" {{ old('entry_member') == 'manager' ? 'selected' : '' }}>Manager</option>
                    <option value="subdealer" {{ old('entry_member') == 'subdealer' ? 'selected' : '' }}>Sub Dealer</option>
                    <option value="salesman" {{ old('entry_member') == 'salesman' ? 'selected' : '' }}>Salesman</option>
                    <option value="expenses" {{ old('entry_member') == 'expenses' ? 'selected' : '' }}>Expenses</option>
                </select>
                @error('entry_member')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Dynamic Name — Simple stored JSON + search + select -->
            <div>
                <label for="member_search" class="block text-sm font-medium text-gray-700 mb-1">
                    Name <span class="text-red-500">*</span>
                </label>

                <!-- search input -->
                <input id="member_search" type="text" autocomplete="off" placeholder="Type to search (name or phone)..."
                    value="{{ old('member_name') ?? '' }}"
                    class="w-full p-3 rounded-lg border @error('member_id') border-red-500 @else border-gray-300 @enderror focus:ring-2 focus:ring-green-500 focus:border-green-500 mb-2" />

                <!-- actual select that will be submitted (name=member_id) -->
                <select id="member_select" name="member_id"
                    class="w-full p-2 rounded-lg border @error('member_id') border-red-500 @else border-gray-300 @enderror focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">-- Select a name --</option>
                    {{-- options will be filled by JS --}}
                </select>

                @error('member_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>


            <!-- Amount -->
            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">
                    Amount <span class="text-red-500">*</span>
                </label>
                <input type="number" id="amount" name="amount" step="0.01" value="{{ old('amount') }}"
                    class="w-full p-3 rounded-lg border @error('amount') border-red-500 @else border-gray-300 @enderror focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    placeholder="Enter amount">
                @error('amount')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- method -->
            <div>
                <label for="pay_method" class="block text-sm font-medium text-gray-700 mb-1">
                    Pay Method <span class="text-red-500">*</span>
                </label>
                <select id="pay_method" name="pay_method"
                    class="w-full p-3 rounded-lg border @error('pay_method') border-red-500 @else border-gray-300 @enderror focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">-- Select--</option>
                    <option value="cash" {{ old('pay_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="online" {{ old('pay_method') == 'online' ? 'selected' : '' }}>Online</option>
                </select>
                @error('pay_method')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <!-- bank -->
            <div>
                <label for="pay_method" class="block text-sm font-medium text-gray-700 mb-1">
                    Bank<span class="text-red-500">*</span>
                </label>
                <select id="bank" name="bank_id"
                    class="w-full p-3 rounded-lg border @error('bank_id') border-red-500 @else border-gray-300 @enderror focus:ring-2 focus:ring-green-500 focus:border-green-500">

                    <option value="">-- Select Bank --</option>

                    @foreach ($banks as $bank)
                        <option value="{{ $bank->id }}"
                            {{ old('bank_id') == $bank->id ? 'selected' : '' }}>
                            {{ $bank->name }}
                        </option>
                    @endforeach
                </select>

                @error('bank_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror

            </div>
            <!-- Payment Type -->
            <div>
                <label for="pay_type" class="block text-sm font-medium text-gray-700 mb-1">
                    Pay Method <span class="text-red-500">*</span>
                </label>
                <select id="pay_type" name="pay_type"
                    class="w-full p-3 rounded-lg border @error('pay_type') border-red-500 @else border-gray-300 @enderror focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    <option value="">-- Select--</option>
                    <option value="inflow" {{ old('pay_type') == 'inflow' ? 'selected' : '' }}>Recived Payment</option>
                    <option value="outflow" {{ old('pay_type') == 'outflow' ? 'selected' : '' }}>Pay user/anybody</option>
                </select>
                @error('pay_type')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Date -->
            <div>
                <label for="date" class="block text-sm font-medium text-gray-700 mb-1">
                    Date <span class="text-red-500">*</span>
                </label>
                <input type="date" id="date" name="date" value="{{ old('date') }}"
                    class="w-full p-3 rounded-lg border @error('date') border-red-500 @else border-gray-300 @enderror focus:ring-2 focus:ring-green-500 focus:border-green-500">
                @error('date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Description
                </label>
                <textarea id="description" name="description" rows="3"
                    class="w-full p-3 rounded-lg border @error('description') border-red-500 @else border-gray-300 @enderror focus:ring-2 focus:ring-green-500 focus:border-green-500"
                    placeholder="Payment description...">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <div>
                <button type="submit"
                    class="w-full py-3 px-4 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-lg shadow-md hover:shadow-lg hover:from-green-600 hover:to-emerald-700 transition">
                    💾 Save Payment
                </button>
            </div>
        </form>
    </div>
@endsection
@push('style_link')
    {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" /> --}}
@endpush

@push('extra_js')
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elements
            const entryMember = document.getElementById('entry_member');
            const searchInput = document.getElementById('member_search');
            const memberSelect = document.getElementById('member_select');

            if (!entryMember || !searchInput || !memberSelect) {
                console.error("Required elements (#entry_member, #member_search, #member_select) not found.");
                return;
            }

            // Stored member data for current member type
            let members = [];

            // Debounce helper
            function debounce(fn, delay) {
                let t;
                return function(...args) {
                    clearTimeout(t);
                    t = setTimeout(() => fn.apply(this, args), delay);
                };
            }

            // Render select options from an array
            function populateSelect(list) {
                // keep first placeholder option
                memberSelect.innerHTML = '<option value="">-- Select a name --</option>';
                if (!list || list.length === 0) {
                    // optionally show "No results"
                    const opt = document.createElement('option');
                    opt.value = '';
                    opt.textContent = 'No results';
                    opt.disabled = true;
                    memberSelect.appendChild(opt);
                    return;
                }

                const frag = document.createDocumentFragment();
                list.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item.id;
                    // visible label — name and phone for convenience
                    opt.textContent = item.name + (item.phone ? ' - ' + item.phone : '');
                    frag.appendChild(opt);
                });
                memberSelect.appendChild(frag);
            }

            // Simple local filter (name or phone)
            function filterMembers(q) {
                const qq = (q || '').trim().toLowerCase();
                if (!qq) return members.slice(); // return copy of all
                return members.filter(m => {
                    const name = (m.name || '').toLowerCase();
                    const phone = (m.phone || '').toLowerCase();
                    return name.includes(qq) || phone.includes(qq);
                });
            }

            // When member type changes: fetch members once, then populate select and clear search
            entryMember.addEventListener('change', function() {
                const type = entryMember.value;
                members = [];
                memberSelect.innerHTML = '<option value="">-- Select a name --</option>';
                searchInput.value = '';

                if (!type) return;

                // loading placeholder
                const loading = document.createElement('option');
                loading.textContent = 'Loading...';
                loading.disabled = true;
                memberSelect.appendChild(loading);

                fetch(`/api/get-members/${encodeURIComponent(type)}`)
                    .then(res => {
                        if (!res.ok) throw new Error('Network response not ok');
                        return res.json();
                    })
                    .then(data => {
                        // normalize: expect array of objects {id, name, phone}
                        members = Array.isArray(data) ? data : [];
                        populateSelect(members);
                    })
                    .catch(err => {
                        console.error(err);
                        memberSelect.innerHTML = '<option value="">Error loading data</option>';
                    });
            });

            // When user types: filter locally and repopulate select (debounced)
            const onType = debounce(function() {
                // Clear current selected option (user must pick again)
                memberSelect.value = '';
                const filtered = filterMembers(searchInput.value);
                populateSelect(filtered);
                // If only one result, optionally auto-select it (uncomment if desired)
                // if (filtered.length === 1) memberSelect.value = filtered[0].id;
            }, 150);

            searchInput.addEventListener('input', onType);

            // Optional UX: when user picks from select, show the chosen label in search input
            memberSelect.addEventListener('change', function() {
                const sel = memberSelect.options[memberSelect.selectedIndex];
                if (sel && sel.value) {
                    // set search input text to selected label for clarity
                    searchInput.value = sel.textContent;
                }
            });

            // If page loads with old values (editing), try to restore:
            (function restoreIfOld() {
                const oldId = memberSelect.value; // if blade set old('member_id'), it'll be present
                const oldType = entryMember.value;
                if (oldId && oldType) {
                    // fetch members to find label if needed
                    fetch(`/api/get-members/${encodeURIComponent(oldType)}`)
                        .then(r => r.json())
                        .then(data => {
                            members = Array.isArray(data) ? data : [];
                            populateSelect(members);
                            // set selection and also update searchInput display
                            if (memberSelect.querySelector(`option[value="${oldId}"]`)) {
                                memberSelect.value = oldId;
                                const text = memberSelect.options[memberSelect.selectedIndex].textContent ||
                                    '';
                                searchInput.value = text;
                            }
                        })
                        .catch(() => {});
                }
            })();
        });
    </script>
@endpush
