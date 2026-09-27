<div class="flex" >

    <!-- Sidebar -->
    <div
        class="bg-gray-800 text-white w-16 hover:w-64 transition-all ease-in-out duration-300 h-screen group flex flex-col overflow-hidden">

        <!-- Logo -->
        <div class="p-4 flex items-center gap-3">
            <img src="{{ url('images/icon.ico') }}" alt="Logo" class="w-8 h-8 rounded-full mr-2">
            {{-- <i class="fas fa-laptop-code text-xl"></i> --}}
            <span class="text-lg font-bold opacity-0 group-hover:opacity-100 whitespace-nowrap transition-opacity">Icon
                Computer</span>
        </div>

        <!-- Menu -->
        <nav class="flex overflow-y-scroll " id="sidebar">
            <ul class="space-y-1">

                <!-- Dashboard -->
                <li>
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center gap-3 px-4 py-2 hover:bg-gray-700 rounded">
                        <i class="fas fa-tachometer-alt w-6 text-center"></i>
                        <span
                            class="opacity-0 group-hover:opacity-100 whitespace-nowrap transition-opacity">Dashboard</span>
                    </a>
                </li>

                <!-- Dropdown: Purchase -->
                <li class="relative">
                    <button onclick="toggleDropdown('purchase')"
                        class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-700 rounded">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-shopping-cart w-6 text-center"></i>
                            <span
                                class="opacity-0 group-hover:opacity-100 whitespace-nowrap transition-opacity">Purchase</span>
                        </div>
                        <i id="arrow-purchase"
                            class="fas fa-chevron-down opacity-0 group-hover:opacity-100 transition-transform"></i>
                    </button>
                    <ul id="menu-purchase" class="hidden pl-12 space-y-1">
                        <li><a href="{{ route('purchase.create') }}"
                                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Create Purchase</a></li>
                        <li><a href="{{ route('purchase.list') }}"
                                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Purchase Table</a></li>
                        <li><a href="{{ route('purchase.return.list') }}"
                                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Purchase Return</a></li>
                    </ul>
                </li>

                <!-- Dropdown: Sales -->
                <li class="relative">
                    <button onclick="toggleDropdown('sales')"
                        class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-700 rounded">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-solid fa-dollar-sign w-6 text-center"></i>
                            <span
                                class="opacity-0 group-hover:opacity-100 whitespace-nowrap transition-opacity">Sales</span>
                        </div>
                        <i id="arrow-sales"
                            class="fas fa-chevron-down opacity-0 group-hover:opacity-100 transition-transform"></i>
                    </button>
                    <ul id="menu-sales" class="hidden pl-12 space-y-1">
                        <li><a href="{{ route('sale.create') }}"
                                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Create Sale</a></li>
                        <li><a href="{{ route('sale.list') }}"
                                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Sale Table</a></li>
                        <li><a href="{{ route('sale.return.list') }}"
                                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Sale Return</a></li>
                    </ul>
                </li>

                <!-- Orders -->
                <li>
                    <a href="{{ route('order.view') }}"
                        class="flex items-center gap-3 px-4 py-2 hover:bg-gray-700 rounded">
                        <i class="fas fa-list w-6 text-center"></i>
                        <span
                            class="opacity-0 group-hover:opacity-100 whitespace-nowrap transition-opacity">Orders</span>
                    </a>
                </li>

                <!-- Stock -->
                <li>
                    <a href="{{ route('emi.list') }}"
                        class="flex items-center gap-3 px-4 py-2 hover:bg-gray-700 rounded">
                        <i class="fas fa-box-open w-6 text-center"></i>
                        <span class="opacity-0 group-hover:opacity-100 whitespace-nowrap transition-opacity">EMI</span>
                    </a>
                </li>
                <!-- Ledger -->
                <li class="relative">
                    <button onclick="toggleDropdown('ledger')"
                        class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-700 rounded">
                        <div class="flex items-center gap-3">
                            <i class="fas fa fa-users w-6 text-center"></i>
                            <span
                                class="opacity-0 group-hover:opacity-100 whitespace-nowrap transition-opacity">Ledger</span>
                        </div>
                        <i id="arrow-ledger"
                            class="fas fa-chevron-down opacity-0 group-hover:opacity-100 transition-transform"></i>
                    </button>
                    <ul id="menu-ledger" class="hidden pl-12 space-y-1">
                        <li><a href="{{ route('ledger.customer') }}"
                                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Customer Ledger</a></li>
                        <li><a href="{{ route('ledger.company') }}"
                                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Company Ledger</a></li>
                        <li><a href="{{ route('ledger.subDealer') }}"
                                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Sub dealer Ledger</a></li>
                        <li><a href="{{ route('ledger.salesman') }}"
                                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Salesman Ledger</a></li>
                        <li><a href="{{ route('ledger.list') }}"
                                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">All Ledger</a></li>
                        <li><a href="{{ route('ledger.previous.entry') }}"
                                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Privious Ledger</a></li>
                    </ul>
                </li>
                {{-- <li>
                    <a href="{{ route('ledger.list') }}"
                        class="flex items-center gap-3 px-4 py-2 hover:bg-gray-700 rounded">
                        <i class="fas fa-solid fa-book w-6 text-center"></i>
                        <span
                            class="opacity-0 group-hover:opacity-100 whitespace-nowrap transition-opacity">Ledger</span>
                    </a>
                </li> --}}
<!-- Reward Point -->
<li>
    <a href="{{ route('reward.point.history') }}"
        class="flex items-center gap-3 px-4 py-2 hover:bg-gray-700 rounded">

        <i class="fas fa-solid fa-book w-6 text-center"></i>

        <span class="opacity-0 group-hover:opacity-100 whitespace-nowrap transition-opacity">

            Reward Point

        </span>

    </a>
</li>
                <!-- Dropdown: product -->
                <li class="relative">
                    <button onclick="toggleDropdown('product')"
                        class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-700 rounded">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-solid fa-cubes w-6 text-center"></i>
                            <span class="opacity-0 group-hover:opacity-100 whitespace-nowrap transition-opacity">Product
                                Management</span>
                        </div>
                        <i id="arrow-product"
                            class="fas fa-chevron-down opacity-0 group-hover:opacity-100 transition-transform"></i>
                    </button>
                    <ul id="menu-product" class="hidden pl-12 space-y-1">
                        <li><a href="{{ route('product.create') }}"
                                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Add Product</a></li>
                        <li><a href="{{ route('product.list') }}"
                                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Product List</a></li>
                        <li><a href="{{ route('product.code') }}"
                                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Product Code</a></li>
                        <li><a href="{{ route('product.empty.stock') }}"
                                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Empty Stock</a></li>
                        <li><a href="{{ route('product.old.stock') }}"
                                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Old Stock</a></li>
                    </ul>
                </li>

                <!-- Dropdown: Staff -->
                <li class="relative">
                    <button onclick="toggleDropdown('staff')"
                        class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-700 rounded">
                        <div class="flex items-center gap-3">
                            <i class="fas fa fa-users w-6 text-center"></i>
                            <span
                                class="opacity-0 group-hover:opacity-100 whitespace-nowrap transition-opacity">Staff</span>
                        </div>
                        <i id="arrow-staff"
                            class="fas fa-chevron-down opacity-0 group-hover:opacity-100 transition-transform"></i>
                    </button>
                    <ul id="menu-staff" class="hidden pl-12 space-y-1">
                        <li><a href="{{ route('users.list') }}"
                                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">User</a></li>
                        <li><a href="{{ route('customers.list') }}"
                                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Customer</a></li>
                    </ul>
                </li>

                <!-- Dropdown: Payment -->
                <li class="relative">
                    <button onclick="toggleDropdown('payment')"
                        class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-700 rounded">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-cash-register w-6 text-center"></i>
                            <span class="opacity-0 group-hover:opacity-100 whitespace-nowrap transition-opacity">Payment
                                Master</span>
                        </div>
                        <i id="arrow-payment"
                            class="fas fa-chevron-down opacity-0 group-hover:opacity-100 transition-transform"></i>
                    </button>
                    <ul id="menu-payment" class="hidden pl-12 space-y-1">
                        <li><a href="{{ route('payment.create') }}"
                                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Make Payment</a></li>
                        <li><a href="{{ route('payment.history') }}"
                                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Payment History</a></li>
                        {{-- <li><a href="{{ route('payment.history') }}"
                                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Bank Statement</a></li> --}}
                        <li><a href="{{ route('expenses.payment.list') }}"
                                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Expenses</a></li>
                        {{-- <li><a href="#" class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Product Code</a></li> --}}
                    </ul>
                </li>
                <!-- Dropdown: Report -->
                <li class="relative">
                    <button onclick="toggleDropdown('report')"
                        class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-700 rounded">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-cash-register w-6 text-center"></i>
                            <span
                                class="opacity-0 group-hover:opacity-100 whitespace-nowrap transition-opacity">Report</span>
                        </div>
                        <i id="arrow-report"
                            class="fas fa-chevron-down opacity-0 group-hover:opacity-100 transition-transform"></i>
                    </button>
                    <ul id="menu-report" class="hidden pl-12 space-y-1">
                        <li><a href="{{ route('report.customer.due') }}"
                                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Customer Due</a></li>
                        <li><a href="{{ route('report.company.due') }}"
                                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Company Due</a></li>
                        <!--<li><a href=""-->
                        <!--        class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Your Due</a></li>-->
                        <!-- <li><a href="{{ route('reports.monthly') }}"
                                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Monthly Report</a></li> -->
                        <li><a href="{{ route('reports.daily') }}"
                                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Daily Report</a></li>
                        <li><a href="{{ route('gst.reports') }}"
                                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Gst Report</a></li>
                    </ul>
                </li>
                <!-- Dropdown: Service -->
<li class="relative">
    <button onclick="toggleDropdown('service')"
        class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-700 rounded">
        <div class="flex items-center gap-3">
            <i class="fas fa-wrench w-6 text-center"></i>
            <span class="opacity-0 group-hover:opacity-100 whitespace-nowrap transition-opacity">Service</span>
        </div>
        <i id="arrow-service"
            class="fas fa-chevron-down opacity-0 group-hover:opacity-100 transition-transform"></i>
    </button>
    
    <!-- Sub Menu -->
    <ul id="menu-service" class="hidden pl-12 space-y-1">
        <li>
            <a href="{{ route('service-booking.index') }}"
                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Call Logs</a>
        </li>
        <li>
            <a href="/admin/service/settings"
                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Service Settings</a>
        </li>
    </ul>
</li>
<!-- Whatsapp -->
                <li>
                    <a href="{{ route('bulk.whatsapp') }}"
                        class="flex items-center gap-3 px-4 py-2 hover:bg-gray-700 rounded">
                        <i class="fa-brands fa-whatsapp"></i>
                        <span class="opacity-0 group-hover:opacity-100 whitespace-nowrap transition-opacity">Whatsapp</span>
                    </a>
                </li>

                <!-- Dropdown: Inventory Setup -->
                <li class="relative pb-10">
                    <button onclick="toggleDropdown('inventory')"
                        class="w-full flex items-center justify-between px-4 py-2 hover:bg-gray-700 rounded">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-clipboard-list w-6 text-center"></i>
                            <span
                                class="opacity-0 group-hover:opacity-100 whitespace-nowrap transition-opacity">Inventory
                                Setup</span>
                        </div>
                        <i id="arrow-inventory"
                            class="fas fa-chevron-down opacity-0 group-hover:opacity-100 transition-transform"></i>
                    </button>
                    <ul id="menu-inventory" class="hidden pl-12 space-y-1">
                        <li><a href="{{ route('companies.list') }}"
                                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Company</a></li>
                        <li><a href="{{ route('category.list') }}"
                                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Category</a></li>
                        <li><a href="{{ route('brand.list') }}"
                                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Brand</a></li>
                        <li><a href="{{ route('expenses.list') }}"
                                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Expenses Add</a></li>
                        <li><a href="{{ route('finance.list') }}"
                                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Finance Add</a></li>
                        <li><a href="{{ route('bank.list') }}"
                                class="block px-4 py-2 hover:bg-gray-600 rounded max-h-10">Bank Add</a></li>
                    </ul>
                </li>
                
                

            </ul>
        </nav>

        <!-- Footer -->
        <div class="p-4 bg-gray-900 flex items-center gap-3 ">
            <i class="fas fa-user-circle text-xl"></i>
           
            <div class="">
                {{-- <i class="fas fa-user-circle text-xl"></i> --}}
            </div>
        </div>
    </div>
</div>