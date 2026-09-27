@extends('layouts.main')
@push('page_title')
    <title>Product Deatils</title>
@endpush
@section('content_page')
    <div class="container mx-auto px-4 py-6">
        {{-- Product Card --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Left: Image Section --}}
            <div class="flex flex-col items-center">
                {{-- Main Image --}}
                <div class="w-full max-w-md border rounded-lg shadow p-3">
                    @if (isset($product->details->thumbnail_image) && $product->details->thumbnail_image != null)
                        <img id="mainImage" src="{{ $product->details->thumbnail_image }}" alt="Product Image"
                            class="w-full h-auto object-contain rounded-lg transition duration-300 ease-in-out" />
                    @else
                        <img id="mainImage" src="{{ asset('images/no_image.jpg') }}" alt="No Image" />
                    @endif

                </div>

                {{-- Thumbnail List --}}
                <div class="flex flex-wrap justify-center mt-4 gap-3">
                    {{-- Default Thumbnail --}}
                    @if (isset($product->details->thumbnail_image) && $product->details->thumbnail_image != null)
                        <img src="{{ $product->details->thumbnail_image }}" alt="Thumbnail"
                            class="w-20 h-20 border rounded-md cursor-pointer object-cover hover:scale-110 transition"
                            onclick="changeImage('{{ $product->details->thumbnail_image }}')" />
                    @else
                        Set Thumbnail image
                    @endif


                    {{-- Additional Images --}}
                    @foreach ($images as $img)
                        <div>
                            <div>
                                <img src="{{ str_replace('\/', '/', $img) }}" alt="Product Image"
                                    class="w-20 h-20 border rounded-md cursor-pointer object-cover hover:scale-110 transition"
                                    onclick="changeImage('{{ str_replace('\/', '/', $img) }}')" />
                            </div>
                            <div onclick="confirmDelete('{{ $img }}', {{ $product->details_id }})"
                                class="text-center p-1 mt-2 mx-3 rounded-md bg-red-300 text-red-600">
                                <i class="fa-solid fa-trash-can"></i>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Right: Product Info --}}
            <div>
                <div class="float-right m-5">

                    <a href="{{ route('product.list') }}"
                        class="px-4 py-2 rounded-md bg-orange-400 right-0 float-right hover:bg-yellow-600">Product List</a>
                    <a href="{{ route('product.edit', $product->id) }}"
                        class="px-4 py-2 rounded-md bg-blue-400 right-0 float-right hover:bg-blue-600 mr-2">Edit</a>
                </div>
                <h2 class="text-2xl font-semibold mb-2">{{ $product->brand->name }}-{{ $product->category->name }}</h2>
                <h3 class="text-xl font-semibold mb-2">{{ $product->details->description ?? 'No description' }}</h3>

                <div class="flex">
                    <p class="text-gray-700 mb-2">
                        <strong>Status:</strong>
                        @isset($product->details->status)
                            <span class="{{ $product->details->status == 1 ? 'text-green-600' : 'text-red-600' }}">
                                {{-- {{ ucfirst($product->details->status) }} --}}
                                {{ $product->details->status == 1 ? 'Active' : 'Inactive' }}
                            </span>
                        @endisset
                    </p>
                    <div>
                        <a href="{{ route('product.change.status', $product->id) }}"
                            class="mx-3 px-3 py-1 rounded-lg bg-amber-200 text-amber-600">Change</a>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        @isset($product->details_id)
                            <div class="flex gap-2">
                                <p class="text-gray-700 mb-2"><strong>Display:</strong>
                                <form action="{{ route('product.updateDisplay', $product->details_id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <select name="display" id="display" class="border-black border-1 bg-white px-2 py-0.5"
                                        onchange="this.form.submit()">
                                        <option value="" @if ($product->details->display == null) selected @endif>No Display
                                        </option>
                                        <option value="top" @if ($product->details->display == 'top') selected @endif>Top</option>
                                        <option value="hot" @if ($product->details->display == 'hot') selected @endif>Hot</option>
                                        <option value="bottom" @if ($product->details->display == 'bottom') selected @endif>Bottom</option>
                                    </select>
                                </form>
                                </p>
                            </div>
                        @endisset

                        <p class="text-gray-700 mb-2"><strong>MRP:</strong>
                            @if (isset($product->details->mrp))
                                <span class="line-through text-red-500">
                                    ₹{{ number_format($product->details->mrp, 2) }}
                                </span>
                            @else
                                No MRP
                            @endif
                        </p>

                        <p class="text-gray-700 mb-2"><strong>Purchase Price (without GST):</strong>
                            ₹{{ $product->purchase_price }}</p>
                        <p class="text-gray-700 mb-2"><strong>Purchase Price (with GST):</strong>
                            ₹{{ $product->purchase_withgst }}</p>
                        <p class="text-gray-700 mb-2"><strong>Sale Price</strong>
                            ₹{{ number_format($product->sale_price, 2) }}
                        </p>
                        <p class="text-gray-700 mb-2"><strong>Sale Price:(by dealer)</strong>
                            ₹{{ number_format($product->price, 2) }}
                        </p>
                        <p class="text-gray-700 mb-2"><strong>Online Price:</strong>
                            ₹{{ number_format($product->online_price, 2) }} | ({{ $product->online_rate }}%)
                        </p>
                        <p class="text-gray-700 mb-2"><strong>Delivery Charge:</strong>
                            ₹{{ number_format($product->delivery_charges_amount, 2) }} |
                            ({{ $product->delivery_charges }}%)
                        </p>
                        <p class="text-gray-700 mb-2"><strong>Dealer Point:</strong>
                            ₹{{ number_format($product->point, 2) }} | ({{ $product->dealer_point }}%)
                        </p>
                        <p class="text-gray-700 mb-2"><strong>Salesman Point:</strong>
                            ₹{{ number_format($product->salesmen_point_price, 2) }} | ({{ $product->salesmen_point }}%)
                        </p>
                        <p class="text-gray-700 mb-2"><strong>Stock :</strong>
                            {{ $product->stock }}</p>
                    </div>
                    <div class="flex items-center">
                        <div class="flex flex-col justify-center items-center">

                            <!-- Product Model -->
                            <div style="font-size: 12px; font-weight: bold; margin-bottom: 2px;">
                                {{ $product->model }}
                            </div>

                            <!-- Barcode Container -->

                            <div
                                style="
        display:flex;
        flex-direction:column;
        align-items:center;
        background:white;
        padding:8px;
    ">
                                {!! DNS1D::getBarcodeSVG($product->code, 'C128', 1.0, 45, 'black') !!}
                                {{-- <p style="margin-top:2px; font-size:12px;">{{ $product->code }}</p> --}}
                            </div>

                        </div>
                    </div>

                </div>



                <hr class="my-4">

                {{-- Add to Cart Button --}}
                {{-- <button class="mt-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg">
                    Edit
                </button> --}}
                @if (!isset($product->details))
                    <label for="">Uplode thumbnail image</label>
                    <form id="thumbnail_image" action="{{ route('product.thambnail', $product->id) }}" method="POST"
                        class="flex flex-1 items-center space-x-2" enctype="multipart/form-data">
                        @csrf
                        <!-- Search Input (fills remaining space) -->
                        <input type="file" id="thumbnail_image" name="thumbnail_image" accept="image/*"
                            placeholder="Uplode thumbnail image" class="p-2 border rounded flex-1 bg-white">
                        <button type="submit"
                            class="text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 px-4 py-2 rounded">
                            Uplode
                        </button>
                    </form>
                @else
                    <label for="">Update thumbnail image</label>
                    <form id="thumbnail_image" action="{{ route('product.thambnail', $product->id) }}" method="POST"
                        class="flex flex-1 items-center space-x-2" enctype="multipart/form-data">
                        @csrf
                        <!-- Search Input (fills remaining space) -->
                        <input type="file" id="thumbnail_image" name="thumbnail_image" accept="image/*"
                            placeholder="Uplode thumbnail image" class="p-2 border rounded flex-1 bg-white">
                        <button type="submit"
                            class="text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 px-4 py-2 rounded">
                            Update
                        </button>
                    </form>
                    <hr class="my-3 text-gray-400">
                    <label for="">Add more images</label>
                    <form id="thumbnail_image" action="{{ route('product.image.add', $product->id) }}" method="POST"
                        class="flex flex-1 items-center space-x-2" enctype="multipart/form-data">
                        @csrf
                        <!-- Search Input (fills remaining space) -->
                        <input type="file" id="thumbnail_image" name="images[]" placeholder="Uplode thumbnail image"
                            class="p-2 border rounded flex-1 bg-white" accept="image/*" multiple>
                        <!-- Search Button -->
                        <button type="submit"
                            class="text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 px-4 py-2 rounded">
                            Add
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection
@push('extra_js')
    <script>
        function changeImage(url) {
            document.getElementById('mainImage').src = url;
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function confirmDelete(imageUrl, id) {
            // console.log(imageUrl);
            // console.log(id);
            Swal.fire({
                title: 'Delete Image?',
                text: "Are you sure you want to delete this image?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/api/deleteProductImage/${id}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                image: imageUrl
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            // console.log(data);
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                            Swal.fire('Deleted!', data.result, 'success');
                        });
                }
            });
        }
    </script>
@endpush
