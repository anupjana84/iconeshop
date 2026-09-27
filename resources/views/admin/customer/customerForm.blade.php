@extends('layouts.main')
@push('page_title')
    <title> {{ isset($user) ? 'Update User' : 'Create User' }}</title>
@endpush
@section('content_page')
    <div>
        <div class="max-w-2xl mx-auto bg-white shadow-lg rounded p-6">
            <h2 class="text-xl font-semibold mb-4">
                {{ isset($customer) ? 'Edit Customer' : 'Create Customer' }}
            </h2>

            <form method="POST"
                action="{{ isset($customer) ? route('customers.update', $customer->id) : route('customers.store') }}">
                @csrf
                @if (isset($customer))
                    @method('PUT')
                @endif

                <!-- Name -->
                <div class="mb-4">
                    <label class="block text-gray-700">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $customer->name ?? '') }}"
                        class="w-full border p-2 rounded @error('name') border-red-500 @enderror">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div class="mb-4">
                    <label class="block text-gray-700">Phone <span class="text-red-500">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone', $customer->phone ?? '') }}"
                        class="w-full border p-2 rounded @error('phone') border-red-500 @enderror">
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- WhatsApp Number -->
                <div class="mb-4">
                    <label class="block text-gray-700">WhatsApp</label>
                    <input type="text" name="wpnumber" value="{{ old('wpnumber', $customer->wpnumber ?? '') }}"
                        class="w-full border p-2 rounded @error('wpnumber') border-red-500 @enderror">
                    @error('wpnumber')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address -->
                <div class="mb-4">
                    <label class="block text-gray-700">Address<span class="text-red-500">*</span></label>
                    <textarea name="address" rows="3" class="w-full border p-2 rounded @error('address') border-red-500 @enderror">{{ old('address', $customer->address ?? '') }}</textarea>
                    @error('address')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- PIN -->
                <div class="mb-4">
                    <label class="block text-gray-700">PIN<span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="text" id="customer_pin_field" name="pin" value="{{ old('pin', $customer->pin ?? '') }}"
                            maxlength="6" placeholder="Enter 6-digit Pincode"
                            class="w-full border p-2 rounded @error('pin') border-red-500 @enderror">
                        <span id="pin_loading" class="hidden absolute right-3 top-2.5 text-xs text-blue-600 font-semibold">Checking API...</span>
                    </div>
                    <div id="pin_api_status" class="text-xs font-semibold mt-1 hidden"></div>
                    @error('pin')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- GST Number -->
                <div class="mb-4">
                    <label class="block text-gray-700">GST Number</label>
                    <input type="text" name="gst_number" value="{{ old('gst_number', $customer->gst_number ?? '') }}"
                        class="w-full border p-2 rounded @error('gst_number') border-red-500 @enderror">
                    @error('gst_number')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex space-x-2">
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                        {{ isset($customer) ? 'Update' : 'Create' }}
                    </button>
                    <a href="{{ route('customers.list') }}"
                        class="bg-gray-400 text-white px-4 py-2 rounded hover:bg-gray-500">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

    </div>
@endsection
@push('extra_style')
@endpush
@push('extra_js')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const pinInput = document.getElementById("customer_pin_field");
        const pinStatus = document.getElementById("pin_api_status");
        const pinLoading = document.getElementById("pin_loading");

        if (pinInput) {
            pinInput.addEventListener("input", function() {
                const pincode = this.value.trim();
                pinStatus.classList.add("hidden");
                pinStatus.textContent = "";

                if (/^\d{6}$/.test(pincode)) {
                    pinLoading.classList.remove("hidden");
                    fetch(`https://api.postalpincode.in/pincode/${pincode}`)
                        .then(res => res.json())
                        .then(data => {
                            pinLoading.classList.add("hidden");
                            if (data && data[0] && data[0].Status === "Success") {
                                const details = data[0].PostOffice[0];
                                pinStatus.textContent = `✓ Valid Pincode: ${details.District}, ${details.State}`;
                                pinStatus.className = "text-xs font-semibold mt-1 text-green-600";
                                pinStatus.classList.remove("hidden");
                            } else {
                                pinStatus.textContent = "❌ Invalid Pincode / Region not found";
                                pinStatus.className = "text-xs font-semibold mt-1 text-red-500";
                                pinStatus.classList.remove("hidden");
                            }
                        })
                        .catch(err => {
                            pinLoading.classList.add("hidden");
                            console.error("Pincode API error:", err);
                        });
                }
            });
            // Initial check if editing
            if (/^\d{6}$/.test(pinInput.value.trim())) {
                pinInput.dispatchEvent(new Event("input"));
            }
        }
    });
</script>
@endpush
