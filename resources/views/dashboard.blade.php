@extends('layouts.main')
@push('page_title')
    <title>Dashboard</title>
@endpush
@section('content_page')
    <h1 class="font-medium mb-5">Welcome to Dashboard</h1>
    <span class="bg-red-300 px-5 py-2 rounded-sm m-5 text-red-600">
        <form action="{{ route('logout') }}" method="POST" class="inline">
            @csrf
            <button type="submit">Logout</button>
        </form>
    </span>
    <hr class="m-3">

    <div>
        Total stock amount : <strong>{{ number_format($totalStockAmount, 2) }}</strong>
    </div>
@endsection
