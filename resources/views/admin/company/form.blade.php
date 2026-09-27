@extends('layouts.main')

@push('page_title')
<title>{{ $page_title }}</title>
@endpush

@section('content_page')
<div class="container mx-auto p-4 max-w-2xl">
    <h2 class="text-2xl font-semibold mb-4">{{ $page_title }}</h2>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
            <strong>Error!</strong>
            <ul class="list-disc ml-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Success Message --}}
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" 
        action="{{ isset($company) ? route('companies.update', $company->id) : route('companies.store') }}" 
        class="space-y-4 bg-white p-6 rounded shadow">
        @csrf

        {{-- Company Name --}}
        <div>
            <label for="name" class="block font-medium text-gray-700">Company Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name', $company->name ?? '') }}"
                class="mt-1 w-full p-2 border rounded bg-white" required>
        </div>

        {{-- Address --}}
        <div>
            <label for="address" class="block font-medium text-gray-700">Address</label>
            <input type="text" name="address" id="address" value="{{ old('address', $company->address ?? '') }}"
                class="mt-1 w-full p-2 border rounded bg-white">
        </div>

        {{-- Phone --}}
        <div>
            <label for="phone" class="block font-medium text-gray-700">Phone</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone', $company->phone ?? '') }}"
                class="mt-1 w-full p-2 border rounded bg-white">
        </div>

        {{-- GST Number --}}
        <div>
            <label for="gst_number" class="block font-medium text-gray-700">GST Number</label>
            <input type="text" name="gst_number" id="gst_number" value="{{ old('gst_number', $company->gst_number ?? '') }}"
                class="mt-1 w-full p-2 border rounded bg-white">
        </div>

        {{-- Buttons --}}
        <div class="flex justify-between mt-6">
            <a href="{{ route('companies.list') }}" 
               class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-gray-800">
                Back
            </a>

            <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                {{ isset($company) ? 'Update Company' : 'Create Company' }}
            </button>
        </div>
    </form>
</div>
@endsection
