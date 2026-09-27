@extends('layouts.main')
@push('page_title')
    <title>EMI Pay</title>
@endpush
@section('content_page')
<div class="max-w-5xl mx-auto p-6">

    <h2 class="text-2xl font-semibold mb-6">{{ $page_title }}</h2>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        
        <div class="bg-white p-4 rounded-lg shadow">
            <p class="text-sm text-gray-500">Name</p>
            <p class="text-lg font-semibold text-gray-800">
                {{ $finance->customer->name ?? '-' }}
            </p>
        </div>

        <div class="bg-white p-4 rounded-lg shadow">
            <p class="text-sm text-gray-500">Phone No</p>
            <p class="text-lg font-semibold text-gray-800">
                {{ $finance->customer->phone ?? '-' }}
            </p>
        </div>

        <div class="bg-white p-4 rounded-lg shadow">
            <p class="text-sm text-gray-500">Down Payment</p>
            <p class="text-lg font-semibold text-gray-800">
                ₹ {{ number_format($finance->down_payment ?? 0, 2) }}
            </p>
        </div>

        <div class="bg-white p-4 rounded-lg shadow">
            <p class="text-sm text-gray-500">Pay Amount</p>
            <p class="text-lg font-semibold text-gray-800">
                ₹ {{ number_format($finance->amount ?? 0, 2) }}
            </p>
        </div>

    </div>

    {{-- Payment Form --}}
    <form action="{{ route('emi.clear') }}" method="POST" class="bg-white p-6 rounded-lg shadow">
        @csrf
        <input type="hidden" name="emi_id" value="{{$finance->id}}">
        <input type="hidden" name="amount" value="{{$finance->amount}}">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Date --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Payment Date
                </label>
                <input type="date" name="clear_date"
                    class="w-full rounded-md border-black focus:border-indigo-500 focus:ring-indigo-500 p-2"
                    required>
            </div>

            {{-- Bank Select --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Select Bank
                </label>
                <select name="bank_id"
                    class="w-full rounded-md border-black focus:border-indigo-500 focus:ring-indigo-500 p-2" 
                    required>
                    <option value="">-- Select Bank --</option>
                    @foreach($banks as $bank)
                        <option value="{{ $bank->id }}">
                            {{ $bank->name }}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

        {{-- Submit --}}
        <div class="mt-6 text-right">
            <button type="submit"
                class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-indigo-700 transition">
                Pay EMI
            </button>
        </div>
    </form>

</div>
@endsection
