<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <!-- SweetAlert2 & Custom Alerts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/custom-alerts.js') }}"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f6fa;
        }

        /* FIXED NAVBAR */
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 68px;
            z-index: 1000;
            background: #2563eb;
            color: white;
            padding: 0 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        }

        .navbar h2 {
            font-size: 22px;
        }

        .logout button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #ffffff;
            color: #1d4ed8;
            border: 1px solid rgba(255, 255, 255, 0.7);
            padding: 9px 16px;
            border-radius: 7px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease;
        }

        .logout button:hover {
            background: #dbeafe;
            color: #1e40af;
            transform: translateY(-1px);
        }

        .logout button:focus-visible {
            outline: 3px solid #bfdbfe;
            outline-offset: 2px;
        }

        .page-layout {
            display: flex;
            padding-top: 68px;
            min-height: 100vh;
        }

        /* FIXED SIDEBAR */
        .side-menu {
            position: fixed;
            top: 68px;
            left: 0;
            bottom: 0;
            width: 230px;
            padding: 24px 14px;
            background: #1e3a8a;
            z-index: 990;
            overflow-y: auto;
        }

        .side-menu a {
            display: block;
            padding: 12px 14px;
            margin-bottom: 8px;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .side-menu a:hover,
        .side-menu a.active {
            background: #2563eb;
        }

        /* MAIN CONTAINER OFFSET FOR FIXED SIDEBAR */
        .container {
            margin-left: 230px;
            padding: 30px;
            flex: 1;
            min-width: 0;
        }

        .welcome {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .welcome h2 {
            font-size: 22px;
            color: #1e293b;
            margin-bottom: 6px;
        }

        .welcome p {
            color: #64748b;
            font-size: 14px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }

        .card h3 {
            margin-bottom: 10px;
            font-size: 17px;
            color: #1e293b;
        }

        .card a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
        }

        /* TAB CONTENT SECTIONS */
        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* REWARDS SECTION */
        .rewards-section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 25px;
        }

        .rewards-section h2 {
            margin-bottom: 18px;
            font-size: 20px;
            color: #1e293b;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .reward-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .reward-stat-card {
            padding: 18px;
            border-radius: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .reward-stat-card.featured {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            border: none;
        }

        .reward-stat-card.featured label,
        .reward-stat-card.featured .val {
            color: white !important;
        }

        .reward-stat-card label {
            display: block;
            font-size: 13px;
            color: #64748b;
            margin-bottom: 6px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .reward-stat-card .val {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
        }

        .reward-table-wrapper {
            overflow-x: auto;
        }

        .reward-table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            margin-top: 10px;
        }

        .reward-table th, 
        .reward-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }

        .reward-table th {
            background: #f1f5f9;
            color: #475569;
            font-weight: 700;
        }

        .badge-earn {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            background: #dcfce7;
            color: #15803d;
            font-size: 12px;
            font-weight: 700;
        }

        .badge-redeem {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            background: #fee2e2;
            color: #b91c1c;
            font-size: 12px;
            font-weight: 700;
        }

        .badge-refund {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            background: #dbeafe;
            color: #1d4ed8;
            font-size: 12px;
            font-weight: 700;
        }

        .orders-section {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 25px;
        }

        .orders-section h2 {
            margin-bottom: 15px;
            font-size: 20px;
            color: #1e293b;
        }

        .order-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            margin-bottom: 20px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }

        .order-card-header {
            background: #f8fafc;
            padding: 14px 18px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .order-card-header .order-info {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .order-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            text-transform: capitalize;
        }

        .status-pending { background: #fef3c7; color: #d97706; }
        .status-delivered { background: #dcfce7; color: #15803d; }
        .status-processing { background: #dbeafe; color: #1d4ed8; }
        .status-cancelled { background: #fee2e2; color: #b91c1c; }

        .order-items-list {
            padding: 10px 18px;
        }

        .order-item-row {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 14px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .order-item-row:last-child {
            border-bottom: none;
        }

        .order-item-img {
            width: 65px;
            height: 65px;
            border-radius: 10px;
            object-fit: contain;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 4px;
            flex-shrink: 0;
        }

        .order-item-details {
            flex: 1;
            min-width: 0;
        }

        .order-item-title {
            font-size: 15px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 5px;
        }

        .order-item-meta {
            font-size: 13px;
            color: #64748b;
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .order-item-price {
            font-size: 16px;
            font-weight: 800;
            color: #059669;
            text-align: right;
            flex-shrink: 0;
        }

        .order-card-footer {
            background: #f8fafc;
            padding: 12px 18px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            font-size: 14px;
        }

        .empty-orders,
        .empty-rewards {
            color: #6b7280;
        }

        @media(max-width: 768px) {
            .navbar {
                padding: 0 15px;
            }

            .side-menu {
                position: fixed;
                top: 68px;
                left: 0;
                right: 0;
                bottom: auto;
                width: 100%;
                height: 54px;
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 0 10px;
                overflow-x: auto;
                white-space: nowrap;
            }

            .side-menu a {
                display: inline-block;
                margin-bottom: 0;
                padding: 8px 12px;
                font-size: 13px;
            }

            .container {
                margin-left: 0;
                margin-top: 54px;
                padding: 15px;
            }

            .cards {
                grid-template-columns: 1fr;
            }

            .order-row {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>

<body>

    <!-- FIXED NAVBAR -->
    <div class="navbar">
        <div style="display: flex; align-items: center; gap: 15px;">
            <a href="{{ route('home') }}" style="color: white; text-decoration: none; font-size: 14px; font-weight: 600; background: rgba(255, 255, 255, 0.2); padding: 8px 14px; border-radius: 7px; display: inline-flex; align-items: center; gap: 6px;">
                <span>🛒</span> <span>Visit Store</span>
            </a>
            <h2>User Dashboard</h2>
        </div>

        <form action="{{ route('logout') }}" method="POST" class="logout">
            @csrf
            <button type="submit">
                <span aria-hidden="true">↪</span>
                <span>Logout</span>
            </button>
        </form>
    </div>

    <div class="page-layout">
        <!-- FIXED SIDEBAR -->
        <aside class="side-menu" aria-label="User menu">
            <a href="{{ route('home') }}" style="background: #16a34a; font-weight: bold; margin-bottom: 12px; display: flex; align-items: center; gap: 6px;">🛒 Shop / Home</a>
            <a data-tab="overview" class="tab-link active">🏠 Overview</a>
            <a data-tab="rewards" class="tab-link">🏆 My Rewards</a>
            <a data-tab="orders" class="tab-link">📦 My Orders</a>
            <a href="{{ route('user.service') }}">🛠️ Service</a>
        </aside>

        <div class="container">

            <!-- OVERVIEW TAB -->
            <div id="tab-overview" class="tab-content active">
                <div class="welcome">
                    <h2>Welcome, {{ auth()->user()->name ?? 'Customer' }}! 👋</h2>
                    <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-top: 8px; color: #475569; font-size: 14px; font-weight: 500;">
                        <span>👤 <strong>Name:</strong> {{ auth()->user()->name ?? 'N/A' }}</span>
                        <span>📧 <strong>Email:</strong> {{ auth()->user()->email ?? 'N/A' }}</span>
                        <span>📱 <strong>Phone:</strong> {{ auth()->user()->phone ?? 'N/A' }}</span>
                    </div>
                </div>

                <div class="cards">
                    <div class="card" style="border-top: 4px solid #16a34a;">
                        <h3>🛒 Place New Order</h3>
                        <p style="color: #64748b; font-size: 14px; margin: 6px 0 12px 0;">Browse products on homepage and order easily.</p>
                        <a href="{{ route('home') }}" style="color: #16a34a; font-weight: 700;">Go to Store →</a>
                    </div>

                    <div class="card" style="border-top: 4px solid #10b981;">
                        <h3>🏆 Reward Points</h3>
                        <p style="font-size: 26px; font-weight: 800; color: #059669; margin: 6px 0;">
                            {{ round($reward->balance ?? 0, 2) }} <span style="font-size: 14px; font-weight: 600; color: #64748b;">pts</span>
                        </p>
                        <a data-tab="rewards" class="tab-link">View Reward History →</a>
                    </div>

                    <div class="card" style="border-top: 4px solid #2563eb;">
                        <h3>📦 My Orders</h3>
                        <p style="font-size: 26px; font-weight: 800; color: #2563eb; margin: 6px 0;">
                            {{ $orders->count() }} <span style="font-size: 14px; font-weight: 600; color: #64748b;">orders</span>
                        </p>
                        <a data-tab="orders" class="tab-link">View Orders →</a>
                    </div>

                    <div class="card" style="border-top: 4px solid #8b5cf6;">
                        <h3>🛠️ Service Support</h3>
                        <p style="color: #64748b; font-size: 14px; margin: 6px 0 12px 0;">Book or track your service requests easily.</p>
                        <a href="{{ route('user.service') }}">Open Service →</a>
                    </div>
                </div>
            </div>

            <!-- REWARDS TAB -->
            <div id="tab-rewards" class="tab-content">
                <div class="rewards-section">
                    <h2>🏆 Reward Point Statement</h2>

                    <div class="reward-stats-grid">
                        <div class="reward-stat-card featured">
                            <label>Customer Name</label>
                            <div class="val" style="font-size: 18px;">{{ $reward->customer_name ?? auth()->user()->name ?? 'N/A' }}</div>
                        </div>

                        <div class="reward-stat-card">
                            <label>Mobile</label>
                            <div class="val" style="font-size: 18px; color: #475569;">{{ $reward->mobile ?? auth()->user()->phone ?? 'N/A' }}</div>
                        </div>

                        <div class="reward-stat-card">
                            <label>Balance</label>
                            <div class="val" style="color: #16a34a;">{{ round($reward->balance ?? 0, 2) }}</div>
                        </div>

                        <div class="reward-stat-card">
                            <label>Total Earned</label>
                            <div class="val" style="color: #2563eb;">{{ round($reward->total_earned ?? 0, 2) }}</div>
                        </div>
                    </div>

                    <h3 style="font-size: 16px; color: #334155; margin-bottom: 10px;">Transaction Details</h3>

                    <div class="reward-table-wrapper">
                        @if(isset($transactions) && count($transactions) > 0)
                            <table class="reward-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Invoice No</th>
                                        <th>Type</th>
                                        <th>Points</th>
                                        <th>Note</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $item)
                                        <tr>
                                            <td>{{ $item->created_at ? $item->created_at->format('d M Y, h:i A') : '-' }}</td>
                                            <td>
                                                @php
                                                    $invoice = '';
                                                    if(!empty($item->sale_id)){
                                                        $sale = \App\Models\Sale::find($item->sale_id);
                                                        $invoice = $sale ? $sale->invoice_number : '';
                                                    }
                                                @endphp
                                                {{ $invoice ?: '-' }}
                                            </td>
                                            <td>
                                                @if(in_array(strtolower($item->transaction_type ?? ''), ['earn', 'earned']))
                                                    <span class="badge-earn">Earned</span>
                                                @elseif(in_array(strtolower($item->transaction_type ?? ''), ['redeem', 'used', 'use']))
                                                    <span class="badge-redeem">Redeemed</span>
                                                @elseif(in_array(strtolower($item->transaction_type ?? ''), ['refund', 'refunded']))
                                                    <span class="badge-refund">Refunded</span>
                                                @else
                                                    <span class="badge-earn">{{ ucfirst($item->transaction_type ?? 'Earned') }}</span>
                                                @endif
                                            </td>
                                            <td style="font-weight: 700; color: {{ in_array(strtolower($item->transaction_type ?? ''), ['redeem', 'used', 'use']) ? '#dc2626' : '#16a34a' }};">
                                                {{ round($item->points, 2) }}
                                            </td>
                                            <td>{{ $item->note ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div style="margin-top: 15px;">
                                {{ $transactions->links() }}
                            </div>
                        @else
                            <p class="empty-rewards" style="padding: 10px 0;">No reward point transactions found for this account.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- ORDERS TAB -->
            <div id="tab-orders" class="tab-content">
                <div class="orders-section">
                    <h2>📦 My Orders</h2>

                    @forelse ($orders as $order)
                        <div class="order-card">
                            <div class="order-card-header">
                                <div class="order-info">
                                    <strong style="font-size: 16px; color: #1e293b;">Order #{{ $order->id }}</strong>
                                    <span style="color: #64748b; font-size: 13px;">📅 {{ $order->created_at ? $order->created_at->format('d M Y, h:i A') : 'N/A' }}</span>
                                </div>
                                <div>
                                    @php
                                        $st = strtolower($order->order_status ?? 'pending');
                                        $statusClass = 'status-pending';
                                        if (in_array($st, ['delivered', 'completed'])) $statusClass = 'status-delivered';
                                        elseif (in_array($st, ['processing', 'shipped', 'dispatch'])) $statusClass = 'status-processing';
                                        elseif (in_array($st, ['cancelled', 'cancel', 'failed'])) $statusClass = 'status-cancelled';
                                    @endphp
                                    <span class="order-badge {{ $statusClass }}">{{ ucfirst($order->order_status ?? 'Pending') }}</span>
                                </div>
                            </div>

                            <div class="order-items-list">
                                @php $orderGrandTotal = 0; @endphp
                                @foreach($order->orderItems as $item)
                                    @php
                                        $product = $item->product;
                                        $pd = $product->details ?? null;
                                        $thumb = $pd->thumbnail_image ?? null;
                                        $productName = $product ? trim(($product->category->name ?? '') . ' ' . ($product->brand->name ?? '') . ' ' . $product->model) : 'Product Item';
                                        $itemTotal = $item->total ?? (($item->price * $item->quantity) + ($item->delivery_charges ?? 0));
                                        $orderGrandTotal += $itemTotal;
                                    @endphp
                                    <div class="order-item-row">
                                        @if($thumb)
                                            <img src="{{ $thumb }}" alt="{{ $productName }}" class="order-item-img">
                                        @else
                                            <div class="order-item-img" style="display:flex;align-items:center;justify-content:center;font-size:24px;">📦</div>
                                        @endif
                                        <div class="order-item-details">
                                            <div class="order-item-title">{{ $productName }}</div>
                                            <div class="order-item-meta">
                                                <span>Qty: <strong>{{ $item->quantity }}</strong></span>
                                                <span>Price: <strong>₹{{ number_format($item->price, 2) }}</strong></span>
                                                @if(($item->delivery_charges ?? 0) > 0)
                                                    <span>Delivery: <strong>₹{{ number_format($item->delivery_charges, 2) }}</strong></span>
                                                @endif
                                                @if($item->price == 0)
                                                    <span style="color: #d97706; font-weight: 700;">🎁 Free Gift</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="order-item-price">
                                            ₹{{ number_format($itemTotal, 2) }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="order-card-footer">
                                <span style="color: #64748b; font-weight: 500;">
                                    Total Items: <strong>{{ $order->orderItems->sum('quantity') }}</strong>
                                </span>
                                <span style="font-size: 16px; font-weight: 800; color: #1e293b;">
                                    Grand Total: <strong style="color: #2563eb;">₹{{ number_format($orderGrandTotal, 2) }}</strong>
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="empty-orders">No orders found for this account.</p>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    <!-- TAB SWITCHING SCRIPT -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navLinks = document.querySelectorAll('.tab-link');
            const tabContents = document.querySelectorAll('.tab-content');

            function switchTab(tabId) {
                // Update active class on tab buttons
                navLinks.forEach(link => {
                    if (link.getAttribute('data-tab') === tabId) {
                        link.classList.add('active');
                    } else {
                        link.classList.remove('active');
                    }
                });

                // Display selected tab content
                tabContents.forEach(content => {
                    if (content.id === 'tab-' + tabId) {
                        content.classList.add('active');
                    } else {
                        content.classList.remove('active');
                    }
                });

                // Update hash in URL
                if (history.pushState) {
                    history.pushState(null, null, '#' + tabId);
                } else {
                    location.hash = '#' + tabId;
                }
            }

            // Click handler for tab links
            document.addEventListener('click', function(e) {
                const link = e.target.closest('.tab-link');
                if (link) {
                    const tabId = link.getAttribute('data-tab');
                    if (tabId && document.getElementById('tab-' + tabId)) {
                        e.preventDefault();
                        switchTab(tabId);
                    }
                }
            });

            // Initial tab selection based on URL hash
            let initialTab = 'overview';
            const hash = window.location.hash.replace('#', '');
            if (hash && (hash === 'rewards' || hash === 'orders' || hash === 'overview')) {
                initialTab = hash;
            }
            switchTab(initialTab);
        });
    </script>

</body>
</html>