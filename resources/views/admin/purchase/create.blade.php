@extends('layouts.main')
@push('page_title')
    <title>Purchase Create</title>
@endpush
@section('content_page')
    <div>
        <div class="max-w-8xl mx-auto bg-white p-6 rounded-lg shadow-lg">
            <h1 class="text-2xl font-bold mb-6">Add Purchase</h1>

            <!-- Form to Add Products -->
            <form id="product-form" class="mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Category (Select Input) -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>
                        <select id="category_id" name="category_id" class="mt-1 p-2 border rounded w-full">
                            <option value="">--select--</option>
                            @foreach ($categories as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!-- Brand (Select Input) -->
                    <div>
                        <label for="brand_id" class="block text-sm font-medium text-gray-700">Brand</label>
                        <select id="brand_id" class="mt-1 p-2 border rounded w-full">
                            <option value="">--select--</option>
                            @foreach ($brands as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Model -->
                    <div>
                        <label for="model" class="block text-sm font-medium text-gray-700">Model</label>
                        <input type="text" id="model" placeholder="Model number"
                            class="mt-1 p-2 border rounded w-full">
                    </div>

                    <!-- Quantity -->
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity</label>
                        <input type="number" id="quantity" onchange="calculateSubAmount()" placeholder="Quantity"
                            class="mt-1 p-2 border rounded w-full">
                    </div>
                    <!-- Purchase Rate -->
                    <div>
                        <label for="rate" class="block text-sm font-medium text-gray-700">Purchase Rate per
                            unit(Rs)(without gst)</label>
                        <input type="number" id="rate" onchange="calculateSubAmount()" oninput="poputatInput()"
                            placeholder="Purchase Rate in rupees" class="mt-1 p-2 border rounded w-full">
                    </div>
                    <!-- Discount -->
                    <div>
                        <label for="discount" class="block text-sm font-medium text-gray-700">Discount(Rs)</label>
                        <input type="number" id="discount" placeholder="Discount" class="mt-1 p-2 border rounded w-full">
                    </div>

                    <!-- GST -->
                    <div>
                        <label for="gst" class="block text-sm font-medium text-gray-700">GST (%)</label>
                        <input type="number" id="gst" name="gst" placeholder="GST"
                            class="mt-1 p-2 border rounded w-full" readonly>
                        <div class="text-sm text-gray-500 mt-1">GST Amount: <span id="gst_amount">0</span></div>
                    </div>

                    <!-- Sale Rate -->
                    <div>
                        <label for="sale_rate" class="block text-sm font-medium text-gray-700">Sale Rate(%)</label>
                        <input type="number" id="sale_rate" 
                            placeholder="Sale Rate in percentage(%)" class="mt-1 p-2 border rounded w-full">
                        <div class="text-sm text-gray-500 mt-1">Sale Amount: <span id="sale_amount">0</span></div>
                    </div>

                    <!-- online rate -->
                    <div>
                        <label for="online_rate" class="block text-sm font-medium text-gray-700">Online Rate(%)</label>
                        <input type="number" id="online_rate" placeholder="online rate in ppercentage(%)"
                            class="mt-1 p-2 border rounded w-full">
                        <div class="text-sm text-gray-500 mt-1">Online rate Amount: <span id="online_rate_amount">0</span>
                        </div>
                    </div>

                    <!-- Delivery charges -->
                    <div>
                        <label for="delivery_charge" class="block text-sm font-medium text-gray-700">Delivery charges
                            (%)</label>
                        <input type="number" id="delivery_charge" placeholder="delivery_charge in percentage(%)"
                            class="mt-1 p-2 border rounded w-full">
                        <div class="text-sm text-gray-500 mt-1">Delivery charges Amount: <span
                                id="delivery_charge_amount">0</span></div>
                    </div>
                    <!-- Delivery charges -->
                    <div>
                        <label for="dealer_point" class="block text-sm font-medium text-gray-700">Dealer Point(%)</label>
                        <input type="number" id="dealer_point" placeholder="dealer_point in percentage(%)"
                            class="mt-1 p-2 border rounded w-full">
                        <div class="text-sm text-gray-500 mt-1">Dealer Point Amount: <span id="dealer_point_amount">0</span>
                        </div>
                    </div>
                    <!-- Delivery charges -->
                    <div>
                        <label for="salesmen_point" class="block text-sm font-medium text-gray-700">Salesmen Point
                            (%)</label>
                        <input type="number" id="salesmen_point" placeholder="salesmen_point"
                            class="mt-1 p-2 border rounded w-full">
                        <div class="text-sm text-gray-500 mt-1">Salesmen Point Amount: <span
                                id="salesmen_point_amount">0</span></div>
                    </div>

                    {{-- <!-- Gross Weight -->
                    <div>
                        <label for="gross_wt" class="block text-sm font-medium text-gray-700">Gross Weight</label>
                        <input type="number" id="gross_wt" placeholder="Gross Weight"
                            class="mt-1 p-2 border rounded w-full">
                    </div>

                    <!-- Net Weight -->
                    <div>
                        <label for="net_wt" class="block text-sm font-medium text-gray-700">Net Weight</label>
                        <input type="number" id="net_wt" placeholder="Net Weight"
                            class="mt-1 p-2 border rounded w-full">
                    </div>



                    <!-- Charges -->
                    <div>
                        <label for="charges" class="block text-sm font-medium text-gray-700">Charges</label>
                        <input type="number" id="charges" placeholder="Charges" class="mt-1 p-2 border rounded w-full">
                    </div> --}}

                    <!-- Sub Amount -->
                    <div>
                        <label for="sub_amount" class="block text-sm font-medium text-gray-700">Sub Amount</label>
                        <input type="number" id="sub_amount" placeholder="Sub Amount"
                            class="mt-1 p-2 border rounded w-full" required>
                    </div>
                    {{-- <!-- Actual Purity -->
                    <div>
                        <label for="actual_purity" class="block text-sm font-medium text-gray-700">Actual Purity</label>
                        <select id="actual_purity" class="mt-1 p-2 border rounded w-full">
                            <option value="">--select--</option>
                            <option value="99.99">99.99</option>
                            <option value="99.50">99.50</option>
                            <option value="91.60">91.60</option>
                        </select>
                    </div> --}}
                </div>
                <button type="button" id="add-product"
                    class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                    Add Product
                </button>
            </form>

            <!-- Table to Display Products -->
            <h2 class="text-xl font-bold mb-4">Product List</h2>
            <div class="bg-white rounded shadow overflow-x-auto">
                <table id="product-table" class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="p-2 border">Catagory</th>
                            <th class="p-2 border">Brand</th>
                            <th class="p-2 border">Model</th>
                            <th class="p-2 border">Quantity</th>
                            <th class="p-2 border">Purchase Rate/unit(Rs)</th>
                            <th class="p-2 border">Discount(Rs)</th>
                            <th class="p-2 border">GST (%)</th>
                            <th class="p-2 border">Sale Rate(%)</th>
                            <th class="p-2 border">Online Rate(%)</th>
                            <th class="p-2 border">Delivery charges(%)</th>
                            <th class="p-2 border">Dealer Point(%)</th>
                            <th class="p-2 border">Salesmen Point(%)</th>
                            <th class="p-2 border">Sub Amount</th>
                            <th class="p-2 border">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Rows will be added here dynamically -->
                    </tbody>
                </table>
            </div>

            <!-- Total Calculations -->
            {{-- <hr class="my-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm">Total SGST: <span id="total_sgst">0</span></p>
                    <p class="text-sm">Total CGST: <span id="total_cgst">0</span></p>
                    <p class="text-sm">Total IGST: <span id="total_igst">0</span></p>
                </div>
                <div>
                    <p class="text-sm">Total Sub Amount: <span id="total_sub_amount">0</span></p>
                    <p class="text-sm">Total Charges: <span id="total_charges">0</span></p>
                </div>
            </div> --}}

            <!-- Vendor Details Form -->
            <form id="vendor-form" action="{{ route('purchase.store') }}" method="POST" class="mt-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="vendor_id" class="block text-sm font-medium text-gray-700">Vendor ID</label>
                        <select id="vendor_id" name="vendor_id" class="mt-1 p-2 border rounded w-full" required>
                            <option value="">--select--</option>
                            @foreach ($company as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="total_wout_discount" class="block text-sm font-medium text-gray-700">Total (w/o Discount)</label>
                        <input type="number" id="total_wout_discount" name="total_wout_discount" placeholder="Total w/o Discount"
                            class="mt-1 p-2 border rounded w-full" readonly>
                    </div>
                    <div>
                        <label for="discount_amount" class="block text-sm font-medium text-gray-700">Discount (Rs)</label>
                        <input type="number" id="discount_amount" name="discount" placeholder="Discount Amount"
                            class="mt-1 p-2 border rounded w-full" value="0">
                    </div>
                    <div>
                        <label for="total_amount" class="block text-sm font-medium text-gray-700">Total Amount</label>
                        <input type="number" id="total_amount" name="total_amount" placeholder="Total Amount"
                            class="mt-1 p-2 border rounded w-full" required readonly>
                    </div>
                    {{-- <div>
                        <label for="round_off" class="block text-sm font-medium text-gray-700">Round Off</label>
                        <input type="number" name="round_off" placeholder="Round Off" step="0.01"
                            class="mt-1 p-2 border rounded w-full" required>
                    </div> --}}
                    <div>
                        <label for="invoice_number" class="block text-sm font-medium text-gray-700">Invoice Number</label>
                        <input type="text" name="invoice_number" placeholder="Invoice Number"
                            class="mt-1 p-2 border rounded w-full" required>
                    </div>
                    {{-- <div>
                        <label for="net_amount" class="block text-sm font-medium text-gray-700">Net Amount</label>
                        <input type="number" id="net_amount" name="net_amount" placeholder="Net Amount"
                            class="mt-1 p-2 border rounded w-full" required readonly>
                    </div> --}}
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700">Purchase Date</label>
                        <input type="date" id="date" name="date" placeholder="Date" required
                            class="mt-1 p-2 border rounded w-full">
                    </div>
                    <div>
                        <label for="gst_applicable" class="block text-sm font-medium text-gray-700">GST Applicable</label>
                        <select id="gst_applicable" name="gst_applicable" class="mt-1 p-2 border rounded w-full"
                            required>
                            <option value="">--select--</option>
                            <option value="1">Yes</option>
                            <option value="0">No</option>

                        </select>
                    </div>
                </div>

                <!-- Hidden input to store product data -->
                <input type="hidden" name="products" id="products-input">

                <button type="submit" class="mt-4 bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    Submit Purchase
                </button>
            </form>
        </div>


    </div>
@endsection
@push('extra_js')
    <script>
        // Function to calculate GST amounts
        // function calculateGST() {
        //     const netWt = parseFloat(document.getElementById('net_wt').value) || 0;
        //     const rate = parseFloat(document.getElementById('rate').value) || 0;
        //     const sGst = parseFloat(document.getElementById('s_gst').value) || 0;
        //     const cGst = parseFloat(document.getElementById('c_gst').value) || 0;
        //     const iGst = parseFloat(document.getElementById('i_gst').value) || 0;

        //     document.getElementById('s_gst_amount').textContent = (netWt * rate * (sGst / 100)).toFixed(2);
        //     document.getElementById('c_gst_amount').textContent = (netWt * rate * (cGst / 100)).toFixed(2);
        //     document.getElementById('i_gst_amount').textContent = (netWt * rate * (iGst / 100)).toFixed(2);
        // }

        // Function to calculate sub amount
        function calculateSubAmount() {
            const quantity = parseFloat(document.getElementById('quantity').value) || 0;
            const rate = parseFloat(document.getElementById('rate').value) || 0;
            const subAmount = (quantity * rate);
            document.getElementById('sub_amount').value = subAmount.toFixed(2);
        }

        // Function to get category name by ID
        function getCategorytNameById(categoryId) {
            const categories = @json($categories); // Pass products from the controller
            const category = categories.find(p => p.id == categoryId);
            return category ? category.name : 'Unknown category';
        }
        // Function to get brand name by ID
        function getBrandtNameById(brandId) {
            const brands = @json($brands); // Pass products from the controller
            const brand = brands.find(p => p.id == brandId);
            return brand ? brand.name : 'Unknown Brand';
        }

        // Add product to the table
        document.getElementById('add-product').addEventListener('click', function() {
            const categoryId = document.getElementById('category_id').value;
            const categoryName = getCategorytNameById(categoryId);
            const brandId = document.getElementById('brand_id').value;
            const brandName = getBrandtNameById(brandId);
            const Model = document.getElementById('model').value;
            const Quantity = document.getElementById('quantity').value;
            const Rate = document.getElementById('rate').value;
            const Discount = document.getElementById('discount').value;
            const Gst = document.getElementById('gst').value;
            const saleRate = document.getElementById('sale_rate').value;
            const onlineRate = document.getElementById('online_rate').value;
            const deliveryCharge = document.getElementById('delivery_charge').value;
            const dealerPoint = document.getElementById('dealer_point').value;
            const salesmenPoint = document.getElementById('salesmen_point').value;
            const subAmount = document.getElementById('sub_amount').value;

            if (categoryId && brandId && Model && Quantity && Rate && Gst && saleRate && onlineRate &&
                deliveryCharge && dealerPoint && salesmenPoint && subAmount) {
                const tableBody = document.querySelector('#product-table tbody');
                const newRow = document.createElement('tr');

                newRow.innerHTML = `
                <td class="p-2 border">${categoryName}</td>
                <td class="p-2 border">${brandName}</td>
                <td class="p-2 border">${Model}</td>
                <td class="p-2 border">${Quantity}</td>
                <td class="p-2 border">${Rate}</td>
                <td class="p-2 border">${Discount}</td>
                <td class="p-2 border">${Gst}</td>
                <td class="p-2 border">${saleRate}</td>
                <td class="p-2 border">${onlineRate}</td>
                <td class="p-2 border">${deliveryCharge}</td>
                <td class="p-2 border">${dealerPoint}</td>
                <td class="p-2 border">${salesmenPoint}</td>
                <td class="p-2 border">${subAmount}</td>
                <td class="p-2 border">
                    <button type="button" class="remove-product bg-red-500 text-white px-2 py-1 rounded hover:bg-red-600">
                        Remove
                    </button>
                    <input type="hidden" name="category_ids[]" value="${categoryId}">
                    <input type="hidden" name="brand_ids[]" value="${brandId}">
                </td>
            `;

                tableBody.appendChild(newRow);

                // Clear the form
                document.getElementById('product-form').reset();
                document.getElementById('sub_amount').value = '';

                // Update totals
                updateTotals();
            } else {
                alert('Please fill out all fields.');
            }
        });

        // Remove product from the table
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-product')) {
                event.target.closest('tr').remove();
                updateTotals();
            }
        });

        // Update totals
        function updateTotals() {
            const rows = document.querySelectorAll('#product-table tbody tr');
            let totalWoutDiscount = 0;

            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                totalWoutDiscount += parseFloat(cells[12].innerText);
            });

            document.getElementById('total_wout_discount').value = totalWoutDiscount.toFixed(2);
            
            calculateFinalTotal();
        }

        function calculateFinalTotal() {
            const totalWoutDiscount = parseFloat(document.getElementById('total_wout_discount').value) || 0;
            const discountAmount = parseFloat(document.getElementById('discount_amount').value) || 0;
            
            const totalAmount = totalWoutDiscount - discountAmount;
            
            document.getElementById('total_amount').value = totalAmount.toFixed(2);
        }

        // Listen for discount input changes
        document.getElementById('discount_amount').addEventListener('input', calculateFinalTotal);
        document.getElementById('vendor-form').addEventListener('submit', function(event) {
            const rows = document.querySelectorAll('#product-table tbody tr');
            const products = [];

            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                const categoryId = row.querySelector('input[name="category_ids[]"]').value;
                const brandId = row.querySelector('input[name="brand_ids[]"]').value;
                const productData = {
                    category: categoryId,
                    brand: brandId,
                    model: cells[2].innerText,
                    quantity: parseFloat(cells[3].innerText),
                    purchase_price: parseFloat(cells[4].innerText),
                    discount: parseFloat(cells[5].innerText),
                    gst: parseFloat(cells[6].innerText),
                    sale_price: parseFloat(cells[7].innerText),
                    online_price: parseFloat(cells[8].innerText),
                    delivery_charges: parseFloat(cells[9].innerText),
                    dealer_point: parseFloat(cells[10].innerText),
                    salesmen_point: parseFloat(cells[11].innerText),
                    total_amount: parseFloat(cells[12].innerText),
                };
                products.push(productData);
            });

            if (products.length === 0) {
                alert('Please add at least one product.');
                event.preventDefault(); // Prevent form submission
            } else {
                // Store product data in hidden input
                document.getElementById('products-input').value = JSON.stringify(products);
            }
        });

        // Calculate GST and sub amount on input change
        // document.getElementById('net_wt').addEventListener('input', () => {
        //     calculateGST();
        //     calculateSubAmount();
        // });
        // document.getElementById('rate').addEventListener('input', () => {
        //     calculateGST();
        //     calculateSubAmount();
        // });
        // document.getElementById('s_gst').addEventListener('input', calculateGST);
        // document.getElementById('c_gst').addEventListener('input', calculateGST);
        // document.getElementById('i_gst').addEventListener('input', calculateGST);
        // document.getElementById('charges').addEventListener('input', calculateSubAmount);

        // // Calculate net amount on round off change
        // document.querySelector('input[name="round_off"]').addEventListener('input', updateTotals);

        //  jQuery (needed for AJAX if not included globally) 
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categorySelect = document.getElementById('category_id');
            const gstInput = document.getElementById('gst');
            const gstAmount = document.getElementById('gst_amount');
        
            categorySelect.addEventListener('change', function() {
                const catId = this.value;
        
                if (catId) {
                    // Use Laravel route() helper for dynamic URL
                    const url = "{{ route('category.gst', ':id') }}".replace(':id', catId);
        
                    fetch(url)
                        .then(response => response.json())
                        .then(data => {
                            if (data.status == 1) {
                                gstInput.value = data.gst;
                                gstAmount.textContent = data.gst;
                            } else {
                                gstInput.value = '';
                                gstAmount.textContent = '0';
                            }
                        })
                        .catch(() => {
                            gstInput.value = '';
                            gstAmount.textContent = '0';
                            alert('Error fetching GST value!');
                        });
                } else {
                    gstInput.value = '';
                    gstAmount.textContent = '0';
                }
            });
        });
        </script>
        
    <script>
        function poputatInput() {
            let purchasePrice = document.getElementById('rate').value;
            if (purchasePrice < 500) {
                document.getElementById('sale_rate').value = 30;
                document.getElementById('online_rate').value = 10;
                document.getElementById('delivery_charge').value = 10;
                document.getElementById('dealer_point').value = 10;
                document.getElementById('salesmen_point').value = 1;
            }
            else if(purchasePrice >= 500 && purchasePrice < 5000) {
                document.getElementById('sale_rate').value = 20;
                document.getElementById('online_rate').value = 8;
                document.getElementById('delivery_charge').value = 4;
                document.getElementById('dealer_point').value = 8;
                document.getElementById('salesmen_point').value = 1;
            }
            else if(purchasePrice >= 5000 && purchasePrice < 10000) {
                document.getElementById('sale_rate').value = 10;
                document.getElementById('online_rate').value = 4;
                document.getElementById('delivery_charge').value = 3;
                document.getElementById('dealer_point').value = 3;
                document.getElementById('salesmen_point').value = 0.5;
            }
            else if(purchasePrice >= 10000 && purchasePrice < 18000) {
                document.getElementById('sale_rate').value = 7;
                document.getElementById('online_rate').value = 3;
                document.getElementById('delivery_charge').value = 1.5;
                document.getElementById('dealer_point').value = 2.5;
                document.getElementById('salesmen_point').value = 0.5;
            } else {
                document.getElementById('sale_rate').value = 5;
                document.getElementById('online_rate').value = 2;
                document.getElementById('delivery_charge').value = 1;
                document.getElementById('dealer_point').value = 2;
                document.getElementById('salesmen_point').value = 0.5;
            }
        }
    </script>
@endpush
