@extends('layouts.guestLayout.main')
@push('page_title')
    <title>Home</title>
@endpush
@section('content_page')
    <div class="min-h-screen w-full bg-gradient-to-br from-blue-50 via-white to-purple-50 py-16 px-4">
        <div class="max-w-6xl mx-auto">

            <!-- Heading -->
            <div class="text-center mb-16">
                <h1 class="text-4xl md:text-5xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 mb-4">
                    Get in Touch
                </h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    We'd love to hear from you. Here's how you can reach us.
                </p>
            </div>
            
            <div class="bg-white rounded-2xl shadow-lg p-8 mb-16 border border-gray-100">
    
    @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <h2 class="text-2xl font-bold text-center mb-6">Send Enquiry</h2>

    <form action="{{ route('enquiry.submit') }}" method="POST" class="grid md:grid-cols-2 gap-6">
        @csrf

        <input type="text" name="name" placeholder="Your Name"
            class="p-3 border rounded-lg w-full" required>

        <input type="email" name="email" placeholder="Your Email"
            class="p-3 border rounded-lg w-full" required>

        <input type="text" name="mobile" placeholder="Mobile Number"
            class="p-3 border rounded-lg w-full" required>

        <input type="text" name="loc" placeholder="Location"
            class="p-3 border rounded-lg w-full">

        <textarea name="msg" placeholder="Your Message"
            class="p-3 border rounded-lg w-full md:col-span-2" rows="4" required></textarea>

        <!-- Checkbox -->
        <div class="md:col-span-2 flex items-start gap-2">
            <input type="checkbox" name="agree" required class="mt-1">
            <label class="text-sm text-gray-600">
                I Authorise <strong>iconcomputer</strong> to send notification via sms / rcs / call / email / whatsapp
            </label>
        </div>

        <!-- Submit -->
        <div class="md:col-span-2 text-center">
            <button type="submit"
                class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                Submit Enquiry
            </button>
        </div>

    </form>
</div>

            <!-- Cards Container -->
            <div class="grid md:grid-cols-3 gap-8">

                <!-- Address Card -->
                <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transform hover:-translate-y-2 transition duration-300 border border-gray-100 overflow-hidden group">
                    <div class="h-2 bg-gradient-to-r from-blue-400 to-blue-600"></div>
                    <div class="p-8 flex flex-col items-center text-center">
                        <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center mb-6 group-hover:bg-blue-100 transition">
                            <i class="fa-solid fa-location-dot text-3xl text-blue-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Visit Us</h2>
                        <p class="text-gray-600 leading-relaxed">
                            <span class="font-semibold text-gray-900">ICON COMPUTER</span><br>
                            Fultata, Bethuadahari,<br>
                            Nadia, Pin-741126, WB
                        </p>
                    </div>
                </div>

                <!-- Phone Card -->
                <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transform hover:-translate-y-2 transition duration-300 border border-gray-100 overflow-hidden group">
                    <div class="h-2 bg-gradient-to-r from-purple-400 to-purple-600"></div>
                    <div class="p-8 flex flex-col items-center text-center">
                        <div class="w-16 h-16 bg-purple-50 rounded-full flex items-center justify-center mb-6 group-hover:bg-purple-100 transition">
                            <i class="fa-solid fa-phone text-3xl text-purple-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Call Us</h2>
                        <div class="text-gray-600 space-y-1">
                            <p class="hover:text-purple-600 transition cursor-pointer">9332226500</p>
                            <p class="hover:text-purple-600 transition cursor-pointer">8670569446</p>
                        </div>
                    </div>
                </div>

                <!-- Email Card -->
                <div class="bg-white rounded-2xl shadow-lg hover:shadow-2xl transform hover:-translate-y-2 transition duration-300 border border-gray-100 overflow-hidden group">
                    <div class="h-2 bg-gradient-to-r from-teal-400 to-teal-600"></div>
                    <div class="p-8 flex flex-col items-center text-center">
                        <div class="w-16 h-16 bg-teal-50 rounded-full flex items-center justify-center mb-6 group-hover:bg-teal-100 transition">
                            <i class="fa-solid fa-envelope text-3xl text-teal-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Email Us</h2>
                        <a href="mailto:support@iconcomputer.in" class="text-teal-600 font-medium hover:underline break-all">
                            support@iconcomputer.in
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection