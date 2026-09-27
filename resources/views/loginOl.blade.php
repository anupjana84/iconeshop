<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Icon Computer - Login</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background: linear-gradient(135deg, #0b1120 0%, #0284c7 50%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        /* Top Navigation Bar */
        .navbar {
            background-color: #ffffff;
            padding: 12px 24px;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 22px;
            font-weight: bold;
            color: #1e293b;
        }
        .logo-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #d97706;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }
        /* Main Login Container */
        .container {
            flex: 1;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding-right: 10%;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px 30px;
            border-radius: 20px;
            width: 380px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
            text-align: center;
            position: relative;
        }
        .avatar-badge {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
            border-radius: 50%;
            margin: -75px auto 15px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            border: 4px solid white;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .login-card h2 {
            font-size: 26px;
            color: #0f172a;
            margin-bottom: 25px;
        }
        .input-group {
            margin-bottom: 18px;
            text-align: left;
        }
        .input-group input {
            width: 100%;
            padding: 14px 18px;
            border: 1px solid #cbd5e1;
            border-radius: 25px;
            font-size: 15px;
            outline: none;
            transition: all 0.3s ease;
        }
        .input-group input:focus {
            border-color: #0284c7;
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.2);
        }
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(90deg, #10b981, #0284c7);
            border: none;
            border-radius: 25px;
            color: white;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: opacity 0.3s;
        }
        .btn-login:hover {
            opacity: 0.9;
        }
        .alert-error {
            color: #ef4444;
            font-size: 14px;
            margin-bottom: 15px;
            display: none;
        }
        /* Demo Accounts Box */
        .info-box {
            margin-top: 25px;
            padding: 12px;
            background: #f1f5f9;
            border-radius: 10px;
            font-size: 12px;
            color: #334155;
            text-align: left;
        }
        /* Dashboard Styling */
        #dashboard {
            display: none;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            max-width: 500px;
            margin: auto;
            text-align: left;
        }
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 15px;
            color: white;
            font-weight: bold;
            background: #0284c7;
            font-size: 14px;
        }
        .logout-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <!-- Top Navigation Bar -->
    <div class="navbar">
        <div class="navbar-brand">
            <div class="logo-circle">আইকন</div>
            Icon Computer
        </div>
    </div>

    <!-- Login Container -->
    <div class="container" id="loginBox">
        <div class="login-card">
            <div class="avatar-badge">ICON</div>
            <h2>Login Here</h2>

            <div class="alert-error" id="errorMsg">ভুল ইমেইল অথবা পাসওয়ার্ড দিয়েছেন!</div>

            <form onsubmit="handleLogin(event)">
                <div class="input-group">
                    <input type="email" id="email" placeholder="Email Address" required>
                </div>
                <div class="input-group">
                    <input type="password" id="password" placeholder="Password" required>
                </div>
                <button type="submit" class="btn-login">Login</button>
            </form>

            <div class="info-box">
                <strong>অনুমোদিত ইউজারসমূহ:</strong><br>
                1. Admin: admin@icon.com / admin123<br>
                2. Manager: manager@icon.com / mgr12345<br>
                3. Salesman: sales@icon.com / sales123<br>
                4. Seller: seller@icon.com / sell1234
            </div>
        </div>
    </div>

    <!-- Dashboard Container (Shows after login) -->
    <div class="container" style="justify-content: center;">
        <div id="dashboard">
            <h2 id="welcomeName">স্বাগতম!</h2>
            <p style="margin: 10px 0;">আপনার ইউজার রোল: <span class="badge" id="userRole">Role</span></p>
            <hr style="margin: 15px 0; border: none; border-top: 1px solid #e2e8f0;">
            <h3>আপনার ড্যাশবোর্ড সুবিধা:</h3>
            <ul id="roleFeatures" style="margin-left: 20px; margin-top: 10px; line-height: 1.6;"></ul>
            <button class="logout-btn" onclick="handleLogout()">লগআউট (Logout)</button>
        </div>
    </div>

    <!-- Logic Engine -->
    <script>
        const users = {
            "admin@icon.com": { pass: "admin123", role: "Administrator", name: "System Administrator", features: ["সম্পূর্ণ সিস্টেম নিয়ন্ত্রণ এবং ইউজার ম্যানেজমেন্ট", "সকল সেলস এবং ইনভেন্টরি রিপোর্ট"] },
            "manager@icon.com": { pass: "mgr12345", role: "Manager", name: "Branch Manager", features: ["দোকান এবং ব্রাঞ্চ পরিচালনা", "স্টাফ ও স্টক তদারকি"] },
            "sales@icon.com": { pass: "sales123", role: "Salesman", name: "Sales Executive", features: ["নতুন বিল তৈরি ও কাস্টমার সেলস", "দৈনিক সেলস এন্ট্রি"] },
            "seller@icon.com": { pass: "sell1234", role: "Seller", name: "Vendor / Seller", features: ["পণ্য ইনভেন্টরিতে যোগ করা", "স্টক আপডেট ও নিজস্ব সেলস হিস্ট্রি"] }
        };

        function handleLogin(e) {
            e.preventDefault();
            const email = document.getElementById('email').value.trim();
            const pass = document.getElementById('password').value.trim();
            const errorMsg = document.getElementById('errorMsg');

            if (users[email] && users[email].pass === pass) {
                errorMsg.style.display = 'none';
                document.getElementById('loginBox').style.display = 'none';
                document.getElementById('dashboard').style.display = 'block';

                const user = users[email];
                document.getElementById('welcomeName').innerText = "স্বাগতম, " + user.name + "!";
                document.getElementById('userRole').innerText = user.role;

                const featuresList = document.getElementById('roleFeatures');
                featuresList.innerHTML = '';
                user.features.forEach(f => {
                    let li = document.createElement('li');
                    li.innerText = f;
                    featuresList.appendChild(li);
                });
            } else {
                errorMsg.style.display = 'block';
            }
        }

        function handleLogout() {
            document.getElementById('dashboard').style.display = 'none';
            document.getElementById('loginBox').style.display = 'flex';
            document.getElementById('email').value = '';
            document.getElementById('password').value = '';
        }
    </script>
</body>
</html> convert in middle