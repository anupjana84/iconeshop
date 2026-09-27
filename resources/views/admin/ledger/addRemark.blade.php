@extends('layouts.main')
@push('page_title')
    <title>Add Remark</title>
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

        <form action="{{ $url }}" method="POST" class="card p-8 shadow-sm border-blue-400 border-2 rounded-lg" style="max-width: 500px;">
            @csrf

            {{-- Amount Input --}}
            <div class="mb-3">
                <label class="form-label">Remark</label>
                <input type="text" name="remark" class="form-control bg-white rounded-sm border-2 p-2 @error('remark') is-invalid @enderror"
                    placeholder="Enter remark" value="{{ old('remark') }}">
                @error('remark')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Submit Button --}}
            <button type="submit" class="px-10 py-2 bg-green-600 rounded-sm text-white font-semibold cursor-pointer">Save</button>
        </form>
    </div>

@endsection
