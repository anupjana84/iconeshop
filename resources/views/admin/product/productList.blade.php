@extends('layouts.main')
@push('page_title')
    <title>Product</title>
@endpush
@section('content_page')
    <div>
        @if (!isset($empty_stocks))
            <div class="max-w-7xl mx-auto mb-2">
                <form id="filterForm" action="{{ route('product.list') }}" method="GET"
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
                            <option value="barcode"
                                {{ isset($search_type) && $search_type == 'barcode' ? 'selected' : '' }}>
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
        @endif

        <div class="flex items-center space-x-2">
            <!-- Search Form -->
            <form id="search-form" class="flex flex-1 items-center space-x-2" method="GET">
                <input type="text" id="invoice_id" name="search" placeholder="Search using Product code"
                    class="p-2 border rounded flex-1 bg-white"
                    @isset($search)
                        value="{{ $search }}"
                    @endisset>

                <button type="submit"
                    class="text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 px-4 py-2 rounded">
                    Search
                </button>

                <a href="{{ route('product.list') }}"
                    class="text-gray-900 bg-gradient-to-r from-lime-200 via-lime-400 to-lime-500 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-lime-300 dark:focus:ring-lime-800 px-4 py-2 rounded">
                    Reset
                </a>
            </form>
        </div>

        @if (request('search_type') != 'barcode')
            <div>
                <div class="flex justify-between w-full px-5">
                    <h2 class="text-lg font-semibold mb-2">All Product List</h2>
                    @isset($totalStock)
                    <h2 class="text-lg font-semibold mb-2">Total Stock: {{ $totalStock }}</h2>
                    @endisset
                </div>
                <div class="bg-white p-4 rounded shadow overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-800 text-white">
                                <th class="py-2 px-4 border">Sl</th>
                                <th class="py-2 px-4 border">Image</th>
                                <th class="py-2 px-4 border">Display</th>
                                <th class="py-2 px-4 border">Name</th>
                                <th class="py-2 px-4 border">Model</th>
                                <th class="py-2 px-4 border">Description</th>
                                <th class="py-2 px-4 border">Purchase Price(Rs)(without gst)</th>
                                <th class="py-2 px-4 border">Sale Price(Rs)(with gst)</th>
                                <th class="py-2 px-4 border">Special Offer</th>
                                <th class="py-2 px-4 border">Status</th>
                                <th class="py-2 px-4 border">Stock</th>
                                <th class="py-2 px-4 border" colspan="3">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $i = 1;
                            @endphp
                            @foreach ($products as $key => $item)
                                <tr class="border">
                                    <td class="py-2 px-4 border">{{ $i }}</td>
                                    <td class="py-2 px-4 border">
                                        @isset($item->details->thumbnail_image)
                                            <img src="{{ $item->details->thumbnail_image }}" alt="Product Thumbnail"
                                                class="h-[60px] w-[50px] object-cover cursor-pointer"
                                                onmouseover="showPreview(event, '{{ $item->details->thumbnail_image }}')"
                                                onmouseout="hidePreview()">
                                        @endisset
                                    </td>
                                    <td class="py-2 px-4 border">
                                        @isset($item->details->display)
                                            {{ $item->details->display }}
                                        @endisset
                                    </td>
                                    <td class="py-2 px-4 border">{{ $item->brand->name }}-{{ $item->category->name }}</td>
                                    <td class="py-2 px-4 border">{{ $item->model }}</td>
                                    <td class="py-2 px-4 border max-w-xs" title="{{ strip_tags($item->details->description ?? '') }}">
                                        {{ \Illuminate\Support\Str::limit(strip_tags($item->details->description ?? ''), 100) ?: '-' }}
                                    </td>
                                    <td class="py-2 px-4 border text-right">{{ $item->purchase_price }}</td>
                                    <td class="py-2 px-4 border text-right">{{ $item->sale_price }}</td>

                                     <!-- Special Offer Modal Trigger Button & Status -->
                                     <td class="py-2 px-4 border text-center">
                                         @php
                                             $so = $item->specialOffer;
                                             $isActive = $so && $so->isCurrentlyActive();
                                         @endphp
                                         <button type="button" 
                                                 data-id="{{ $item->id }}"
                                                 data-name="{{ ($item->brand->name ?? '') . ' ' . $item->model }}"
                                                 data-price="{{ (float)($item->online_price ?: $item->sale_price ?: 0) }}"
                                                 onclick="openSpecialOfferModalFromBtn(this)"
                                                 class="px-3 py-1.5 rounded-lg text-xs font-semibold shadow-sm transition-all duration-200 inline-flex items-center justify-center gap-1.5 {{ $isActive ? 'bg-gradient-to-r from-amber-500 to-yellow-600 text-white hover:from-amber-600 hover:to-yellow-700 ring-2 ring-yellow-300' : 'bg-gray-100 text-gray-700 border border-gray-300 hover:bg-gray-200' }}">
                                             <span>⚡</span>
                                             <span>{{ $isActive ? 'Edit Offer' : 'Special Offer' }}</span>
                                         </button>

                                         @if($so)
                                             <div class="mt-1.5 text-xs font-medium">
                                                 @if($so->offer_type === 'flat')
                                                     <span class="inline-block bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-bold text-[11px]">₹{{ number_format($so->flat_discount, 0) }} FLAT OFF</span>
                                                 @elseif($so->offer_type === 'percentage')
                                                     <span class="inline-block bg-red-100 text-red-700 px-2 py-0.5 rounded-full font-bold text-[11px]">-{{ $so->percentage_discount }}% OFF</span>
                                                 @elseif($so->offer_type === 'free_product')
                                                     <span class="inline-block bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-[11px]">🎁 Free {{ $so->freeProduct ? (($so->freeProduct->brand->name ?? '') . ' ' . $so->freeProduct->model) : 'Gift' }}</span>
                                                 @endif

                                                 <div class="text-[10px] text-gray-500 mt-1 flex items-center justify-center gap-1">
                                                     @if($isActive)
                                                         <span class="inline-block w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                                                         <span class="text-green-700 font-semibold">Ends: {{ \Carbon\Carbon::parse($so->end_date)->format('d M, h:i A') }}</span>
                                                     @else
                                                         <span class="inline-block w-2 h-2 rounded-full bg-red-400"></span>
                                                         <span class="text-red-500 font-medium">Expired / Inactive</span>
                                                     @endif
                                                 </div>
                                             </div>
                                         @endif
                                     </td>

                                    <!-- Status Column with Popup -->
                                    <td class="py-2 px-4 border text-center">
                                        <a href="javascript:void(0)" onclick="confirmStatusChange('{{ route('product.change.status', $item->id) }}', '{{ $item->id }}', '{{ $item->details->status ?? 0 }}')">
                                            @isset($item->details->status)
                                                @if ($item->details->status == 0)
                                                    <span class="bg-red-200 text-red-800 px-2 py-1 rounded-full text-sm">Inactive</span>
                                                @elseif($item->details->status == 1)
                                                    <span class="bg-green-200 text-green-800 px-2 py-1 rounded-full text-sm">Active</span>
                                                @else
                                                    <span class="bg-gray-200 text-gray-800 px-2 py-1 rounded-full text-sm">Unknown</span>
                                                @endif
                                            @endisset
                                        </a>
                                    </td>
                                    <td class="py-2 px-4 border text-center">
                                        {{ $item->stock }}
                                    </td>
                                    <td>
                                        <a id="action" href="{{ route('product.show', ['id' => $item->id]) }}">
                                            <button class="bg-green-800 text-white px-3 py-1 ml-2 rounded hover:bg-red-400">
                                                <i class="fa-solid fa-circle-info"></i>
                                            </button>
                                        </a>
                                    </td>
                                    <td>
                                        <a id="action" href="{{ route('product.edit', $item->id) }}">
                                            <button class="bg-green-600 text-white px-3 py-1 mx-2 rounded hover:bg-green-700">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                        </a>
                                    </td>
                                    <td>
                                        <form action="{{ route('product.delete', $item->id) }}" method="POST"
                                            id="deleteForm{{ $item->id }}" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-red-600 text-white px-3 py-1 mr-2 rounded hover:bg-red-700">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
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
        @endif

        {{-- For purchase company details --}}
        @if (!isset($empty_stocks))
            @if (isset($search))
                <div>
                    <h2 class="text-lg font-semibold mb-2">Purchase from</h2>
                    <div class="bg-white p-4 rounded shadow overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-gray-800 text-white">
                                    <th class="py-2 px-4 border">Sl</th>
                                    <th class="py-2 px-4 border">Company Name</th>
                                    <th class="py-2 px-4 border">Invoice id</th>
                                    <th class="py-2 px-4 border">Date</th>
                                    <th class="py-2 px-4 border">Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $i = 1;
                                @endphp
                                @foreach ($products as $key => $item)
                                    @isset($item->purchase->id)
                                        <tr class="border">
                                            <td class="py-2 px-4 border">{{ $i }}</td>
                                            <td class="py-2 px-4 border">
                                                @isset($item->purchase->company->name)
                                                    {{ $item->purchase->company->name }}
                                                @endisset
                                            </td>
                                            <td class="py-2 px-4 border">
                                                @isset($item->purchase->purchase_invoice_no)
                                                    {{ $item->purchase->purchase_invoice_no }}
                                                @endisset
                                            </td>
                                            <td class="py-2 px-4 border">
                                                @isset($item->purchase->purchase_date)
                                                    {{ $item->purchase->purchase_date }}
                                                @endisset
                                            </td>
                                            <td>
                                                <a id="action" href="{{ route('purchase.show', $item->purchase->id) }}">
                                                    <button class="bg-green-600 text-white px-3 py-1 mx-2 rounded hover:bg-green-700">
                                                        <i class="fa-solid fa-circle-info"></i>
                                                    </button>
                                                </a>
                                            </td>
                                        </tr>
                                        @php
                                            $i++;
                                        @endphp
                                    @endisset
                                @endforeach
                            </tbody>
                        </table>
                        @if ($i == 1)
                            <div class="text-red-600 text-center">No Purchase history found</div>
                        @endif
                    </div>
                </div>
            @endif
            <div>
                @if ($search_type == 'barcode' or isset($search) or $i == 2)
                    <div class="max-w-6xl mx-auto p-4">
                        <div class="flex justify-end mb-4">
                            <button onclick="printSection('printArea')"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded shadow">
                                🖨️ Print Barcodes
                            </button>
                        </div>

                        <div id="printArea" class="print-container">
                            @foreach ($products as $item)
                                @for ($j = 0; $j < $item->stock; $j++)
                                    <div class="barcode-box">
                                        <p class="title">{{ $item->brand->name ?? '' }} -
                                            {{ $item->category->name ?? '' }}</p>
                                        <p class="title">{{ $item->model ?? '' }}</p>
                                        <div style="display:flex; flex-direction:column; align-items:center; background:white; padding:8px;">
                                            {!! DNS1D::getBarcodeSVG($item->code, 'C128', 1.0, 45, 'black') !!}
                                        </div>
                                    </div>
                                @endfor
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endif
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Delete Confirmation
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('form[id^="deleteForm"]').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This record will be permanently deleted!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        });

        // Special Offer Confirmation and Update Function
        function confirmSpecialOffer(productId, value, selectElement) {
            const offerText = value === 'yes' ? 'Yes' : 'No';
            
            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to set Special Offer to "${offerText}"?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Update it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // If confirmed, update the special offer
                    updateSpecialOffer(productId, value);
                } else {
                    // If cancelled, revert the dropdown to previous value
                    const previousValue = value === 'yes' ? 'no' : 'yes';
                    selectElement.value = previousValue;
                }
            });
        }

        // Special Offer Update Function using Fetch API
        function updateSpecialOffer(productId, value) {
            // Show loading state
            Swal.fire({
                title: 'Updating...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Get CSRF token from meta tag
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            // Using Fetch API
            fetch('{{ route("product.update.special.offer") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    special_offer: value
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Updated!',
                        text: 'Special offer has been updated successfully.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message || 'Something went wrong.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Something went wrong. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            });
        }

        // Status Change Function with Popup using Fetch API
        function confirmStatusChange(url, productId, currentStatus) {
            const statusText = currentStatus == 1 ? 'Active' : 'Inactive';
            const newStatusText = currentStatus == 1 ? 'Inactive' : 'Active';
            
            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to change status from "${statusText}" to "${newStatusText}"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Update it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Updating...',
                        text: 'Please wait',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Using Fetch API for status update
                    fetch(url, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        Swal.fire({
                            title: 'Updated!',
                            text: 'Status has been updated successfully.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'Something went wrong. Please try again.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
                }
            });
        }
    </script>

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

                        if (!$('#category').val()) {
                            categorySelect.empty().append('<option value="">Select Category</option>');
                            $.each(response.categories, function(i, cat) {
                                categorySelect.append('<option value="' + cat.id + '">' + cat
                                    .name + '</option>');
                            });
                        }

                        if (!$('#brand').val()) {
                            brandSelect.empty().append('<option value="">Select Brand</option>');
                            $.each(response.brands, function(i, brand) {
                                brandSelect.append('<option value="' + brand.id + '">' + brand
                                    .name + '</option>');
                            });
                        }

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

            fetchData();
            $('#category, #brand').change(fetchData);

            $('#resetBtn').click(function() {
                $('#filterForm')[0].reset();
                $('#searchTypeWrapper').addClass('hidden');
                window.location = "{{ route('product.list') }}";
            });
        });
    </script>

    <script>
        function printSection(divId) {
            let printContent = document.getElementById(divId).innerHTML;
            let originalContent = document.body.innerHTML;

            document.body.innerHTML = `
            <html>
            <head>
                <title>Print Barcode</title>
                <style>
                    body {
                        margin: 0;
                        padding: 0px;
                        font-family: Arial, sans-serif;
                    }
                    .print-container {
                        display: flex;
                        flex-wrap: wrap;
                        justify-content: space-between;
                        padding-top: 5px;
                        gap-row: 26px;
                    }
                    .barcode-box {
                        width: 48%;
                        margin-bottom: 12px;
                        padding: 0px 4px;
                        text-align: center;
                        border-radius: 6px;
                        height: 22mm;
                    }
                    .title {
                        margin: 0;
                        font-size: 8px;
                        font-weight: bold;
                    }
                    @media print {
                        @page { 
                            size: auto;
                            margin: 0mm;
                        }
                        body { margin: 0; }
                    }
                </style>
            </head>
            <body>
                <div class="print-container">${printContent}</div>
            </body>
            </html>
            `;

            window.print();
            document.body.innerHTML = originalContent;
            location.reload();
        }
    </script>

    <!-- SPECIAL OFFER MODAL -->
    <div id="specialOfferModal" class="fixed inset-0 z-[9990] hidden overflow-y-auto bg-black/70 backdrop-blur-sm items-center justify-center p-4" style="display: none; z-index: 9990;">
        <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden border border-gray-100 transform transition-all my-8">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-amber-500 via-amber-600 to-yellow-600 px-6 py-4 flex items-center justify-between text-white">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">⚡</span>
                    <div>
                        <h3 class="text-lg font-bold">Special Offer Configuration</h3>
                        <p id="soModalProductName" class="text-xs text-amber-100 font-medium truncate max-w-xs"></p>
                    </div>
                </div>
                <button type="button" onclick="closeSpecialOfferModal()" class="text-amber-100 hover:text-white text-2xl font-bold p-1 rounded-lg hover:bg-white/10 transition-colors">&times;</button>
            </div>

            <!-- Modal Body / Form -->
            <form id="specialOfferForm" onsubmit="saveSpecialOffer(event)" class="p-6 space-y-5">
                <input type="hidden" id="so_product_id" name="product_id">
                <input type="hidden" id="so_offer_id" name="offer_id">

                <!-- Form Inline Error / Alert Message -->
                <div id="so_form_alert" class="hidden p-3.5 rounded-xl bg-red-50 border border-red-200 text-red-700 text-xs flex items-center justify-between gap-2 transition-all shadow-sm">
                    <div class="flex items-center gap-2">
                        <span class="text-base flex-shrink-0">⚠️</span>
                        <span id="so_form_alert_msg" class="font-medium"></span>
                    </div>
                    <button type="button" onclick="hideSoFormAlert()" class="text-red-400 hover:text-red-600 text-lg font-bold leading-none">&times;</button>
                </div>

                <!-- 1. Offer Type Selection -->
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Select Offer Type / অফারের ধরন</label>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="cursor-pointer border rounded-xl p-3 text-center transition-all bg-gray-50 flex flex-col items-center justify-center gap-1 text-xs font-semibold" id="label_type_flat">
                            <input type="radio" name="offer_type" value="flat" onchange="toggleOfferTypeFields('flat')" class="sr-only">
                            <span class="text-lg">💵</span>
                            <span>Flat Discount (₹)</span>
                        </label>
                        <label class="cursor-pointer border rounded-xl p-3 text-center transition-all bg-gray-50 flex flex-col items-center justify-center gap-1 text-xs font-semibold" id="label_type_percentage">
                            <input type="radio" name="offer_type" value="percentage" onchange="toggleOfferTypeFields('percentage')" class="sr-only">
                            <span class="text-lg">🏷️</span>
                            <span>Percent Off (%)</span>
                        </label>
                        <label class="cursor-pointer border rounded-xl p-3 text-center transition-all bg-gray-50 flex flex-col items-center justify-center gap-1 text-xs font-semibold" id="label_type_free_product">
                            <input type="radio" name="offer_type" value="free_product" onchange="toggleOfferTypeFields('free_product')" class="sr-only">
                            <span class="text-lg">🎁</span>
                            <span>Free Product</span>
                        </label>
                    </div>
                </div>

                <!-- 2. Dynamic Value Fields -->
                <div id="field_flat_discount" class="hidden">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Flat Discount Amount (₹)</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-bold">₹</span>
                        <input type="number" id="so_flat_discount" name="flat_discount" step="1" min="0" placeholder="e.g. 500" class="w-full pl-8 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 text-sm">
                    </div>
                </div>

                <div id="field_percentage_discount" class="hidden">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Percentage Discount (%)</label>
                    <div class="relative">
                        <input type="number" id="so_percentage_discount" name="percentage_discount" step="0.1" min="0" max="100" placeholder="e.g. 15" class="w-full pl-4 pr-8 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 text-sm">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 font-bold">%</span>
                    </div>
                </div>

                <div id="field_free_product" class="hidden">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Select Free Product Gift 🎁</label>
                    <select id="so_free_product_id" name="free_product_id" class="w-full px-3 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 text-sm bg-white">
                        <option value="">-- Choose Free Product --</option>
                        @if(isset($allProducts))
                            @foreach($allProducts as $pItem)
                                <option value="{{ $pItem->id }}">{{ ($pItem->brand->name ?? '') . ' ' . $pItem->model }} (Stock: {{ $pItem->stock }})</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <!-- 3. Duration & Dates -->
                <div class="border-t border-gray-100 pt-4">
                    <div class="flex items-center justify-between mb-2">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Validity Period / মেয়াদ</label>
                        <!-- Quick Presets -->
                        <div class="flex gap-1">
                            <button type="button" onclick="setOfferDuration(1)" class="px-2 py-0.5 text-[11px] bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 rounded-md font-semibold">1 Day</button>
                            <button type="button" onclick="setOfferDuration(3)" class="px-2 py-0.5 text-[11px] bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 rounded-md font-semibold">3 Days</button>
                            <button type="button" onclick="setOfferDuration(7)" class="px-2 py-0.5 text-[11px] bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 rounded-md font-semibold">7 Days</button>
                            <button type="button" onclick="setOfferDuration(30)" class="px-2 py-0.5 text-[11px] bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 rounded-md font-semibold">30 Days</button>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-[11px] text-gray-600 mb-1">Start Date & Time</label>
                            <input type="datetime-local" id="so_start_date" name="start_date" required class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 text-xs">
                        </div>
                        <div>
                            <label class="block text-[11px] text-gray-600 mb-1">End Date & Time</label>
                            <input type="datetime-local" id="so_end_date" name="end_date" required class="w-full px-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 text-xs">
                        </div>
                    </div>
                </div>

                <!-- 4. Active Status Toggle -->
                <div class="flex items-center justify-between bg-amber-50/60 p-3 rounded-xl border border-amber-100">
                    <span class="text-xs font-bold text-gray-800">Activate Offer Immediately</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="so_is_active" name="is_active" value="1" checked class="sr-only peer">
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-600"></div>
                    </label>
                </div>

                <!-- Modal Footer Buttons -->
                <div class="flex items-center justify-between gap-3 border-t border-gray-100 pt-4">
                    <button type="button" id="btnDeleteSpecialOffer" onclick="deleteSpecialOffer()" class="hidden px-4 py-2 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded-xl border border-red-200 transition-colors">
                        🗑️ Remove Offer
                    </button>
                    <div class="flex items-center gap-2 ml-auto">
                        <button type="button" onclick="closeSpecialOfferModal()" class="px-4 py-2 text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                            Cancel
                        </button>
                        <button type="submit" id="btnSubmitSpecialOffer"
                         class="px-5 py-2 text-xs font-bold text-black bg-gradient-to-r from-amber-500 to-yellow-600 hover:from-amber-600 hover:to-yellow-700 rounded-xl shadow-md transition-all">
                            Save Offer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentOfferId = null;
        let currentSellingPrice = 0;

        function openSpecialOfferModalFromBtn(btn) {
            if (!btn) return;
            const productId = btn.getAttribute('data-id');
            const productName = btn.getAttribute('data-name');
            const sellingPrice = btn.getAttribute('data-price');
            openSpecialOfferModal(productId, productName, sellingPrice);
        }

        function showSoToast(icon, title, text = '') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: icon,
                title: title,
                text: text,
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true,
                customClass: {
                    container: '!z-[999999]'
                },
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
        }

        function showSoFormAlert(msg) {
            const alertBox = document.getElementById('so_form_alert');
            const alertMsg = document.getElementById('so_form_alert_msg');
            if (alertBox && alertMsg) {
                alertMsg.innerText = msg;
                alertBox.classList.remove('hidden');
            }
        }

        function hideSoFormAlert() {
            const alertBox = document.getElementById('so_form_alert');
            if (alertBox) {
                alertBox.classList.add('hidden');
            }
        }

        function openSpecialOfferModal(productId, productName, sellingPrice = 0) {
            hideSoFormAlert();
            const modal = document.getElementById('specialOfferModal');
            if (modal) {
                document.body.appendChild(modal);
                modal.classList.remove('hidden');
                modal.style.display = 'flex';
            }

            currentSellingPrice = parseFloat(sellingPrice) || 0;
            document.getElementById('so_product_id').value = productId;
            document.getElementById('soModalProductName').innerText = productName + (currentSellingPrice > 0 ? ` (Selling Price: ₹${currentSellingPrice})` : '');
            document.getElementById('btnSubmitSpecialOffer').innerText = 'Save Offer';
            
            // Set default dates
            const now = new Date();
            const nowIso = new Date(now.getTime() - (now.getTimezoneOffset() * 60000)).toISOString().slice(0, 16);
            
            const endDate = new Date(now.getTime() + (7 * 24 * 60 * 60 * 1000));
            const endIso = new Date(endDate.getTime() - (endDate.getTimezoneOffset() * 60000)).toISOString().slice(0, 16);

            document.getElementById('so_start_date').value = nowIso;
            document.getElementById('so_end_date').value = endIso;

            // Reset fields
            document.getElementById('so_offer_id').value = '';
            document.getElementById('so_flat_discount').value = '';
            document.getElementById('so_percentage_discount').value = '';
            document.getElementById('so_free_product_id').value = '';
            document.getElementById('so_is_active').checked = true;
            document.getElementById('btnDeleteSpecialOffer').classList.add('hidden');
            currentOfferId = null;

            // Default to flat
            toggleOfferTypeFields('flat');

            // Fetch existing offer via AJAX
            fetch(`/product/${productId}/special-offer`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.offer) {
                        const offer = data.offer;
                        currentOfferId = offer.id;
                        document.getElementById('so_offer_id').value = offer.id;
                        document.getElementById('btnDeleteSpecialOffer').classList.remove('hidden');
                        document.getElementById('btnSubmitSpecialOffer').innerText = 'Update Offer';

                        toggleOfferTypeFields(offer.offer_type);
                        if (offer.offer_type === 'flat') {
                            document.getElementById('so_flat_discount').value = offer.flat_discount || '';
                        } else if (offer.offer_type === 'percentage') {
                            document.getElementById('so_percentage_discount').value = offer.percentage_discount || '';
                        } else if (offer.offer_type === 'free_product') {
                            document.getElementById('so_free_product_id').value = offer.free_product_id || '';
                        }

                        if (offer.start_date) {
                            const sd = new Date(offer.start_date);
                            document.getElementById('so_start_date').value = new Date(sd.getTime() - (sd.getTimezoneOffset() * 60000)).toISOString().slice(0, 16);
                        }
                        if (offer.end_date) {
                            const ed = new Date(offer.end_date);
                            document.getElementById('so_end_date').value = new Date(ed.getTime() - (ed.getTimezoneOffset() * 60000)).toISOString().slice(0, 16);
                        }

                        document.getElementById('so_is_active').checked = offer.is_active == 1;
                    }
                })
                .catch(err => {
                    console.error('Error fetching special offer:', err);
                    showSoToast('error', 'Fetch Error', 'Failed to load existing special offer details.');
                });
        }

        function closeSpecialOfferModal() {
            hideSoFormAlert();
            const modal = document.getElementById('specialOfferModal');
            if (modal) {
                modal.classList.add('hidden');
                modal.style.display = 'none';
            }
        }

        function toggleOfferTypeFields(type) {
            const types = ['flat', 'percentage', 'free_product'];
            types.forEach(t => {
                const label = document.getElementById(`label_type_${t}`);
                const field = document.getElementById(`field_${t}_discount`) || document.getElementById(`field_${t}`);
                const radio = label ? label.querySelector('input[type="radio"]') : null;

                if (t === type) {
                    if (label) label.className = 'cursor-pointer border-2 border-amber-500 rounded-xl p-3 text-center transition-all bg-amber-50 text-amber-800 flex flex-col items-center justify-center gap-1 text-xs font-bold shadow-sm';
                    if (field) field.classList.remove('hidden');
                    if (radio) radio.checked = true;
                } else {
                    if (label) label.className = 'cursor-pointer border border-gray-200 rounded-xl p-3 text-center transition-all bg-gray-50 text-gray-600 hover:border-gray-300 flex flex-col items-center justify-center gap-1 text-xs font-semibold';
                    if (field) field.classList.add('hidden');
                    if (radio) radio.checked = false;
                }
            });
        }

        function setOfferDuration(days) {
            const startDateVal = document.getElementById('so_start_date').value;
            let start = startDateVal ? new Date(startDateVal) : new Date();
            
            let end = new Date(start.getTime() + (days * 24 * 60 * 60 * 1000));
            const endIso = new Date(end.getTime() - (end.getTimezoneOffset() * 60000)).toISOString().slice(0, 16);
            document.getElementById('so_end_date').value = endIso;
        }

        function saveSpecialOffer(event) {
            event.preventDefault();
            hideSoFormAlert();

            const form = event.target;
            const selectedType = form.querySelector('input[name="offer_type"]:checked')?.value || 'flat';

            const flatDiscountVal = document.getElementById('so_flat_discount').value.trim();
            const percentageDiscountVal = document.getElementById('so_percentage_discount').value.trim();
            const flatDiscount = parseFloat(flatDiscountVal) || 0;
            const percentageDiscount = parseFloat(percentageDiscountVal) || 0;
            const startDate = document.getElementById('so_start_date').value;
            const endDate = document.getElementById('so_end_date').value;

            if (selectedType === 'flat') {
                if (!flatDiscountVal || flatDiscount <= 0) {
                    const msg = 'Please enter a valid flat discount amount!';
                    showSoToast('warning', 'Invalid Amount', msg);
                    showSoFormAlert(msg);
                    document.getElementById('so_flat_discount').focus();
                    return;
                }
                if (currentSellingPrice > 0 && flatDiscount >= currentSellingPrice) {
                    const msg = `Flat discount (₹${flatDiscount}) cannot be equal to or greater than selling price (₹${currentSellingPrice})!`;
                    showSoToast('error', 'Discount Exceeds Selling Price', msg);
                    showSoFormAlert(msg);
                    document.getElementById('so_flat_discount').focus();
                    return;
                }
            } else if (selectedType === 'percentage') {
                if (!percentageDiscountVal || percentageDiscount <= 0) {
                    const msg = 'Please enter a valid percentage discount!';
                    showSoToast('warning', 'Invalid Percentage', msg);
                    showSoFormAlert(msg);
                    document.getElementById('so_percentage_discount').focus();
                    return;
                }
                if (percentageDiscount >= 100) {
                    const msg = 'Percentage discount cannot be 100% or greater!';
                    showSoToast('error', 'Invalid Percentage', msg);
                    showSoFormAlert(msg);
                    document.getElementById('so_percentage_discount').focus();
                    return;
                }
            } else if (selectedType === 'free_product') {
                const freeProdId = document.getElementById('so_free_product_id').value;
                if (!freeProdId) {
                    const msg = 'Please choose a free gift product!';
                    showSoToast('warning', 'Select Free Gift', msg);
                    showSoFormAlert(msg);
                    document.getElementById('so_free_product_id').focus();
                    return;
                }
            }

            if (!startDate) {
                const msg = 'Please select a start date and time!';
                showSoToast('warning', 'Missing Start Date', msg);
                showSoFormAlert(msg);
                return;
            }

            if (!endDate) {
                const msg = 'Please select an end date and time!';
                showSoToast('warning', 'Missing End Date', msg);
                showSoFormAlert(msg);
                return;
            }

            if (new Date(endDate) <= new Date(startDate)) {
                const msg = 'End date and time must be after the start date!';
                showSoToast('warning', 'Invalid Validity Period', msg);
                showSoFormAlert(msg);
                return;
            }

            const payload = {
                product_id: document.getElementById('so_product_id').value,
                offer_id: document.getElementById('so_offer_id').value || null,
                offer_type: selectedType,
                flat_discount: selectedType === 'flat' ? flatDiscount : null,
                percentage_discount: selectedType === 'percentage' ? percentageDiscount : null,
                free_product_id: selectedType === 'free_product' ? (document.getElementById('so_free_product_id').value || null) : null,
                start_date: startDate,
                end_date: endDate,
                is_active: document.getElementById('so_is_active').checked ? 1 : 0
            };

            const submitBtn = document.getElementById('btnSubmitSpecialOffer');
            const originalBtnText = submitBtn.innerText;
            submitBtn.disabled = true;
            submitBtn.innerText = 'Saving...';

            fetch('{{ route("product.special.offer.save") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerText = originalBtnText;

                if (data.success) {
                    showSoToast('success', 'Success!', data.message || 'Special offer saved successfully.');
                    closeSpecialOfferModal();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    const msg = data.message || 'Failed to save special offer.';
                    showSoToast('error', 'Error', msg);
                    showSoFormAlert(msg);
                }
            })
            .catch(err => {
                console.error(err);
                submitBtn.disabled = false;
                submitBtn.innerText = originalBtnText;
                const msg = 'An error occurred while saving the offer. Please try again.';
                showSoToast('error', 'Server Error', msg);
                showSoFormAlert(msg);
            });
        }

        function deleteSpecialOffer() {
            const offerId = currentOfferId || document.getElementById('so_offer_id').value || document.getElementById('so_product_id').value;
            if (!offerId) {
                showSoToast('error', 'Error', 'No special offer found to remove.');
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to remove this special offer?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, remove it!',
                customClass: {
                    container: '!z-[999999]'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/product/special-offer/${offerId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            showSoToast('success', 'Removed!', data.message || 'Special offer removed successfully.');
                            closeSpecialOfferModal();
                            setTimeout(() => location.reload(), 1000);
                        } else {
                            const msg = data.message || 'Failed to remove special offer.';
                            showSoToast('error', 'Error', msg);
                            showSoFormAlert(msg);
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        const msg = 'An error occurred while removing the offer.';
                        showSoToast('error', 'Server Error', msg);
                        showSoFormAlert(msg);
                    });
                }
            });
        }
    </script>
@endpush

@push('extra_style')
<style>
    .swal2-container {
        z-index: 999999 !important;
    }
</style>
@endpush