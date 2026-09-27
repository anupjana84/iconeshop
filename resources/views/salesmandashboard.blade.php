<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $page_title }}</title>
    <!-- SweetAlert2 & Custom Alerts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/custom-alerts.js') }}"></script>
    <style>
        body { margin: 0; font-family: sans-serif; background: #f5f7fb; color: #172033; }
        header { padding: 20px 28px; background: #0f766e; color: white; display: flex; justify-content: space-between; align-items: center; }
        main { max-width: 960px; margin: 32px auto; padding: 0 20px; }
        section { background: white; padding: 24px; border-radius: 8px; box-shadow: 0 2px 8px #00000012; }
        button { border: 0; padding: 9px 14px; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <header><strong>Salesman Dashboard</strong><form action="{{ route('logout') }}" method="POST">@csrf<button>Logout</button></form></header>
    <main><section><h1>Salesman workspace</h1><p>Manage your assigned sales activities from here.</p></section></main>
</body>
</html>
