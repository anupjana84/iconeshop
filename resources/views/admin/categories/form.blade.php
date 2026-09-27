@extends('layouts.main')
@push('page_title')
    <title>Ladger</title>
@endpush
@section('content_page')
<div>
    <div class="max-w-lg mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-semibold mb-4">
            {{ $category->exists ? 'Edit Category' : 'Create Category' }}
        </h2>
    
        <form action="{{ $category->exists ? route('category.update', $category) : route('category.store') }}"
              method="POST" enctype="multipart/form-data">
    
            @csrf
            @if($category->exists)
                @method('PUT')
            @endif
    
            {{-- Name --}}
            <div class="mb-3">
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" id="name"
                       class="mt-1 p-2 border rounded w-full"
                       value="{{ old('name', $category->name) }}">
                @error('name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>
    
            {{-- HSN Code --}}
            <div class="mb-3">
                <label for="hsn_code" class="block text-sm font-medium text-gray-700">HSN Code</label>
                <input type="text" name="hsn_code" id="hsn_code"
                       class="mt-1 p-2 border rounded w-full"
                       value="{{ old('hsn_code', $category->hsn_code) }}">
                @error('hsn_code') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>
    
            {{-- GST --}}
            <div class="mb-3">
                <label for="gst" class="block text-sm font-medium text-gray-700">GST (%)</label>
                <input type="number" name="gst" id="gst"
                       class="mt-1 p-2 border rounded w-full"
                       value="{{ old('gst', $category->gst) }}">
                @error('gst') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>
    
            {{-- Image --}}
            <div class="mb-3">
                <label for="image" class="block text-sm font-medium text-gray-700">Image</label>
                <input type="file" name="image" id="image" accept="image/*" class="mt-1 p-2 border rounded w-full">
                @if($category->image)
                    <img src="{{ $category->image }}" alt="Image" class="mt-2 w-24 h-24 object-cover border">
                @endif
                @error('image') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>
    
            {{-- Active --}}
            <div class="mb-3">
                <label class="block text-sm font-medium text-gray-700">Status</label>
                <select name="active" class="mt-1 p-2 border rounded w-full">
                    <option value="1" {{ old('active', $category->active) == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('active', $category->active) == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('active') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>
    
            {{-- Submit --}}
            <div class="text-right">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                    {{ $category->exists ? 'Update' : 'Create' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection