@extends('layouts.main')
@push('page_title')
    <title>Helpline</title>
@endpush
@section('content_page')
<h2>Helpline Numbers</h2>
<a href="{{ route('tollfree.create') }}" class="btn btn-primary">Add New</a>

<table class="table table-bordered mt-3">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Phones</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        @foreach($data as $d)
        <tr>
            <td>{{ $d->id }}</td>
            <td>{{ $d->name }}</td>
            <td>
                @foreach($d->phones as $p)
                    <span class="badge bg-info">{{ $p }}</span>
                @endforeach
            </td>
            <td>
                <a href="{{ route('tollfree.edit', $d->id) }}" class="btn btn-warning btn-sm">Edit</a>

                <form action="{{ route('tollfree.destroy', $d->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
