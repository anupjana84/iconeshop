@extends('layouts.main')
@push('page_title')
    <title>Ladger</title>
@endpush
@section('content_page')
    <div class="flex justify-center py-4 ">

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Please fix the following issues:</strong>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('ledger.previous.entry.save') }}" method="POST" class="card p-8 shadow-sm border-blue-400 border-2 rounded-lg" style="width: 100%; max-width: 500px;">
            @csrf

            {{-- Select Member --}}
            <div class="mb-3">
                <label class="form-label block mb-1">Select Member</label>
                
                {{-- Client-side Search Input --}}
                <input type="text" id="memberSearch" placeholder="Search customer, company or user..." 
                    class="form-control mb-2 w-full border-1 rounded-sm px-2 py-1 bg-gray-50 focus:bg-white text-sm"
                    onkeyup="filterMembers()">

                <select name="member_id" id="memberSelect"
                    class="form-select border-1 rounded-sm px-2 py-2 w-full bg-white @error('member_id') is-invalid @enderror">
                    <option value="">-- Choose Member --</option>
                    
                    <optgroup label="Companies" id="group-company">
                        @foreach ($members->where('type', 'company') as $member)
                            <option value="{{ $member['type'] . '_' . $member['id'] }}">
                                {{ $member['name'] }} ({{ $member['phone'] ?? 'N/A' }})
                            </option>
                        @endforeach
                    </optgroup>

                    <optgroup label="Customers" id="group-customer">
                        @foreach ($members->where('type', 'customer') as $member)
                            <option value="{{ $member['type'] . '_' . $member['id'] }}">
                                {{ $member['name'] }} ({{ $member['phone'] ?? 'N/A' }})
                            </option>
                        @endforeach
                    </optgroup>

                    <optgroup label="Users" id="group-user">
                        @foreach ($members->where('type', 'user') as $member)
                            <option value="{{ $member['type'] . '_' . $member['id'] }}">
                                {{ $member['name'] }} ({{ $member['phone'] ?? 'N/A' }})
                            </option>
                        @endforeach
                    </optgroup>
                </select>
                @error('member_id')
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Amount Input --}}
            <div class="mb-3">
                <label class="form-label block mb-1">Amount</label>
                <input type="number" name="amount" class="form-control w-full bg-white rounded-sm border-2 p-2 @error('amount') is-invalid @enderror"
                    placeholder="Enter amount" value="{{ old('amount') }}">
                @error('amount')
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>

            {{-- Submit Button --}}
            <div class="mt-4 text-center">
                <button type="submit" class="w-full px-10 py-2 bg-green-600 hover:bg-green-700 rounded-sm text-white font-semibold transition-colors duration-200">
                    Save Entry
                </button>
            </div>
        </form>
    </div>

    <script>
        function filterMembers() {
            let input = document.getElementById('memberSearch');
            let filter = input.value.toLowerCase();
            let select = document.getElementById('memberSelect');
            let options = select.getElementsByTagName('option');
            let optgroups = select.getElementsByTagName('optgroup');

            // Start from 1 to skip "Choose Member"
            for (let i = 1; i < options.length; i++) {
                let txtValue = options[i].textContent || options[i].innerText;
                if (txtValue.toLowerCase().indexOf(filter) > -1) {
                    options[i].style.display = "";
                } else {
                    options[i].style.display = "none";
                }
            }

            // Hide/Show optgroups if all their children are hidden
            for (let i = 0; i < optgroups.length; i++) {
                let opts = optgroups[i].getElementsByTagName('option');
                let hasVisible = false;
                for (let j = 0; j < opts.length; j++) {
                    if (opts[j].style.display !== "none") {
                        hasVisible = true;
                        break;
                    }
                }
                optgroups[i].style.display = hasVisible ? "" : "none";
            }
        }
    </script>

@endsection
