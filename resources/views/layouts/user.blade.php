<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/icon.ico') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <!-- SweetAlert2 & Custom Alert Enhancer -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/custom-alerts.js') }}"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>{{ $page_title ?? 'User Portal' }}</title>
    <style>
        .user-shell {
            display: flex;
            min-height: calc(100vh - 68px);
        }

        .user-menu {
            width: 230px;
            flex-shrink: 0;
            padding: 24px 14px;
            background: #1e3a8a;
        }

        .user-menu a {
            display: block;
            padding: 12px 14px;
            margin-bottom: 8px;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        .user-menu a:hover,
        .user-menu a.active {
            background: #2563eb;
        }

        .user-content {
            flex: 1;
            min-width: 0;
            padding: 24px;
        }

        .user-logout {
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

        .user-logout:hover {
            background: #dbeafe;
            color: #1e40af;
            transform: translateY(-1px);
        }

        .user-logout:focus-visible {
            outline: 3px solid #bfdbfe;
            outline-offset: 2px;
        }

        @media (max-width: 768px) {
            .user-shell {
                display: block;
            }

            .user-menu {
                width: auto;
                display: flex;
                gap: 8px;
                padding: 10px;
                overflow-x: auto;
            }

            .user-menu a {
                white-space: nowrap;
                margin-bottom: 0;
            }
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen">
    <header class="bg-blue-700 text-white px-5 py-4 flex items-center justify-between">
        <a href="{{ route('userdashboard') }}" class="font-bold text-lg">User Dashboard</a>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="user-logout">
                <span aria-hidden="true">↪</span>
                <span>Logout</span>
            </button>
        </form>
    </header>

    <div class="user-shell">
        <aside class="user-menu" aria-label="User menu">
            <a href="{{ route('userdashboard') }}">🏠 Dashboard</a>
            <a href="{{ route('userdashboard') }}#my-orders">📦 My Orders</a>
            <a href="{{ route('user.service') }}" class="active">🛠️ Service</a>
        </aside>

        <main class="user-content">
            @yield('content_page')
        </main>
    </div>

    @stack('style_link')
    @stack('extra_style')
    @stack('page_title')
    @stack('extra_js')
</body>

</html>