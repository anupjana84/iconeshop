@extends('layouts.main')
@push('page_title')
    <title>Add Bank</title>
@endpush
@section('content_page')
    <div class="max-w-lg mx-auto bg-white p-6 rounded-xl shadow-md mt-6">
        <h2 class="text-2xl font-semibold mb-4 text-center">
            {{ $page_title }}
        </h2>

        <!-- Form -->
        <form action="{{ $url }}" method="POST" autocomplete="off">
            @csrf

            <!-- Brand Name Field -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Bank Name</label>
                <input type="text" id="name" name="name" value="{{ old('name', $bank->name ?? '') }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror"
                    placeholder="Enter bank name" @isset($bank)
                        readonly
                    @endisset>

                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            @isset($bank)
            <div class="mb-4">
                <label for="" class="block text-sm font-medium text-gray-700 mb-1">Current Balance</label>
                <input type="number" id="" name="" value="{{  $bank->amount ?? '' }}" step="0.01" readonly
                    class="w-full border bg-gray-50 border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('brand') border-red-500 @enderror"
                    placeholder="Enter amount">
                    
                    
                </div>
                @endisset
            <div class="mb-4">
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">{{$amountText}}</label>
                <input type="number" id="amount" name="amount" value="{{ old('amount') }}" step="0.01"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('amount') border-red-500 @enderror"
                    placeholder="Enter amount">

                @error('amount')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            @isset($remark)
            <div class="mb-4">
                <label for="remark" class="block text-sm font-medium text-gray-700 mb-1">Remark</label>
                <input type="text" id="remark" name="remark" value="{{ old('amount') }}" step="0.01"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 @error('remark') border-red-500 @enderror"
                    placeholder="Enter remark">

                @error('remark')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            @endisset


            <!-- Submit Button -->
            <div class="text-center mt-6">
                <button type="submit"
                    class="bg-blue-600 text-white font-medium px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    {{ isset($bank) ? 'Update ' : 'Add ' }}
                </button>
            </div>
        </form>
    </div>
@endsection
