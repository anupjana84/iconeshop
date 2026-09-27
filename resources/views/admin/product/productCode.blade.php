@extends('layouts.main')
@push('page_title')
    <title>Product Code</title>
@endpush
@section('content_page')
    <div>
        <div class="max-w-7xl mx-auto mb-2">
            <form id="filterForm" action="{{ route('product.code') }}" method="GET"
                class="grid grid-cols-1 md:grid-cols-5 gap-4 bg-gradient-to-r from-blue-50 to-indigo-100 p-5 rounded-2xl shadow-lg">

                <!-- Category -->
                <div>
                    <select id="category" name="category_id"
                        class="bg-white w-full border border-gray-500 rounded-lg px-3 py-2 focus:outline-gray-400 focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Category</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ isset($category_id) && $category_id == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Brand -->
                <div>
                    <select id="brand" name="brand_id"
                        class="bg-white w-full border border-gray-500 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Brand</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}"
                                {{ isset($brand_id) && $brand_id == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Model -->
                <div>
                    <select id="model" name="model"
                        class="bg-white w-full border border-gray-500 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select Model</option>
                        @if (isset($model) && $model)
                            <option value="{{ $model }}" selected>{{ $model }}</option>
                        @endif
                    </select>
                </div>

                <!-- Search Type (hidden by default) -->
                <div id="searchTypeWrapper" class="{{ isset($category_id) || isset($brand_id) ? '' : 'hidden' }}">
                    <select id="searchType" name="search_type"
                        class="bg-white w-full border border-gray-500 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Visibility Type</option>
                        <option value="barcode" {{ isset($search_type) && $search_type == 'barcode' ? 'selected' : '' }}>
                            Barcode</option>
                        <option value="record" {{ isset($search_type) && $search_type == 'record' ? 'selected' : '' }}>
                            Record List</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex flex-row justify-center gap-2">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2 rounded-lg transition-all">
                        Search
                    </button>
                    <button type="button" id="resetBtn"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold px-6 py-2 rounded-lg transition-all">
                        Reset
                    </button>
                </div>
            </form>
        </div>
        {{-- ////////////////////////////          --}}
        <div class="flex  items-center gap-4 mb-4 mt-2">


            <!-- Search Form -->
            <form id="search-form" class="flex flex-1 items-center space-x-2" method="GET">
                <!-- Search Input (fills remaining space) -->
                <input type="text" id="invoice_id" name="search" placeholder="Search using Product code"
                    class="p-2 border rounded flex-1 bg-white"
                    @isset($search)
                        value="{{ $search }}"
                    @endisset>

                <!-- Search Button -->
                <button type="submit"
                    class="text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 px-4 py-2 rounded">
                    Search
                </button>

                <!-- Reset Button -->
                <a href="{{ route('product.list') }}"
                    class="text-gray-900 bg-gradient-to-r from-lime-200 via-lime-400 to-lime-500 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-lime-300 dark:focus:ring-lime-800 px-4 py-2 rounded">
                    Reset
                </a>
            </form>
        </div>
        <div>
            
<div class="bg-white rounded shadow mb-2 border overflow-hidden">

    <!-- HEADER -->
    <div class="flex justify-between items-center bg-red-500 px-2 py-1">

        <h4 class="text-white">
            ⚠️Danger/warning
        </h4>

<button type="button"
    id="togglePricePanel"

    class="bg-white text-red-500 px-3 py-1 rounded hover:bg-gray-200 transition-all duration-300 flex items-center gap-2">

    <i class="fa-solid fa-maximize"></i>

    Maximize

</button>

    </div>

    <!-- BODY -->
    <div id="pricePanelBody" class="p-4 hidden">

        <div class="grid grid-cols-1 md:grid-cols-5 gap-6">

            <!-- ACTION -->
            <div>

                <label class="font-semibold">
                    Action
                </label>

                <select id="change_type"
                    class="w-full border rounded p-2">

                    <option value="increase">
                        Increase %
                    </option>

                    <option value="decrease">
                        Decrease %
                    </option>

                </select>

            </div>

            <!-- SALE -->
            <div>

                <label class="font-semibold">
                    Sale %
                </label>

                <input type="number"
                    id="sale_change_percent"
                    step="0.01"
                    class="w-full border rounded p-2"
                    placeholder="2">

            </div>

            <!-- ONLINE -->
            <div>

                <label class="font-semibold">
                    Online %
                </label>

                <input type="number"
                    id="online_change_percent"
                    step="0.01"
                    class="w-full border rounded p-2"
                    placeholder="2">

            </div>

            <!-- CATEGORY -->
            <div>

                <label class="font-semibold">
                    Category
                </label>

                <select id="bulk_category"
                    class="w-full border rounded p-2">

                    <option value="">
                        All
                    </option>

                    @foreach($categories as $cat)

                        <option value="{{ $cat->id }}">
                            {{ $cat->name }}
                        </option>

                    @endforeach

                </select>

            </div>
            
            <!-- BRAND -->
  <div>

    <label class="font-semibold">
        Brand
    </label>

    <select id="bulk_brand"
        class="w-full border rounded p-2">

        <option value="">
            All
        </option>

        @foreach($brands as $brand)

            <option value="{{ $brand->id }}">
                {{ $brand->name }}
            </option>

        @endforeach

    </select>

  </div>

            <!-- BUTTON -->
            <div class="flex items-end">

                <button type="button"
                    id="updatePrices"

                    class="bg-red-500 hover:bg-red-500 text-white px-4 py-2 rounded w-full h-[42px]">

                    Update Prices

                </button>

            </div>

        </div>

    </div>

</div>            
            
            <h2 class="text-lg font-semibold mb-2">All Product List </h2> <!-- Subheading for Table -->
            <div class="bg-white p-4 rounded shadow overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-800 text-white">
                            <th class="py-2 px-4 border">Sl</th>
                            <th class="py-2 px-4 border">Code</th>
                            <th class="py-2 px-4 border">Purchase Price (without GST)</th>
                            <th class="py-2 px-4 border">Purchase Price (with GST)</th>
                            <th class="py-2 px-4 border">Sale Price(Rs)</th>
                            <th class="py-2 px-4 border">Online rate(Rs)</th>
                            <th class="py-2 px-4 border">Delivery Charges(Rs)</th>
                            <th class="py-2 px-4 border">Dealer Point(Rs)</th>
                            <th class="py-2 px-4 border">salesmen Point(Rs)</th>
                            <th class="py-2 px-4 border">Stock</th>
                            <th class="py-2 px-4 border">Details</th>
                            {{-- <th class="py-2 px-4 border" colspan="2">Action</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 1;
                        @endphp
                        @foreach ($products as $key => $item)
                            <tr class="border">
                                <td class="py-2 px-4 border border-gray-300">{{ $i }}</td>
                                <td class="py-2 px-2 bg-gray-200 border border-gray-300">
                                    @isset($item->code)
                                        <div class="flex flex-col justify-center items-center ">
                                            <div class="text-center">{{ $item->category->name }}-{{ $item->brand->name }}
                                            </div>
                                            <div class="text-center">{{ $item->model }}</div>
                                            <div class="flex flex-col justify-center items-center px-3 pt-2 bg-white">
                                                <img src="data:image/png;base64,{{ DNS1D::getBarcodePNG($item->code, 'C128', 3, 100, [0, 0, 0], false) }}"
                                                    alt="barcode"
                                                    style="display:block; margin:auto; background:white; padding:10px;" />
                                                <p>{{ $item->code }}</p>
                                            </div>
                                        </div>
                                    @endisset
                                </td>
                                <td class="py-2 px-4 border border-gray-300 text-right">
                                    @isset($item->purchase_price)
                                        {{ $item->purchase_price }}
                                    @endisset
                                </td>
                                <td class="py-2 px-4 border border-gray-300 text-right">
                                    {{ $item->purchase_withgst }}
                                </td>
                                <td class="py-2 px-4 border border-gray-300 text-center">
                                    @isset($item->sale_rate)
                                        {{ $item->sale_price }}<br>
                                        ({{ $item->sale_rate }}%)
                                    @endisset
                                </td>

                                <td class="py-2 px-4 border border-gray-300 text-center">
                                    @if (isset($item->online_price))
                                        {{ $item->online_price }}<br>
                                        ({{ $item->online_rate }}%)
                                    @else
                                        <p class="text-red-600">Not set</p>
                                    @endif
                                </td>
                                <td class="py-2 px-4 border border-gray-300 text-center">
                                    @if (isset($item->delivery_charges))
                                        {{ $item->delivery_charges_amount }}<br>
                                        ({{ $item->delivery_charges }}%)
                                    @else
                                        <p class="text-green-600">Free Delivery</p>
                                    @endif
                                </td>
                                <td class="py-2 px-4 border border-gray-300 text-center">
                                    {{ $item->point }}<br>
                                    @isset($item->dealer_point)
                                        ({{ $item->dealer_point }}%)
                                    @endisset
                                </td>
                                <td class="py-2 px-4 border border-gray-300 text-center">
                                    @if (isset($item->salesmen_point))
                                        {{ $item->salesmen_point_price }}<br>
                                        ({{ $item->salesmen_point }}%)
                                    @else
                                        <p class="text-red-600">Not set</p>
                                    @endif
                                </td>
                                <td class=" border border-gray-300 text-center">
                                    @if (isset($item->stock))
                                        {{ $item->stock }}
                                    @else
                                        <p class="text-red-600">Not set</p>
                                    @endif
                                </td>
                                <td class="border border-gray-300 text-center">
                                    <a id="action" href="{{ route('product.show', ['id' => $item->id]) }}"><button
                                            class="mt-1 bg-green-800 text-white px-3 py-1 rounded hover:bg-red-700"><i
                                                class="fa-solid fa-circle-info"></i></button></a>
                                </td>
                            </tr>
                            @php
                                $i++;
                            @endphp
                        @endforeach
                    </tbody>
                </table>
                @if ($i == 1)
                    <div class="text-red-600 text-center">No product found</div>
                @endif
            </div>
            <div class="py-4">
                {{ $products->links() }}
            </div>
        </div>
    </div>
    <div id="imagePreview" class="hidden fixed z-50 border bg-white shadow-lg rounded p-1">
        <img id="previewImg" src="" alt="Preview" class="w-60 h-60 object-contain">
    </div>
@endsection
@push('extra_js')
    <script>
        function showPreview(e, src) {
            const preview = document.getElementById('imagePreview');
            const img = document.getElementById('previewImg');
            img.src = src;
            preview.style.top = (e.pageY + 20) + 'px';
            preview.style.left = (e.pageX + 20) + 'px';
            preview.classList.remove('hidden');
        }

        function hidePreview() {
            document.getElementById('imagePreview').classList.add('hidden');
        }
    </script>

    {{-- Filter Script --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <script>
        $(document).ready(function() {

            function fetchData() {
                $.ajax({
                    url: "{{ route('get.dependent.data') }}",
                    type: "GET",
                    data: {
                        category_id: $('#category').val(),
                        brand_id: $('#brand').val(),
                    },
                    success: function(response) {
                        let categorySelect = $('#category');
                        let brandSelect = $('#brand');
                        let modelSelect = $('#model');

                        // Populate Category
                        if (!$('#category').val()) {
                            categorySelect.empty().append('<option value="">Select Category</option>');
                            $.each(response.categories, function(i, cat) {
                                categorySelect.append('<option value="' + cat.id + '">' + cat
                                    .name + '</option>');
                            });
                        }

                        // Populate Brand
                        if (!$('#brand').val()) {
                            brandSelect.empty().append('<option value="">Select Brand</option>');
                            $.each(response.brands, function(i, brand) {
                                brandSelect.append('<option value="' + brand.id + '">' + brand
                                    .name + '</option>');
                            });
                        }

                        // Populate Model
                        modelSelect.empty().append('<option value="">Select Model</option>');
                        $.each(response.models, function(i, model) {
                            modelSelect.append('<option value="' + model + '">' + model +
                                '</option>');
                        });

                        toggleSearchType();
                    }
                });
            }

            function toggleSearchType() {
                const hasCategory = $('#category').val();
                const hasBrand = $('#brand').val();

                if (hasCategory || hasBrand) {
                    $('#searchTypeWrapper').removeClass('hidden');
                } else {
                    $('#searchTypeWrapper').addClass('hidden');
                    $('#searchType').val('');
                }
            }

            // Initial load
            fetchData();

            // On change
            $('#category, #brand').change(fetchData);

            // Reset button
            $('#resetBtn').click(function() {
                $('#filterForm')[0].reset();
                $('#searchTypeWrapper').addClass('hidden');
                window.location = "{{ route('product.list') }}"; // reset to full product list
            });
        });
    </script>
    
    <script>

$(document).ready(function(){
    
        // MINIMIZE / MAXIMIZE

$('#togglePricePanel').click(function(){

    $('#pricePanelBody').slideToggle(300);

    if($('#pricePanelBody').hasClass('hidden')){

        $('#pricePanelBody').removeClass('hidden');

        $(this).html(`
            <i class="fa-solid fa-minimize"></i>
            Minimize
        `);

    }else{

        setTimeout(() => {

            $('#pricePanelBody').addClass('hidden');

        }, 300);

        $(this).html(`
            <i class="fa-solid fa-maximize"></i>
            Maximize
        `);
    }

});

    $('#updatePrices').click(function(){
        showConfirm('Update Prices', 'Are you sure you want to update product prices?', function(){
            $.ajax({

            url: "{{ route('bulk.price.update') }}",

            type: "POST",

            data: {

                _token:
                    "{{ csrf_token() }}",

                change_type:
                    $('#change_type').val(),

                sale_change_percent:
                    $('#sale_change_percent').val(),

                online_change_percent:
                    $('#online_change_percent').val(),

                category_id:
                    $('#bulk_category').val(),

                brand_id:
                    $('#bulk_brand').val(),

            },

            success:function(response){

                if(response.status){
                    showAlert('Success', response.message, 'success').then(function(){
                        location.reload();
                    });
                }else{
                    showAlert('Error', response.message, 'error');
                }
            }

        });
    });
});

</script>

@endpush
