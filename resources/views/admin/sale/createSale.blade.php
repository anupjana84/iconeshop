@extends('layouts.main')
@push('page_title')
    <title>Sale Create</title>
@endpush
@section('content_page')
    <div>
        <div class="max-w-8xl mx-auto bg-white p-6 rounded-lg shadow-lg">
            <h1 class="text-2xl font-bold mb-6">Add Sale</h1>

            <!-- Form to Add Products -->
            <form id="sale-product-form" class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    <!-- Product Code -->
                    <div>
                        <label for="product_code" class="block text-sm font-medium text-gray-700">Product Code</label>
                        <input type="text" id="product_code" placeholder="Enter Product Code"
                            class="mt-1 p-2 border rounded w-full">
                    </div>
                    <!-- Sl no -->
                    <div>
                        <label for="slno" class="block text-sm font-medium text-gray-700">Sl No</label>
                        <input type="text" id="slno" placeholder="Enter Product Code"
                            class="mt-1 p-2 border rounded w-full">
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Category</label>
                        <input type="text" id="category" readonly class="mt-1 p-2 border rounded w-full bg-gray-100">
                    </div>

                    <!-- Brand -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Brand</label>
                        <input type="text" id="brand" readonly class="mt-1 p-2 border rounded w-full bg-gray-100">
                    </div>

                    <!-- Model -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Model</label>
                        <input type="text" id="model" readonly class="mt-1 p-2 border rounded w-full bg-gray-100">
                    </div>

                    <!-- GST -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">GST (%)</label>
                        <input type="number" id="gst" readonly class="mt-1 p-2 border rounded w-full bg-gray-100">
                    </div>

                    <!-- Warranty -->
                    <div>
                        <label for="warranty" class="block text-sm font-medium text-gray-700">Warranty</label>
                        <select id="warranty" class="mt-1 p-2 border rounded w-full">
                            <option value="">--select--</option>
                            <option value="No warranty">No Warranty</option>
                            <option value="1 Week">1 Week</option>
                            <option value="1 Month">1 Month</option>
                            <option value="3 Months">3 Months</option>
                            <option value="6 Months">6 Months</option>
                            <option value="10 Months">10 Months</option>
                            <option value="1 Year">1 Year</option>
                            <option value="2 Years">2 Years</option>
                            <option value="3 Years">3 Years</option>
                            <option value="5 Years">5 Years</option>
                            <option value="company warranty">Company Warranty</option>
                        </select>
                    </div>

                    <!-- Sale Rate -->
                    <div>
                        <label for="sale_rate" class="block text-sm font-medium text-gray-700">Sale Rate (Rs)</label>
                        <input type="number" id="sale_rate" placeholder="Sale Rate" class="mt-1 p-2 border rounded w-full">
                    </div>

                    <!-- Quantity -->
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                        <input type="number" id="quantity" placeholder="Quantity" class="mt-1 p-2 border rounded w-full">
                    </div>

                    <!-- Discount -->
                    <div>
                        <label for="discount" class="block text-sm font-medium text-gray-700">Extra Discount (Rs)</label>
                        <input type="number" id="discount" placeholder="Discount" class="mt-1 p-2 border rounded w-full">
                    </div>

                    <!-- Subtotal -->
                    <div>
                        <label for="subtotal" class="block text-sm font-medium text-gray-700">Subtotal</label>
                        <input type="number" id="subtotal" readonly class="mt-1 p-2 border rounded w-full bg-gray-100">
                    </div>
                </div>

                <button type="button" id="add-sale-product"
                    class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Add Product
                </button>
            </form>

            <!-- Table to Display Products -->
            <h2 class="text-xl font-bold mb-4">Product List</h2>
            <div class="bg-white rounded shadow overflow-x-auto">
                <table id="sale-product-table" class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="p-2 border">Product Code</th>
                            <th class="p-2 border">Category</th>
                            <th class="p-2 border">Brand</th>
                            <th class="p-2 border">Model</th>
                            <th class="p-2 border">GST (%)</th>
                            <th class="p-2 border">Warranty</th>
                            <th class="p-2 border">Sale Rate (Rs)</th>
                            <th class="p-2 border">Quantity</th>
                            <th class="p-2 border">Discount (Rs)</th>
                            <th class="p-2 border">Subtotal</th>
                            <th class="p-2 border">Sl No</th>
                            <th class="p-2 border">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Rows will be added dynamically -->
                    </tbody>
                </table>
            </div>

            <!-- Customer Details -->
            <form id="sale-form" action="{{ route('sale.store') }}" method="POST" class="mt-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                    <div>
                        <label for="customer_phone" class="block text-sm font-medium text-gray-700">Customer Mobile
                            Number</label>
                        <input type="text" id="customer_phone" name="customer_phone" placeholder="Enter 10 Digit Mobile No."
                            class="mt-1 p-2 border rounded w-full" required>
                    </div>
                    <div>
                        <label>Reward Point Balance</label>
                        <input type="text" id="reward_balance" readonly value="0"
                            class="mt-1 p-2 border rounded w-full bg-gray-100">
                    </div>
                    <div>
                        <label>Use Reward Point</label>
                        <input type="number" id="used_reward_points" name="used_reward_points" value="0" min="0"
                            class="mt-1 p-2 border rounded w-full">
                    </div>
                    <div>
                        <label for="customer_name" class="block text-sm font-medium text-gray-700">Customer Name</label>
                        <input type="text" id="customer_name" name="customer_name" placeholder="Customer Name"
                            class="mt-1 p-2 border rounded w-full" required>
                    </div>
                    <div>
                        <label for="customer_whatsapp" class="block text-sm font-medium text-gray-700">Customer
                            Whatsapp</label>
                        <input type="text" id="customer_whatsapp" name="customer_whatsapp"
                            placeholder="Customer whatsapp number" class="mt-1 p-2 border rounded w-full" required>
                    </div>
                    <div>
                        <label for="customer_pin" class="block text-sm font-medium text-gray-700">Customer Pin</label>
                        <input type="text" id="customer_pin" name="customer_pin" placeholder="Customer Pin number"
                            maxlength="6" class="mt-1 p-2 border rounded w-full" required>
                        <div id="sale_pin_status" class="text-xs font-semibold mt-1 hidden"></div>
                    </div>
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">Customer Address</label>
                        <input type="text" id="address" name="address" placeholder="Customer address"
                            class="mt-1 p-2 border rounded w-full" required>
                    </div>
                    <div>
                        <label for="gst_number" class="block text-sm font-medium text-gray-700">Customer GST Number</label>
                        <input type="text" id="gst_number" name="gst_number"
                            class="mt-1 p-2 border rounded w-full bg-gray-100">
                    </div>
                </div>

                <div class="bg-gray-200 text-2xl mt-2 pb-2">
                    <h2>Payment Details</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                    <div>
                        <label for="final_discount" class="block text-sm font-medium text-gray-700">Extra Discount</label>
                        <input type="number" id="final_discount" name="final_discount" required step="0.01"
                            class="mt-1 p-2 border rounded w-full" oninput="updateTotals()">
                    </div>
                    <div>
                        <label for="total_amount" class="block text-sm font-medium text-gray-700">Total Amount</label>
                        <input type="number" id="total_amount" name="total_amount" readonly
                            class="mt-1 p-2 border rounded w-full bg-gray-100">
                    </div>


                    <div>
                        <label for="cash_amount" class="block text-sm font-medium text-gray-700">Cash Payment
                            amount(Rs)</label>
                        <input type="number" id="cash_amount" name="cash_amount"
                            class="mt-1 p-2 border rounded w-full bg-gray-100">
                    </div>
                    <div>
                        <label for="online_amount" class="block text-sm font-medium text-gray-700">Online Payment
                            amount(Rs)</label>
                        <input type="number" id="online_amount" name="online_amount"
                            class="mt-1 p-2 border rounded w-full bg-gray-100">
                    </div>
                    <div>
                        <label for="finance">Finance</label>
                        <select name="finance"
                            class="rounded-lg border border-gray-300 p-2 w-full focus:ring-2 focus:ring-blue-500">
                            <option value="">--Select Finance--</option>
                            @foreach ($finances as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="gst_applicable" class="block text-sm font-medium text-gray-700">GST Applicable</label>
                        <select id="gst_applicable" name="gst_applicable" class="mt-1 p-2 border rounded w-full" required>
                            <option value="">--select--</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </div>
                    <div>
                        <label for="bank" class="block text-sm font-medium text-gray-700">Select Bank(If online
                            payment)</label>
                        <select id="bank" name="bank" class="mt-1 p-2 border rounded w-full">
                            <option value="">-- Select Bank --</option>

                            @foreach ($banks as $bank)
                                <option value="{{ $bank->id }}" {{ old('bank') == $bank->id ? 'selected' : '' }}>
                                    {{ $bank->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="referral_mobile" class="block text-sm font-medium text-gray-700">Referral Mobile
                            Number</label>
                        <input type="text" id="referral_mobile" name="referral_mobile"
                            class="mt-1 p-2 border rounded w-full" placeholder="Referral Mobile No">
                    </div>
                </div>

                <!-- Hidden input to store product data -->
                <input type="hidden" name="products" id="sale-products-input">

                <button type="submit" class="mt-4 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    Submit Sale
                </button>
            </form>
        </div>
    </div>
@endsection

@push('extra_js')
    <script>
        // Fetch product details when product code is entered
        document.getElementById('product_code').addEventListener('change', function () {
            const code = this.value;
            if (!code) return;

            fetch(`/api/productbarcode/${code}`) // your API route to get product details by code
                .then(res => res.json())
                .then(data => {
                    if (data) {
                        // console.log(data);
                        // console.log(data.data.sale_price);
                        document.getElementById('category').value = data.data.category.name;
                        document.getElementById('brand').value = data.data.brand.name;
                        document.getElementById('model').value = data.data.model;
                        document.getElementById('gst').value = data.data.category.gst;
                        document.getElementById('sale_rate').value = data.data.sale_price; // autofill, editable
                    } else {
                        alert("Product not found");
                    }
                });
        });
        // Fetch customer details when product code is entered
        document.getElementById('customer_phone').addEventListener('change', function () {

            const mobile = this.value;

            // RESET

            document.getElementById('customer_name').value = "";
            document.getElementById('customer_pin').value = "";
            document.getElementById('customer_whatsapp').value = "";
            document.getElementById('address').value = "";
            document.getElementById('gst_number').value = "";

            document.getElementById('reward_balance').value = "0";

            // ENABLE EDIT

            document.getElementById('customer_name').readOnly = false;
            document.getElementById('customer_pin').readOnly = false;
            document.getElementById('customer_whatsapp').readOnly = false;
            document.getElementById('address').readOnly = false;

            if (!mobile) return;

            // CUSTOMER FETCH

            fetch(`/api/customer/${mobile}`)

                .then(res => res.json())

                .then(data => {

                    // console.log(data);

                    // CUSTOMER TABLE FOUND

                    if (data.type == 'customer') {

                        document.getElementById('customer_name').value =
                            data.customer.name ?? '';

                        document.getElementById('customer_pin').value =
                            data.customer.pin ?? '';

                        document.getElementById('customer_whatsapp').value =
                            data.customer.wpnumber ?? '';

                        document.getElementById('address').value =
                            data.customer.address ?? '';

                        document.getElementById('gst_number').value =
                            data.customer.gst_number ?? '';

                        // LOCK EXISTING CUSTOMER

                        document.getElementById('customer_name').readOnly = true;

                        document.getElementById('customer_pin').readOnly = true;

                        document.getElementById('customer_whatsapp').readOnly = true;

                        document.getElementById('address').readOnly = true;
                    }

                    // REWARD POINT TABLE FOUND

                    else if (data.type == 'reward') {

                        document.getElementById('customer_name').value =
                            data.reward_point.customer_name ?? '';

                        document.getElementById('reward_balance').value =
                            data.reward_point.balance ?? 0;
                    }
                })

                .catch(error => {

                    // console.log(error);

                });

            // REWARD BALANCE FETCH

            fetch(`/reward-point/balance/${mobile}`)

                .then(res => res.json())

                .then(data => {

                    if (data.balance !== undefined) {

                        document.getElementById('reward_balance').value =
                            data.balance;
                    }
                })

                .catch(error => {

                    // console.log(error);

                });

        });

        function fatchCustomer() {

        }

        // Calculate subtotal when rate/qty/discount changes
        function calculateSubtotal() {
            const rate = parseFloat(document.getElementById('sale_rate').value) || 0;
            const qty = parseFloat(document.getElementById('quantity').value) || 0;
            const discount = parseFloat(document.getElementById('discount').value) || 0;
            const subtotal = (rate * qty) - discount;
            document.getElementById('subtotal').value = subtotal.toFixed(2);
        }

        document.getElementById('sale_rate').addEventListener('input', calculateSubtotal);
        document.getElementById('quantity').addEventListener('input', calculateSubtotal);
        document.getElementById('discount').addEventListener('input', calculateSubtotal);

        // Add product to table
        document.getElementById('add-sale-product').addEventListener('click', function () {
            const code = document.getElementById('product_code').value;
            const slno = document.getElementById('slno').value;
            const category = document.getElementById('category').value;
            const brand = document.getElementById('brand').value;
            const model = document.getElementById('model').value;
            const gst = document.getElementById('gst').value;
            const warranty = document.getElementById('warranty').value;
            const rate = document.getElementById('sale_rate').value;
            const qty = document.getElementById('quantity').value;
            const discount = document.getElementById('discount').value;
            const subtotal = document.getElementById('subtotal').value;

            if (code && slno && category && brand && model && gst && warranty && rate && qty && subtotal) {
                const tableBody = document.querySelector('#sale-product-table tbody');
                const newRow = document.createElement('tr');

                newRow.innerHTML = `
                    <td class="p-2 border">${code}</td>
                    <td class="p-2 border">${category}</td>
                    <td class="p-2 border">${brand}</td>
                    <td class="p-2 border">${model}</td>
                    <td class="p-2 border">${gst}</td>
                    <td class="p-2 border">${warranty}</td>
                    <td class="p-2 border">${rate}</td>
                    <td class="p-2 border">${qty}</td>
                    <td class="p-2 border">${discount}</td>
                    <td class="p-2 border">${subtotal}</td>
                    <td class="p-2 border">${slno}</td>
                    <td class="p-2 border">
                        <button type="button" class="remove-product bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">
                            Remove
                        </button>
                        <input type="hidden" name="product_codes[]" value="${code}">
                    </td>
                `;

                tableBody.appendChild(newRow);

                // Reset form
                document.getElementById('sale-product-form').reset();
                document.getElementById('subtotal').value = '';

                updateTotals();
            } else {
                alert("Please fill all fields.");
            }
        });

        // Remove product row
        document.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-product')) {
                e.target.closest('tr').remove();
                updateTotals();
            }
        });

        // Update total
        function updateTotals() {
            const rows = document.querySelectorAll('#sale-product-table tbody tr');
            let final_discount = document.getElementById('final_discount').value || 0;
            let total = 0;
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                total += parseFloat(cells[9].innerText) || 0;
            });
            document.getElementById('total_amount').value = (total - final_discount).toFixed(2);
        }

        // Handle final submit
        document.getElementById('sale-form').addEventListener('submit', function (e) {
            const rows = document.querySelectorAll('#sale-product-table tbody tr');
            const products = [];

            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                const productData = {
                    product_code: cells[0].innerText,
                    category: cells[1].innerText,
                    brand: cells[2].innerText,
                    model: cells[3].innerText,
                    gst: cells[4].innerText,
                    warranty: cells[5].innerText,
                    sale_rate: parseFloat(cells[6].innerText),
                    quantity: parseInt(cells[7].innerText),
                    discount: parseFloat(cells[8].innerText),
                    subtotal: parseFloat(cells[9].innerText),
                    sl_no: cells[10].innerText,
                };
                products.push(productData);
            });

            if (products.length === 0) {
                alert("Please add at least one product.");
                e.preventDefault();
            } else {
                document.getElementById('sale-products-input').value = JSON.stringify(products);
            }
        });

        // Pincode API lookup listener
        const salePinInput = document.getElementById("customer_pin");
        const salePinStatus = document.getElementById("sale_pin_status");
        if (salePinInput) {
            salePinInput.addEventListener("input", function () {
                const pin = this.value.trim();
                salePinStatus.classList.add("hidden");
                if (/^\d{6}$/.test(pin)) {
                    fetch(`https://api.postalpincode.in/pincode/${pin}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data && data[0] && data[0].Status === "Success") {
                                const info = data[0].PostOffice[0];
                                salePinStatus.textContent = `✓ ${info.District}, ${info.State}`;
                                salePinStatus.className = "text-xs font-semibold mt-1 text-green-600";
                                salePinStatus.classList.remove("hidden");
                            } else {
                                salePinStatus.textContent = "❌ Invalid Pincode";
                                salePinStatus.className = "text-xs font-semibold mt-1 text-red-500";
                                salePinStatus.classList.remove("hidden");
                            }
                        })
                        .catch(err => console.error("Pincode API error:", err));
                }
            });
        }
    </script>
@endpush