@extends('layouts.main')
@push('page_title')
    <title>Helpline</title>
@endpush
@section('content_page')
<h2>Add Helpline Number</h2>

<form method="POST" action="{{ route('tollfree.store') }}">
    @csrf

    <label>Name</label>
    <input type="text" name="name" class="form-control">

    <label>Phone Numbers</label>

    <div id="phonesWrapper">
        <input type="text" name="phones[]" class="form-control mb-2">
    </div>

    <button type="button" id="addPhone" class="btn btn-secondary mt-2">Add More</button>

    <button type="submit" class="btn btn-success mt-3">Save</button>
</form>

<script>
document.getElementById('addPhone').onclick = function() {
    let wrapper = document.getElementById('phonesWrapper');
    wrapper.insertAdjacentHTML('beforeend', '<input type="text" name="phones[]" class="form-control mb-2">');
};
</script>

@endsection
