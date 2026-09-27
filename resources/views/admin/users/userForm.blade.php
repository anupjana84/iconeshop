@extends('layouts.main')
@push('page_title')
    <title> {{ isset($user) ? 'Update User' : 'Create User' }}</title>
@endpush
@section('content_page')
    
    <div>
        {{-- resources/views/users/form.blade.php --}}
        <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-md">
            {{-- Show validation errors --}}
            {{-- @if ($errors->any())
                <div class="mb-4 p-3 bg-red-100 text-red-600 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif --}}

            <form method="POST" action="{{ $url }}">
                @csrf
                @if (isset($user))
                    @method('PUT')
                @endif

                {{-- Name --}}
                <div class="mb-4">
                    <label for="name" class="block font-medium mb-1">User Name</label>
                    <input type="text" name="name" id="name"
                        class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400
                   @error('name') border-red-500 @enderror"
                        value="{{ old('name', $user->name ?? '') }}">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone No --}}
                <div class="mb-4">
                    <label for="phone" class="block font-medium mb-1">Phone Number</label>
                    <input type="text" name="phone" id="phone"
                        class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400
                   @error('phone') border-red-500 @enderror"
                        value="{{ old('phone', $user->phone ?? '') }}" placeholder="Enter mobile number">
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- WhatsApp No --}}
                <div class="mb-4">
                    <label for="wpnumber" class="block font-medium mb-1">WhatsApp Number</label>
                    <input type="text" name="wpnumber" id="whatsapp"
                        class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400
                   @error('wpnumber') border-red-500 @enderror"
                        value="{{ old('wpnumber', $user->wpnumber ?? '') }}" placeholder="Enter whatsapp number">
                    @error('wpnumber')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- User Role --}}
                <div class="mb-4">
                    <label for="role" class="block font-medium mb-1">User Role</label>
                    <select name="role" id="role"
                        class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400
                    @error('role') border-red-500 @enderror">
                        <option value="">-- Select Role --</option>
                        <option value="manager" {{ old('role', $user->role ?? '') == 'manager' ? 'selected' : '' }}>Manager
                        </option>
                        <option value="subdealer" {{ old('role', $user->role ?? '') == 'subdealer' ? 'selected' : '' }}>Sub Dealer
                        </option>
                        <option value="seller" {{ old('role', $user->role ?? '') == 'seller' ? 'selected' : '' }}>Seller
                        </option>
                        <option value="salesman" {{ old('role', $user->role ?? '') == 'salesman' ? 'selected' : '' }}>Salesman
                        </option>
                    </select>
                    @error('role')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                @if(!isset($user))
                {{-- Password (only required on create) --}}
                <div class="mb-4">
                    <label for="password" class="block font-medium mb-1">Password</label>
                    <input type="password" name="password" id="password"
                        class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400
                   @error('password') border-red-500 @enderror"
                        {{ isset($user) ? '' : 'required' }}>
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                    @if (isset($user))
                        <p class="text-gray-500 text-sm mt-1">Leave blank if you don't want to change password</p>
                    @endif
                </div>
                @endif


                {{-- Submit Button --}}
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded hover:bg-blue-700 transition">
                        {{ isset($user) ? 'Update User' : 'Create User' }}
                    </button>
                </div>
            </form>
        </div>

    </div>
@endsection
@push('extra_style')
@endpush
@push('extra_js')
@endpush
