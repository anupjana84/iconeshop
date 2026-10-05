@extends('layouts.main')
@push('page_title')
    <title>Order Place</title>
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
                <div class="flex items-center">
                    <h2 class="text-lg font-semibold text-gray-800 ">Cash Order </h2>
                    <div class="absolute top-3 right-0 cursor-pointer">
                        <a href="" class="px-4 py-2 m-4 rounded-sm bg-green-500"><i class="fa-solid fa-pen-to-square"></i></a>
                    </div>
                </div>
            @endif

        </div>

        {{-- ✅ Second Container: Order Items --}}
        <div class="bg-white rounded-2xl shadow-md p-6 border">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Order Items List</h2>

            <form action="{{ route('order.place', $order->id) }}" method="POST">
                @csrf

                <div class="overflow-x-auto rounded-lg border">
                    <table class="w-full border-collapse text-sm">
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
                                <th class="px-3 py-2 text-left">Delivery</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @php
                                $total_amount = 0;
                            @endphp
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
                                            <option value="No warranty" {{ $item->warranty == 'No warranty' ? 'selected' : '' }}>1
                                                No Warranty</option>
                                            <option value="1 week" {{ $item->warranty == '1 week' ? 'selected' : '' }}>1
                                                Week</option>
                                            <option value="1 month" {{ $item->warranty == '1 month' ? 'selected' : '' }}>1
                                                Month</option>
                                            <option value="3 month" {{ $item->warranty == '3 month' ? 'selected' : '' }}>3
                                                Month</option>
                                            <option value="6 month" {{ $item->warranty == '6 month' ? 'selected' : '' }}>6
                                                Month</option>
                                            <option value="10 month" {{ $item->warranty == '10 month' ? 'selected' : '' }}>6
                                                Month</option>
                                            <option value="1 year" {{ $item->warranty == '1 year' ? 'selected' : '' }}>1
                                                Year</option>
                                            <option value="2 year" {{ $item->warranty == '2 year' ? 'selected' : '' }}>2
                                                Year</option>
                                            <option value="5 year" {{ $item->warranty == '5 year' ? 'selected' : '' }}>5
                                                Year</option>
                                            <option value="company warranty" {{ $item->warranty == 'company warranty' ? 'selected' : '' }}>Company warranty
                                            </option>
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
                                    <td class="px-3 py-2">{{ $item->gst }}%</td>
                                    <td class="px-3 py-2">
                                        <input type="number" name="quantity[]" value="{{ $item->quantity }}" min="1"
                                            class="quantity-input w-20 p-1 rounded-sm border border-gray-300 text-md focus:ring-blue-500"
                                            data-price="{{ $item->price }}"
                                            data-delivery="{{ $item->delivery_charges ?? 0 }}" />
                                        @error('quantity.' . $index)
                                            <span class="text-red-600 text-xs block">{{ $message }}</span>
                                        @enderror
                                    </td>
                                    <td class="px-3 py-2">₹{{ $item->price }}</td>
                                    <td class="px-3 py-2 item-total">₹{{ $item->total }}</td>
                                    <td class="px-3 py-2">
                                        @if ($item->delivery_charges == null)
                                            <span class="px-2 py-1 rounded-sm  bg-green-100 text-green-700">No
                                                delivery</span>
                                        @else
                                            {{ $item->delivery_charges }}
                                        @endif

                                    </td>
                                </tr>
                                @php
                                    $total_amount = $total_amount + $item->total + $item->delivery_charges;
                                @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{-- TOTAL --}}
                    <div class="col-span-3 text-gray-700 font-medium text-lg">
                        Total Amount:
                        <span class="font-bold" id="total_amount_display">₹{{ $total_amount }}</span>
                    </div>

                    {{-- ✅ Payment Section --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">


                        <input type="hidden" name="total_amount" id="total_amount_hidden" value="{{ $total_amount }}">

                        <input type="hidden" name="reward_point_balance" id="reward_point_balance_hidden">

                        <input type="hidden" name="reward_mobile" value="{{ $rewardMobile }}">

                        {{-- REWARD BALANCE --}}
                        <div>
                            <label class="font-semibold">
                                Reward Point Balance
                            </label>

                            <input type="text" id="reward_balance"
                                class="rounded-lg border border-gray-300 p-2 w-full bg-gray-100" readonly
                                value="{{ $rewardBalance ?? 0 }}">
                        </div>

                        {{-- USE REWARD --}}
                        <div>
                            <label class="font-semibold">
                                Use Reward Point
                            </label>

                            <input type="number" name="reward_point_used" id="reward_point_used"
                                class="rounded-lg border border-gray-300 p-2 w-full" placeholder="Enter Reward Point"
                                value="0">
                        </div>

                        {{-- DISCOUNT --}}
                        <div>
                            <label for="discount">
                                Extra Discount Amount(Rs)
                            </label>

                            <input type="number" name="discount"
                                class="rounded-lg border border-gray-300 p-2 w-full focus:ring-2 focus:ring-blue-500"
                                placeholder="Enter Discount">

                            @error('discount')
                                <span class="text-red-600 text-sm">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        {{-- FINAL AMOUNT --}}
                        <div>
                            <label for="final_amount">
                                Final Amount(Rs)
                            </label>

                            <input type="text" name="final_amount" readonly
                                class="rounded-lg border border-gray-300 p-2 w-full focus:ring-2 focus:ring-blue-500"
                                placeholder="Enter Final Amount">

                            @error('final_amount')
                                <span class="text-red-600 text-sm">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        {{-- CASH --}}
                        <div>
                            <label for="cash_payment">
                                Cash Payment amount(Rs)
                            </label>

                            <input type="number" name="cash_payment"
                                class="rounded-lg border border-gray-300 p-2 w-full focus:ring-2 focus:ring-blue-500"
                                placeholder="Enter Cash Payment">

                            @error('cash_payment')
                                <span class="text-red-600 text-sm">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        {{-- ONLINE --}}
                        <div>
                            <label for="online_payment">
                                Online Payment amount(Rs)
                            </label>

                            <input type="number" name="online_payment"
                                class="rounded-lg border border-gray-300 p-2 w-full focus:ring-2 focus:ring-blue-500"
                                placeholder="Enter Online Payment">

                            @error('online_payment')
                                <span class="text-red-600 text-sm">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        {{-- FINANCE --}}
                        <div>
                            <label for="finance">
                                Finance
                            </label>

                            <select name="finance"
                                class="rounded-lg border border-gray-300 p-2 w-full focus:ring-2 focus:ring-blue-500">

                                <option value="">
                                    --Select Finance--
                                </option>

                                @foreach ($finances as $item)

                                    <option value="{{ $item->id }}">
                                        {{ $item->name }}
                                    </option>

                                @endforeach

                            </select>

                            @error('finance')
                                <span class="text-red-600 text-sm">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        {{-- GST --}}
                        <div>
                            <label for="gst_applicable">
                                GST Applicable
                            </label>

                            <select name="gst_applicable"
                                class="rounded-lg border border-gray-300 p-2 w-full focus:ring-2 focus:ring-blue-500">

                                <option value="no">
                                    No
                                </option>

                                <option value="yes">
                                    Yes
                                </option>

                            </select>

                            @error('gst_applicable')
                                <span class="text-red-600 text-sm">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        {{-- BANK --}}
                        <div>
                            <label for="bank_id">
                                Bank(If online paid)
                            </label>

                            <select name="bank_id"
                                class="rounded-lg border border-gray-300 p-2 w-full focus:ring-2 focus:ring-blue-500">

                                <option value="">
                                    -- Select Bank --
                                </option>

                                @foreach ($banks as $bank)

                                    <option value="{{ $bank->id }}" {{ old('bank_id') == $bank->id ? 'selected' : '' }}>

                                        {{ $bank->name }}

                                    </option>

                                @endforeach

                            </select>

                            @error('bank_id')
                                <span class="text-red-600 text-sm">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                    </div>

                    {{-- SUBMIT --}}
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
@push('extra_js')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const discountInput = document.querySelector('input[name="discount"]');
            const finalAmountInput = document.querySelector('input[name="final_amount"]');
            const totalAmountDisplay = document.getElementById('total_amount_display');
            const totalAmountHidden = document.getElementById('total_amount_hidden');
            const quantityInputs = document.querySelectorAll('.quantity-input');

            function calculateTotals() {
                let grandTotal = 0;
                quantityInputs.forEach(input => {
                    const qty = parseFloat(input.value) || 1;
                    const price = parseFloat(input.dataset.price) || 0;
                    const delivery = parseFloat(input.dataset.delivery) || 0;
                    const itemTotal = qty * price;
                    const row = input.closest('tr');
                    const itemTotalCell = row.querySelector('.item-total');
                    if (itemTotalCell) {
                        itemTotalCell.textContent = '₹' + itemTotal.toFixed(2);
                    }
                    grandTotal += itemTotal + (delivery * qty);
                });

                if (totalAmountDisplay) {
                    totalAmountDisplay.textContent = '₹' + grandTotal.toFixed(2);
                }
                if (totalAmountHidden) {
                    totalAmountHidden.value = grandTotal.toFixed(2);
                }

                updateFinalAmount(grandTotal);
            }

            function updateFinalAmount(currentTotal) {
                const total = (currentTotal !== undefined) ? currentTotal : (parseFloat(totalAmountHidden.value) || 0);
                let discount = parseFloat(discountInput ? discountInput.value : 0) || 0;
                if (discount < 0) {
                    alert('Discount cannot be negative');
                    discount = 0;
                    if (discountInput) discountInput.value = 0;
                } else if (discount > total) {
                    alert('Discount cannot exceed total amount');
                    discount = 0;
                    if (discountInput) discountInput.value = 0;
                }
                if (finalAmountInput) {
                    finalAmountInput.value = (total - discount).toFixed(2);
                }
            }

            quantityInputs.forEach(input => {
                input.addEventListener('input', calculateTotals);
            });

            if (discountInput) {
                discountInput.addEventListener('input', () => {
                    const currentTotal = parseFloat(totalAmountHidden ? totalAmountHidden.value : 0) || 0;
                    updateFinalAmount(currentTotal);
                });
            }

            // Initial calculation
            calculateTotals();

            let mobile = "{{ $order->customer->phone ?? '' }}";
            if (mobile != '') {
                fetch('/reward-point/balance/' + mobile)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            const rewardBal = document.getElementById('reward_balance');
                            const rewardHidden = document.getElementById('reward_point_balance_hidden');
                            if (rewardBal) rewardBal.value = data.balance;
                            if (rewardHidden) rewardHidden.value = data.balance;
                        }
                    });
            }
        });
    </script>
@endpush