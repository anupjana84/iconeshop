@extends('layouts.main')
@push('page_title')
    <title>Ladger</title>
@endpush
@section('content_page')
    <div class="max-w-lg mx-auto bg-white p-6 rounded-xl shadow-md mt-6">
        <h2 class="text-2xl font-semibold mb-4 text-center">
            {{ isset($brand) ? 'Edit Brand' : 'Add New Brand' }}
        </h2>

        <!-- Form -->
        <form action="{{ $url }}" method="POST" autocomplete="off">
            @csrf

            <!-- Brand Name Field -->
            <div class="mb-4">
                <label for="brand" class="block text-sm font-medium text-gray-700 mb-1">Brand Name</label>
                <input type="text" id="brand" name="brand" value="{{ old('brand', $brand->name ?? '') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('brand') border-red-500 @enderror"
                    placeholder="Enter brand name">

                @error('brand')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="text-center mt-6">
                <button type="submit"
                    class="bg-blue-600 text-white font-medium px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    {{ isset($brand) ? 'Update Brand' : 'Add Brand' }}
                </button>
            </div>
        </form>
    </div>
@endsection
<!-- aaaaa -->