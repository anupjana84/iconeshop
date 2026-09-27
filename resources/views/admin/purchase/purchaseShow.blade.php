@extends('layouts.main')
@push('page_title')
    <title>Purchace Details</title>
@endpush
@section('content_page')
    <!-- Table Section -->
    <div>

        <div class="bg-white p-6 rounded-lg shadow-sm mb-6 border border-gray-200 max-w-4xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Left Side: Basic Information -->
                <div class="space-y-3">
                    <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Basic Information</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex">
                            <span class="font-medium text-gray-600 w-32">Purchase From:</span>
                            <span class="text-gray-900 font-medium">{{$purchase->company->name}}</span>
                        </div>
                        <div class="flex">
                            <span class="font-medium text-gray-600 w-32">Invoice ID:</span>
                            <span class="text-gray-900">{{$purchase->purchase_invoice_no}}</span>
                        </div>
                        <div class="flex">
                            <span class="font-medium text-gray-600 w-32">Purchase Date:</span>
                            <span class="text-gray-900">{{$purchase->purchase_date}}</span>
                        </div>
                    </div>
                </div>
    
                <!-- Right Side: Financial Details -->
                <div class="space-y-3">
                    <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Financial Details</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total (w/o Discount):</span>
                            <span class="font-medium">₹{{ number_format($purchase->total_wout_discount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-red-600">
                            <span>Discount:</span>
                            <span>- ₹{{ number_format($purchase->discount, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total with GST:</span>
                            <span class="font-medium">₹{{ number_format($purchase->with_gst_total, 2) }}</span>
                        </div>
                        <div class="flex justify-between border-t pt-2 mt-2 text-base font-bold text-gray-900 bg-gray-50 p-2 rounded">
                            <span>Grand Total:</span>
                            <span>₹{{ number_format($purchase->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <h2 class="text-lg font-semibold mb-2">Products</h2> <!-- Subheading for Table -->
        <div class="bg-white p-4 rounded shadow overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="py-2 px-4 border">Sl</th>
                        <th class="py-2 px-4 border">category</th>
                        <th class="py-2 px-4 border">brand</th>
                        <th class="py-2 px-4 border">quantity</th>
                        <th class="py-2 px-4 border">price(Rs)</th>
                        <th class="py-2 px-4 border">gst(%)</th>
                        <th class="py-2 px-4 border">Price With GST</th>
                        <th class="py-2 px-4 border">discount</th>
                        <th class="py-2 px-4 border">gst_applicable</th>
                        <th class="py-2 px-4 border">Return qty</th>
                        <th class="py-2 px-4 border">Return</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $i = 1;
                    @endphp
                    @foreach ($purchase_item as $key => $product)
                        <tr class="border">
                            <td class="py-2 px-4 border">{{ $i }}</td>
                            <td class="py-2 px-4 border">{{ $product->product->category->name }}</td>
                            <td class="py-2 px-4 border">{{ $product->product->brand->name }}</td>
                            <td class="py-2 px-4 border">{{ $product->quantity }}</td>
                            <td class="py-2 px-4 border text-right">{{ $product->price }}</td>
                            <td class="py-2 px-4 border text-center">{{ $product->gst }}</td>
                            <td class="py-2 px-4 border text-right">{{ round(($product->price*($product->gst/100))+$product->price) }}.00</td>
                            <td class="py-2 px-4 border">{{ $product->discount }}</td>
                            <td class="py-2 px-4 border">
                                @if ($product->gst_applicable == 1)
                                    Yes
                                @else
                                    No
                                @endif</td>
                            <td class="py-2 px-4 border text-center">{{ $product->return_quantity }}</td>

                                <td class="py-2 px-4 border">
                                    <form action="{{ route('purchase.return') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $product->id }}">
                                        <input type="number" name="return_quantity" id="return_quantity"  min="1" max="{{$product->quantity-$product->return_quantity}}" class="border rounded px-2 py-1 w-20" placeholder="Qty">
                                        <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Return</button>
                                    </form>
                                </td>
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
