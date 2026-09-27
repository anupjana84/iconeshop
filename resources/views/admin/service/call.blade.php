@extends('layouts.main')
@push('page_title')
    <title>{{ isset($status) ? 'Edit Service Status' : 'Add Service Status' }}</title>

    @section('content_page')
        @php
            $isEdit = isset($status);
        @endphp
        <div class="max-w-xl mx-auto bg-white p-5 rounded shadow">
            <h2 class="text-xl font-bold mb-4">{{ $isEdit ? 'Update Service Status' : 'Add Service Status' }}</h2>

            <form action="{{ $url }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="service_call_id" value="{{ $id }}">

                {{-- Image Remark --}}
                <div class="mb-3">
                    <label class="block font-semibold">Image</label>
                    <input type="file" name="image" class="w-full border p-2 rounded bg-gray-200" accept="image/*"
                        ></input>
                        @isset($status->image)
                            
                        <div>
                            <img src="{{ asset('storage/'.$status->image) }}" width="70" height="70">
                        </div>
                        @endisset

                    @error('image')
                        <div class="text-red-600 text-sm">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Admin Remark --}}
                <div class="mb-3">
                    <label class="block font-semibold">Admin Remark</label>
                    <textarea name="admin_remark" class="w-full border p-2 rounded" rows="3"
                        placeholder="Write down your remark here ....">{{ old('admin_remark', $status->admin_remark ?? '') }}</textarea>
                    @error('admin_remark')
                        <div class="text-red-600 text-sm">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Case ID --}}
                <div class="mb-3">
                    <label class="block font-semibold">Case ID</label>
                    <input type="text" name="case_id" class="w-full border p-2 rounded" placeholder="Enter Case ID"
                        value="{{ old('case_id', $status->case_id ?? '') }}">
                    @error('case_id')
                        <div class="text-red-600 text-sm">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="flex gap-3 w-full">
                    <div class="mb-3 w-1/2">
                        <label class="block font-semibold">Status</label>
                        <select name="status" class="w-full border p-2 rounded">
                            <option value="Pending" {{ $serviceCall->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Completed" {{ $serviceCall->status == 'Completed' ? 'selected' : '' }}>Completed
                            </option>
                            <option value="Canceled" {{ $serviceCall->status == 'Canceled' ? 'selected' : '' }}>Canceled
                            </option>
                        </select>
                        @error('status')
                            <div class="text-red-600 text-sm">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class=" w-1/2">
                        <label class="block font-semibold">Call Date</label>
                        <input type="date" name="call_date" class="w-full border p-2 rounded"
                            value="{{ old('call_date', isset($serviceCall->call_date) ? date('Y-m-d', strtotime($serviceCall->call_date)) : '') }}">
                        @error('call_date')
                            <div class="text-red-600 text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                </div>


                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded">{{ $isEdit ? 'Update Status' : 'Save Status' }}</button>
            </form>
        </div>
    @endsection
    @push('extra_js')
    @endpush
