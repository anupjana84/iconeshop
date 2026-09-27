@extends('layouts.main')
@push('page_title')
    <title>Sale Return Details</title>
@endpush
@section('content_page')
    <!-- Table Section -->
    <div>

        <div class="flex justify-evenly p-2">
            <div>
                @if ($return->sale->customer)
                    
                <p>Return To : {{$return->sale->customer->name}}</p>
                @else
                    Cash
                @endif
                <p>Total Amount : {{$return->total}}</p>
                <p>Return Date : {{$return->return_date}}</p>
            </div>
            <div>
                <p>Sale Invoice Id : {{$return->sale->invoice_number}}</p>
                <p>Sale return Date : {{$return->return_date}}</p>

            </div>
        </div>
        <hr>
        <h2 class="text-lg font-semibold mb-2">Products</h2> <!-- Subheading for Table -->
        <div class="bg-white p-4 rounded shadow overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="py-2 px-4 border">Sl</th>
                        <th class="py-2 px-4 border">Product</th>
                        <th class="py-2 px-4 border">quantity</th>
                        <th class="py-2 px-4 border">price(Rs)</th>
                        <th class="py-2 px-4 border">Total(Rs)</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = 1;
                    @endphp
                    @foreach ($returnItems as $key => $product)
                        <tr class="border">
                            <td class="py-2 px-4 border">{{ $i }}</td>
                            <td class="py-2 px-4 border">{{ $product->product->brand->name }} 
                                {{ $product->product->category->name }} {{ $product->product->model }}</td>
                            <td class="py-2 px-4 border">{{ $product->quantity }}</td>
                            <td class="py-2 px-4 border">{{ $product->price }}</td>
                            <td class="py-2 px-4 border">{{ $product->total }}</td>
                        </tr>
                        @php
                            $i++;
                        @endphp
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
@push('extra_js')
@endpush
