@extends('layouts.main')
@push('page_title')
    <title>User List</title>
@endpush
@section('content_page')
    <!-- Page Heading with Round Logo -->
    
        {{-- resources/views/users/show.blade.php --}}
<div class="max-w-2xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden border">
    <div class="p-6 relative">
        {{-- Edit + Delete buttons --}}
        <div class="absolute top-4 right-4 flex space-x-2">
            <a href="{{ route('user.edit', $user->id) }}"
               class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-md text-sm shadow">
                Edit
            </a>
            <form action="{{ route('user.delete', $user->id) }}" method="POST" onsubmit="return confirmDelete(event, 'Delete User', 'Are you sure you want to delete this user?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-md text-sm shadow">
                    Delete
                </button>
            </form>
        </div>

        {{-- User Info --}}
        <div class="text-center">
            <div class="w-20 h-20 mx-auto rounded-full bg-gray-200 flex items-center justify-center text-2xl font-bold text-gray-600">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <h2 class="mt-4 text-xl font-semibold text-gray-800">{{ $user->name }}</h2>
            <p class="text-gray-500">{{ $user->role }}</p>
        </div>

        <div class="mt-6 space-y-4">
            <div class="flex justify-between border-b pb-2">
                <span class="text-gray-600 font-medium">Phone:</span>
                <span class="text-gray-800">{{ $user->phone }}</span>
            </div>
           
            <div class="flex justify-between">
                <span class="text-gray-600 font-medium">Role:</span>
                <span class="text-gray-800 capitalize">{{ $user->role }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600 font-medium">Status:</span>
                <div>

                    <span class="text-gray-800 capitalize">{{ $user->status }}</span>
                    <a href="{{route('user.change.status',$user->id)}}"
                        class="px-2 py-1 bg-amber-300 rounded-sm cursor-pointer text-red-600">Change</a>
                    </div>
            </div>
            <div class="flex justify-between bg-red-100 p-3 rounded-xl items-center">
                <span class="text-gray-600 font-medium">Change Password:</span>
                <div>
                    <form action="{{route('user.change.password',$user->id)}}" method="POST">
                        @csrf
                        <input type="text" name="password" class="px-2 py-1 bg-white border-1 border-black" placeholder="New password....">
                        <input type="submit" value="Update" class="text-red-600 px-3 py-1 bg-amber-300 border-1 border-red-600 cursor-pointer">
                    </form>
                    @error('password')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
            </div>
        </div>
    </div>
</div>

    </div>
@endsection
@push('extra_style')
@endpush
@push('extra_js')
@endpush