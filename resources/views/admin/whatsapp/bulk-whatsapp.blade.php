@extends('layouts.main')
@push('page_title')
    <title>whatsapp</title>
@endpush
@section('content_page')
    <div class="max-w-xl mx-auto mt-10 p-6 bg-white rounded shadow">

        <h1 class="text-xl font-bold mb-4">Send Bulk WhatsApp Message</h1>
    
        @if(session('success'))
            <div class="p-3 bg-green-100 text-green-700 mb-4">{{ session('success') }}</div>
        @endif
    
        <form action="{{ route('bulk.whatsapp.send') }}" method="POST" enctype="multipart/form-data">
            @csrf
    
            <label class="block font-medium">Message</label>
            <textarea name="message" class="w-full border p-2 rounded mt-1" rows="3" required placeholder="Write your message"></textarea>
    
            <label class="block font-medium mt-4">Upload Image (optional)</label>
            <input type="file" name="image" class="w-full border p-2 rounded" accept="image/*">
    
            <button type="submit"
                    class="mt-5 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Send To All Customers
            </button>
        </form>
    
    </div>
@endsection
