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

        .navbar {
            background: #2563eb;
            color: white;
            padding: 18px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h2 {
            font-size: 22px;
        }

        .logout button {
            background: white;
            color: #2563eb;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
        }

        .container {
            padding: 30px;
        }

        .welcome {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }

        .card h3 {
            margin-bottom: 10px;
        }

        .card a {
            color: #2563eb;
            text-decoration: none;
        }

        @media(max-width: 768px) {
            .cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <div class="navbar">

        <h2>User Dashboard</h2>

        <form action="{{ route('logout') }}" method="POST" class="logout">
            @csrf
            <button type="submit">Logout</button>
        </form>

    </div>

    <div class="container">

        <div class="welcome">
            <h2></h2>

            <p>
               
            </p>
        </div>

        <div class="cards">

            <div class="card">
                <h3>👤 Profile</h3>
                <p>View and update your profile.</p>
                <br>
                <a href="#">View Profile →</a>
            </div>

            <div class="card">
                <h3>📦 Orders</h3>
                <p>Check your orders.</p>
                <br>
                <a href="#">My Orders →</a>
            </div>

            <div class="card">
                <h3>⚙️ Settings</h3>
                <p>Manage your account settings.</p>
                <br>
                <a href="#">Settings →</a>
            </div>

        </div>

    </div>

</body>
</html>