@extends('layouts.main')
@push('page_title')
    <title>Direct Order Place</title>
@endpush
@section('content_page')
    <div class="max-w-6xl mx-auto p-6">

        {{-- ✅ First Container: Customer Info --}}
        <div class="bg-white rounded-2xl shadow-md p-6 mb-6 border relative">
            @if (isset($order->customer_id))
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Customer Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-gray-700">
                    <p><span class="font-medium">Name:</span> {{ $order->customer->name ?? 'N/A' }}</p>
                    <p><span class="font-medium">Phone:</span> {{ $order->customer->phone ?? 'N/A' }}</p>
                    <p><span class="font-medium">Reference Phone:</span> {{ $order->referral_phone ?? '-' }}</p>
                    <p><span class="font-medium">Address:</span> {{ $order->customer->address ?? 'N/A' }}</p>
                    <p><span class="font-medium">Pin No:</span> {{ $order->customer->pin ?? 'N/A' }}</p>
                    <p><span class="font-medium">Order Date:</span> {{ $order->created_at->format('d-M-y') }}</p>
                    <p><span class="font-medium">Salesman:</span>
                        @isset($order->dealer->name)
                            {{ $order->dealer->name }}
                        @endisset
                    </p>
                    <p><span class="font-medium">Order Status:</span> {{ $order->order_status }}</p>
                    <form action="{{ route('order.cancel', $order->id) }}" method="post">
                        @csrf
                        @method('PUT')
                        @if ($order->order_status != 'canceled' && $order->order_status != 'delivered')
                            <button type="submit" class="px-2 cursor-pointer py-1 ml-2 rounded-sm bg-red-500 text-white"
                                onclick="return confirmDelete(event, 'Cancel Order', 'Are you sure you want to cancel this order?');">Cancel
                                Order</button>
                        @endif
                    </form>

                    <p><span class="font-medium">Notes:</span> {{ $order->notes }}</p>
                </div>
                <div class="absolute top-3 right-0 cursor-pointer">
                    <a href="{{ route('customers.edit', $order->customer_id) }}"
                        class="px-4 py-2 m-4 rounded-sm bg-green-500"><i class="fa-solid fa-pen-to-square"></i></a>
                </div>
            @else
                <div class="flex justify-between">
                    <div class="flex items-center">
                        <h2 class="text-lg font-semibold text-gray-800 ">Cash Order </h2>
                        <div class="mx-10 text-md font-semibold bg-green-300 px-2 rounded-sm text-gray-700">Salesman :
                            {{ $order->dealer->name }}
                        </div>
                    </div>
                    <div>
                        <form action="{{ route('order.cancel', $order->id) }}" method="post">
                            @csrf
                            @method('PUT')
                            @if ($order->order_status != 'canceled' && $order->order_status != 'delivered')
                                <button type="submit" class="px-2 cursor-pointer py-1 ml-2 rounded-sm bg-red-500 text-white"
                                    onclick="return confirmDelete(event, 'Cancel Order', 'Are you sure you want to cancel this order?');">Cancel
                                    Order</button>
                            @endif
                        </form>
                    </div>
                </div>
            @endif

        </div>

        {{-- ✅ Second Container: Order Items --}}
        <div class="bg-white rounded-2xl shadow-md p-6 border">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Order Items List</h2>

            <form action="{{ route('order.direct.place', $order->id) }}" method="POST">
                @csrf

                <div class="overflow-x-auto rounded-lg border">
                    <table class="w-full border-collapse text-sm" id="orderTable">
                        <thead class="bg-gray-100 text-gray-700">
                            <tr>
                                <th class="px-3 py-2 text-left">#</th>
                                <th class="px-3 py-2 text-left">Sl No</th>
                                <th class="px-3 py-2 text-left">Warranty</th>
                                <th class="px-3 py-2 text-left">Item</th>
                                <th class="px-3 py-2 text-left">Brand</th>
                                <th class="px-3 py-2 text-left">Model</th>
                                <th class="px-3 py-2 text-left">GST</th>
                                <th class="px-3 py-2 text-left">Quantity</th>
                                <th class="px-3 py-2 text-left">Rate with GST</th>
                                <th class="px-3 py-2 text-left">Total Rate</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @php $total_amount = 0; @endphp
                            @foreach ($order_item as $index => $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-3 py-2">{{ $index + 1 }}</td>
                                    <td class="px-3 py-2">
                                        <input name="slno[]"
                                            class="w-full p-1 rounded-sm border border-gray-300 text-md focus:ring-blue-500" />
                                        @error('slno.' . $index)
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </td>
                                    <td class="px-3 py-2">
                                        <select name="warranty[]"
                                            class="w-full rounded-sm border border-gray-300 text-md focus:ring-blue-500">
                                            <option value="">Select</option>
                                            <option value="1 week" {{ $item->warranty == '1 week' ? 'selected' : '' }}>1
                                                Week</option>
                                            <option value="1 month" {{ $item->warranty == '1 month' ? 'selected' : '' }}>1
                                                Month</option>
                                            <option value="3 month" {{ $item->warranty == '3 month' ? 'selected' : '' }}>3
                                                Month</option>
                                            <option value="6 month" {{ $item->warranty == '6 month' ? 'selected' : '' }}>6
                                                Month</option>
                                            <option value="1 year" {{ $item->warranty == '1 year' ? 'selected' : '' }}>1
                                                Year</option>
                                            <option value="2 year" {{ $item->warranty == '2 year' ? 'selected' : '' }}>2
                                                Year</option>
                                            <option value="5 year" {{ $item->warranty == '5 year' ? 'selected' : '' }}>5
                                                Year</option>
                                        </select>
                                        @error('warranty.' . $index)
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </td>
                                    <td class="px-3 py-2 font-semibold">{{ $item->product->category->name }}</td>
                                    <td class="px-3 py-2">{{ $item->product->brand->name }}</td>
                                    <td class="px-3 py-2">
                                        <span class="font-bold text-gray-900 block">{{ $item->product->model }}</span>
                                        @if (!empty($item->product->code))
                                            <span class="inline-block font-mono font-bold text-blue-700 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200 text-xs mt-0.5">
                                                Code: {{ $item->product->code }}
                                            </span>
                                        @endif
                                        @php
                                            $so = $item->product->specialOffer ?? null;
                                            $soActive = $so && $so->isCurrentlyActive();
                                        @endphp
                                        @if($soActive)
                                            @if($so->offer_type === 'flat')
                                                <span class="inline-flex items-center gap-1 text-[11px] font-black bg-amber-100 text-amber-900 border border-amber-300 px-2 py-0.5 rounded-md mt-1">
                                                    ⚡ Special Offer: ₹{{ number_format($so->flat_discount, 0) }} FLAT OFF
                                                </span>
                                            @elseif($so->offer_type === 'percentage')
                                                <span class="inline-flex items-center gap-1 text-[11px] font-black bg-purple-100 text-purple-900 border border-purple-300 px-2 py-0.5 rounded-md mt-1">
                                                    ⚡ Special Offer: {{ $so->percentage_discount }}% OFF
                                                </span>
                                            @elseif($so->offer_type === 'free_product' && $so->freeProduct)
                                                <span class="inline-flex items-center gap-1 text-[11px] font-black bg-emerald-100 text-emerald-900 border border-emerald-300 px-2 py-0.5 rounded-md mt-1">
                                                    🎁 Free Gift: {{ $so->freeProduct->brand->name ?? '' }} {{ $so->freeProduct->model }}
                                                </span>
                                            @endif
                                        @elseif(!empty($item->product->free_gift))
                                            <span class="inline-flex items-center gap-1 text-[11px] font-black bg-emerald-100 text-emerald-900 border border-emerald-300 px-2 py-0.5 rounded-md mt-1">
                                                🎁 Free Gift: {{ $item->product->free_gift }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2">{{ $item->product->category->gst }}%</td>
                                    <td class="px-3 py-2">
                                        <input name="quantity[]" value="{{ $item->quantity }}" min="1"
                                            class="quantity w-20 p-1 rounded-sm border border-gray-300 text-md focus:ring-blue-500"
                                            type="number" />
                                        @error('quantity.' . $index)
                                            <span class="text-red-600 text-xs block">{{ $message }}</span>
                                        @enderror
                                    </td>
                                    <td class="px-3 py-2 flex items-center gap-1">
                                        ₹<input name="price[]" value="{{ $item->product->sale_price }}"
                                            class="price w-26 p-1 rounded-sm border border-gray-300 text-md focus:ring-blue-500"
                                            type="number" min="0" step="1" />
                                        @error('price.' . $index)
                                            <span class="text-red-600 text-xs">{{ $message }}</span>
                                        @enderror
                                    </td>
                                    <td class="px-3 py-2 totalRate">₹{{ $item->quantity * $item->product->sale_price }}
                                    </td>
                                </tr>
                                @php $total_amount += ($item->quantity * $item->product->sale_price); @endphp
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Grand total display -->
                    <div class="text-right p-4 font-semibold text-lg">
                        Grand Total: ₹<span id="grandTotal">{{ $total_amount }}</span>
                    </div>
                </div>

                {{-- ✅ Payment Section --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">

                    <input type="hidden" name="total_amount" value="{{ $total_amount }}">
                    <div>
                        <label for="discount">Discount Amount(Rs)</label>
                        <input type="number" name="discount"
                            class="rounded-lg border border-gray-300 p-2 w-full focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter Discount">
                    </div>
                    <div>
                        <label for="final_amount">Final Amount(Rs)</label>
                        <input type="text" name="final_amount" readonly
                            class="rounded-lg border bg-gray-100 border-gray-300 p-2 w-full focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter GST Number">
                        @error('final_amount')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="cash_payment">Cash Payment amount(Rs)</label>
                        <input type="number" name="cash_payment"
                            class="rounded-lg border border-gray-300 p-2 w-full focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter Cash Payment">
                    </div>
                    <div>
                        <label for="online_payment">Online Payment amount(Rs)</label>
                        <input type="number" name="online_payment"
                            class="rounded-lg border border-gray-300 p-2 w-full focus:ring-2 focus:ring-blue-500"
                            placeholder="Enter Online Payment">
                    </div>


                    <div>
                        <label for="gst_applicable">GST Applicable</label>
                        <select name="gst_applicable"
                            class="rounded-lg border border-gray-300 p-2 w-full focus:ring-2 focus:ring-blue-500">
                            <option value="no">No</option>
                            <option value="yes">Yes</option>
                        </select>
                    </div>
                    <div>
                        <label for="bank_id">Bank (If online pay)</label>
                        <select name="bank_id"
                            class="rounded-lg border border-gray-300 p-2 w-full focus:ring-2 focus:ring-blue-500">
                            <option value="">-- Select Bank --</option>

                            @foreach ($banks as $bank)
                                <option value="{{ $bank->id }}" {{ old('bank_id') == $bank->id ? 'selected' : '' }}>
                                    {{ $bank->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('bank_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div>
                    <p class="text-red-600 pt-4 px-10 font-medium">EMI, finance, due, or advance payments are not
                        accepted under this circumstances.</p>
                </div>

                <div class="mt-6">
                    <button type="submit"
                        class="w-full cursor-pointer bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg shadow">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
{{-- @push('extra_js')
<script>
    const discountInput = document.querySelector('input[name="discount"]');
    const final_amountInput = document.querySelector('input[name="final_amount"]');
    // discountInput.addEventListener('input', () => {
    // });
    let totalAmount = document.getElementById('grandTotal').innerText;

    discountInput.addEventListener('input', () => {
        const discount = parseFloat(discountInput.value) || 0;
        final_amountInput.value = totalAmount - discount;
    });
    discountInput.addEventListener('input', () => {
        if (discountInput.value < 0) {
            alert('Discount cannot be negative');
            discountInput.value = 0;
        } else if (discountInput.value > totalAmount) {
            alert('Discount cannot exceed total amount');
            discountInput.value = 0;
        }
        final_amountInput.value = totalAmount - discountInput.value;
    });
    ////
    document.addEventListener("DOMContentLoaded", () => {
        const table = document.getElementById("orderTable");
        const grandTotalEl = document.getElementById("grandTotal");
        totalAmount = grandTotalEl.textContent;


        // Listen for input changes on all price fields
        table.addEventListener("input", (e) => {
            if (!e.target.classList.contains("price")) return;

            const row = e.target.closest("tr");
            const price = parseFloat(e.target.value) || 0;
            const quantity = parseFloat(row.querySelector(".quantity").textContent) || 0;
            const totalCell = row.querySelector(".totalRate");

            // Update individual total
            const total = quantity * price;
            totalCell.textContent = "₹" + total.toFixed(2);

            // Recalculate grand total
            let grandTotal = 0;
            table.querySelectorAll(".totalRate").forEach(td => {
                const val = parseFloat(td.textContent.replace("₹", "")) || 0;
                grandTotal += val;
            });

            grandTotalEl.textContent = grandTotal.toFixed(2);
        });
    });
</script>
@endpush --}}

@push('extra_js')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const table = document.getElementById("orderTable");
            const grandTotalEl = document.getElementById("grandTotal");

            const discountInput = document.querySelector('input[name="discount"]');
            const finalAmountInput = document.querySelector('input[name="final_amount"]');
            const cashPaymentInput = document.querySelector('input[name="cash_payment"]');
            const onlinePaymentInput = document.querySelector('input[name="online_payment"]');
            const totalAmountInput = document.querySelector('input[name="total_amount"]');

            // Helper: format number as ₹xxx.xx
            const formatMoney = (num) => "₹" + parseFloat(num || 0).toFixed(2);

            // Function: Calculate and update grand total
            const updateGrandTotal = () => {
                let grandTotal = 0;
                table.querySelectorAll(".totalRate").forEach(td => {
                    const val = parseFloat(td.textContent.replace("₹", "")) || 0;
                    grandTotal += val;
                });
                grandTotalEl.textContent = grandTotal.toFixed(2);
                if (totalAmountInput) {
                    totalAmountInput.value = grandTotal.toFixed(2);
                }
                updateFinalAmount(); // re-sync after recalculating
            };

            // Function: Calculate and update final amount (total - discount)
            const updateFinalAmount = () => {
                const totalAmount = parseFloat(grandTotalEl.textContent) || 0;
                const discount = parseFloat(discountInput.value) || 0;

                if (discount < 0) {
                    alert("Discount cannot be negative!");
                    discountInput.value = 0;
                } else if (discount > totalAmount) {
                    alert("Discount cannot exceed total amount!");
                    discountInput.value = 0;
                }

                const final = totalAmount - (parseFloat(discountInput.value) || 0);
                finalAmountInput.value = final.toFixed(2);

                updatePaymentCheck(); // sync payment validation
            };

            // Function: Validate payment totals
            const updatePaymentCheck = () => {
                const final = parseFloat(finalAmountInput.value) || 0;
                const cash = parseFloat(cashPaymentInput.value) || 0;
                const online = parseFloat(onlinePaymentInput.value) || 0;
                const paid = cash + online;

                if (paid > final) {
                    alert("⚠️ Total payment cannot exceed Final Amount!");
                    cashPaymentInput.value = "";
                    onlinePaymentInput.value = "";
                }
            };

            // 💰 Listen for input changes on item price & quantity fields
            table.addEventListener("input", (e) => {
                if (!e.target.classList.contains("price") && !e.target.classList.contains("quantity")) return;

                const row = e.target.closest("tr");
                const priceInput = row.querySelector(".price");
                const qtyInput = row.querySelector(".quantity");
                const price = parseFloat(priceInput ? priceInput.value : 0) || 0;
                const quantity = parseFloat(qtyInput ? (qtyInput.value || qtyInput.textContent) : 0) || 0;
                const totalCell = row.querySelector(".totalRate");

                const total = quantity * price;
                totalCell.textContent = formatMoney(total);

                updateGrandTotal();
            });

            // 💸 Listen for input changes on discount & payments
            if (discountInput) discountInput.addEventListener("input", updateFinalAmount);
            if (cashPaymentInput) cashPaymentInput.addEventListener("input", updatePaymentCheck);
            if (onlinePaymentInput) onlinePaymentInput.addEventListener("input", updatePaymentCheck);

            // Initial setup
            updateGrandTotal();
        });
    </script>
@endpush