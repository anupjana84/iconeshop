@extends('layouts.main')
@push('page_title')
    <title>
        Sale Details</title>
@endpush
@section('content_page')
    <!-- Table Section -->
    <div>

        <div class="relative bg-white shadow-lg rounded-2xl px-5 py-2 border border-gray-200 max-w-3xl mx-auto mt-4">
            <!-- Edit Button -->
            @isset($sales->customer_id)
                
            <a href="{{ route('customers.edit', $sales->customer_id) }}"
                class="absolute top-3 right-3 bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-1.5 rounded-md shadow">
                Edit
            </a>
            @endisset

            {{-- <!-- Header -->
            <div class="flex justify-between items-start mb-3">
                <h2 class="text-xl font-semibold text-gray-800">Sale Summary</h2>
                <span class="text-sm text-gray-500">{{ $sales->created_at->format('d M, Y h:i A') }}</span>
            </div> --}}

            <!-- Content -->
            <div class="grid grid-cols-2 gap-6 text-gray-700">
                <div>
                    @if ($sales->customer_id)
                        <p class="mb-1"><span class="font-medium text-gray-800">Sale To:</span> {{ $sales->customer->name }}
                        </p>
                        <p class="mb-1"><span class="font-medium text-gray-800">Phone no:</span>
                            {{ $sales->customer->phone }}
                        </p>
                        <p class="mb-1"><span class="font-medium text-gray-800">Address :</span>
                            {{ $sales->customer->address }}
                        </p>
                        <p class="mb-1"><span class="font-medium text-gray-800">State :</span>
                            {{ $sales->customer->state }}
                        </p>
                        <p class="mb-1"><span class="font-medium text-gray-800">Pin :</span> {{ $sales->customer->pin }}
                        </p>
                    @else
                        <p class="mb-1">Cash</p>
                    @endif

                </div>
                <div>
                    <p class="mb-1"><span class="font-medium text-gray-800">Invoice ID:</span>
                        {{ $sales->invoice_number }}</p>
                    <p class="mb-1"><span class="font-medium text-gray-800">Sale Date:</span>
                        {{ $sales->created_at->format('d M, Y') }}</p>
                    <p class="mb-1"><span class="font-medium text-gray-800">Total Amount:</span>
                        ₹{{ number_format($sales->total, 2) }}</p>
                    @isset($sales->order_id)
                        @if ($sales->cash_order)
                        <div class="mb-1"><span class="font-medium text-gray-800 bg-green-300 px-2 rounded-sm">Salesman :</span>
                            {{ $sales->order->dealer->name }}
                        </div>
                        @else
                            @isset($sales->order->dealer->name)
                            <p class="mb-1"><span class="font-medium text-gray-800 bg-green-300 px-2 rounded-sm">Sub Dealer:</span>
                                {{ $sales->order->dealer->name }}
                            </p>
                            @endisset
                        @endif
                    @endisset
                    <p class="mb-1"><span class="font-medium text-gray-800">Sale By:</span> {{ $sales->salesman->name }}
                    </p>
                </div>
            </div>
        </div>

        <h2 class="text-lg font-semibold mb-2">Sale Items</h2> <!-- Subheading for Table -->
        <div class="bg-white p-4 rounded shadow overflow-x-auto">
            <table id="sale-table" class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-800 text-white">
                        <th class="py-2">#</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>GST</th>
                        <th>Discount</th>
                        <th>Warranty</th>
                        <th>SL No</th>
                        <th>Action</th>
                        <th>Return qty</th>
                        <th>Return</th>
                    </tr>
                </thead>

                <tbody>
                    @php $i = 1; @endphp
                    @foreach ($sale_item as $product)
                        <tr data-row-id="{{ $product->id }}">
                            <!-- Put a small per-row form INSIDE the last TD (valid HTML) -->
                            <td class="py-2 px-2 border text-center">{{ $i++ }}</td>
                            <td class="py-2 px-2 border">{{ $product->product->brand->name }}
                                {{ $product->product->category->name }} {{ $product->product->model }}</td>
                            <td class="py-2 px-2 border text-center">{{ $product->quantity }}</td>
                            <td class="py-2 px-2 border text-right">{{ $product->price }}</td>
                            <td class="py-2 px-2 border text-center">{{ $product->gst }}</td>
                            <td class="py-2 px-2 border text-right">{{ $product->discount }}</td>

                            <!-- IMPORTANT: inputs remain in row but reference the form via `form="form-<id>"` -->
                            <td class="py-1 px-2 border">
                                <select name="warranty" form="form-{{ $product->id }}"
                                    class=" row-changeable w-full  py-1 border border-gray-500">
                                    <option value="">Select</option>
                                    <option value="no warranty" {{ $product->warranty == 'no warranty' ? 'selected' : '' }}>no warranty
                                    </option>
                                    <option value="1 week" {{ $product->warranty == '1 week' ? 'selected' : '' }}>1 Week
                                    </option>
                                    <option value="1 month" {{ $product->warranty == '1 month' ? 'selected' : '' }}>1 Month
                                    </option>
                                    <option value="3 month" {{ $product->warranty == '3 month' ? 'selected' : '' }}>3 Month
                                    </option>
                                    <option value="6 month" {{ $product->warranty == '6 month' ? 'selected' : '' }}>6 Month
                                    </option>
                                    <option value="10 month" {{ $product->warranty == '10 month' ? 'selected' : '' }}>10 Month
                                    </option>
                                    <option value="1 year" {{ $product->warranty == '1 year' ? 'selected' : '' }}>1 Year
                                    </option>
                                    <option value="2 year" {{ $product->warranty == '2 year' ? 'selected' : '' }}>2 Year
                                    </option>
                                    <option value="5 year" {{ $product->warranty == '5 year' ? 'selected' : '' }}>5 Year
                                    </option>
                                    <option value="company warranty" {{ $product->warranty == 'company warranty' ? 'selected' : '' }}>Company warranty
                                    </option>
                                </select>
                            </td>

                            <td class="py-2 px-2 border">
                                <input type="text" name="sl_no" value="{{ $product->sl_no }}"
                                    class="row-changeable w-full px-2 py-1 border border-gray-500"
                                    form="form-{{ $product->id }}">
                            </td>

                            <td class="py-2 px-2 border">
                                <form id="form-{{ $product->id }}" action="{{ route('sale.update') }}" method="POST"
                                    class="inline-block">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $product->id }}">
                                    <!-- Update button is hidden initially -->
                                    <button type="submit" data-update-btn="{{ $product->id }}"
                                        class="update-btn hidden bg-orange-800 text-white px-3 py-1 rounded hover:bg-green-700">
                                        <i class="fa-solid fa-file-pen"></i> Update
                                    </button>
                                </form>
                            </td>
                            <td class="py-2 px-2 border text-center">{{ $product->return_quantity }}</td>
                            <td class="border">
                                <form action="{{ route('sale.return') }}" method="POST"
                                    class="flex gap-2 p-1 items-center justify-center">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $product->id }}">
                                    <input type="number" name="return_quantity" id="return_quantity" min="1"
                                        max="{{ $product->quantity - $product->return_quantity }}"
                                        class="border rounded px-2 py-1 w-20" placeholder="Qty">
                                    <button type="submit"
                                        class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">Return</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>
@endsection
@push('extra_js')
    <script>
        (function() {
            // Use event delegation on the table for both input and change events
            const table = document.getElementById('sale-table');
            if (!table) return console.warn('sale-table not found');

            // Utility: show the update button for a given row id
            function showUpdateButtonForRow(rowId) {
                // find button with data-update-btn attribute
                const btn = document.querySelector(`[data-update-btn="${rowId}"]`);
                if (!btn) return;
                // If you're using Tailwind's hidden class:
                btn.classList.remove('hidden');
                // As a fallback ensure CSS display if hidden class isn't available:
                btn.style.display = '';
            }

            // Event handler for inputs/selects inside table
            function onChangeEvent(e) {
                const target = e.target;
                // only act for elements that should trigger (class 'row-changeable')
                if (!target.classList || !target.classList.contains('row-changeable')) return;

                // find closest tr
                const tr = target.closest('tr');
                if (!tr) return;
                const rowId = tr.getAttribute('data-row-id');
                if (!rowId) return;
                showUpdateButtonForRow(rowId);
            }

            // listen for input (text) and change (select)
            table.addEventListener('input', onChangeEvent);
            table.addEventListener('change', onChangeEvent);

            // Optional: if you want only one row's button visible at a time, uncomment:
            /*
            function hideAllUpdateButtons() {
                document.querySelectorAll('.update-btn').forEach(b => {
                    b.classList.add('hidden');
                    b.style.display = 'none';
                });
            }
            table.addEventListener('input', function(e){
                hideAllUpdateButtons();
                onChangeEvent(e);
            });
            table.addEventListener('change', function(e){
                hideAllUpdateButtons();
                onChangeEvent(e);
            });
            */
        })();
    </script>
@endpush
