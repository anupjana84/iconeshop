<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Icon Computer - Seller Portal</title>
    <!-- SweetAlert2 & Custom Alerts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('js/custom-alerts.js') }}"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #0f172a;
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* =========================
           NAVBAR
        ========================== */

        .navbar {
            background-color: #1e293b;
            padding: 15px 30px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #334155;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 22px;
            font-weight: bold;
            color: #ffffff;
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
            font-size: 13px;
        }

        /* =========================
           MAIN CONTAINER
        ========================== */

        .main-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            text-align: center;
        }

        .header-title {
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #ffffff;
        }

        .header-subtitle {
            font-size: 15px;
            color: #38bdf8;
            margin-bottom: 40px;
        }

        /* =========================
           ONE ROLE CARD
        ========================== */

        .roles-grid {
            display: flex;
            justify-content: center;
            width: 100%;
            margin-bottom: 40px;
        }

        .role-card {
            width: 300px;
            min-height: 175px;

            background-color: #1e293b;

            border: 2px solid #8b5cf6;

            border-radius: 12px;

            padding: 25px 20px;

            text-align: center;

            cursor: pointer;

            transition: all 0.3s ease;

            position: relative;

            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }

        .role-card:hover {
            transform: translateY(-8px);

            background-color: rgba(139, 92, 246, 0.1);

            box-shadow: 0 12px 20px rgba(0, 0, 0, 0.5);
        }

        .role-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #8b5cf6;
        }

        .role-info {
            font-size: 13px;
            color: #94a3b8;
            line-height: 1.6;
        }

        .role-info span {
            color: #38bdf8;
            display: block;
            margin-top: 4px;
            font-weight: 600;
        }

        .click-hint {
            display: inline-block;

            margin-top: 15px;

            padding: 7px 14px;

            background: rgba(255, 255, 255, 0.1);

            border-radius: 20px;

            font-size: 11px;

            color: #cbd5e1;
        }

        /* =========================
           SYSTEM ENGINE
        ========================== */

        .system-engine {
            background-color: #1e293b;

            border: 2px solid #38bdf8;

            border-radius: 12px;

            padding: 20px 40px;

            width: 100%;

            max-width: 600px;
        }

        .system-engine h3 {
            color: #ffffff;
            font-size: 18px;
            margin-bottom: 6px;
        }

        .system-engine p {
            color: #94a3b8;
            font-size: 13px;
        }

        /* =========================
           LOGIN MODAL
        ========================== */

        .modal-overlay {
            position: fixed;

            top: 0;
            left: 0;

            width: 100%;
            height: 100%;

            background: rgba(15, 23, 42, 0.85);

            backdrop-filter: blur(5px);

            display: none;

            justify-content: center;
            align-items: center;

            z-index: 1000;

            padding: 20px;
        }

        .login-card {
            background: #ffffff;

            padding: 40px 30px;

            border-radius: 20px;

            width: 360px;

            max-width: 100%;

            box-shadow: 0 20px 25px rgba(0, 0, 0, 0.5);

            text-align: center;

            position: relative;

            color: #0f172a;
        }

        .close-btn {
            position: absolute;

            top: 15px;
            right: 20px;

            font-size: 22px;

            cursor: pointer;

            color: #64748b;
        }

        .close-btn:hover {
            color: #ef4444;
        }

        /* =========================
           AVATAR
        ========================== */

        .avatar-badge {
            width: 60px;
            height: 60px;

            background: linear-gradient(135deg, #8b5cf6, #0284c7);

            border-radius: 50%;

            margin: -65px auto 15px auto;

            display: flex;

            align-items: center;
            justify-content: center;

            color: white;

            font-weight: bold;

            border: 4px solid #ffffff;
        }

        .login-card h2 {
            font-size: 22px;
            margin-bottom: 5px;
        }

        .selected-role-name {
            font-size: 13px;

            color: #0284c7;

            font-weight: bold;

            margin-bottom: 20px;
        }

        /* =========================
           INPUT
        ========================== */

        .input-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .input-group label {
            display: block;

            font-size: 12px;

            font-weight: 600;

            color: #475569;

            margin-bottom: 5px;
        }

        .input-group input,
        .input-group select {
            width: 100%;

            padding: 12px 16px;

            border: 1px solid #cbd5e1;

            border-radius: 25px;

            font-size: 14px;

            outline: none;
        }

        .input-group input:focus,
        .input-group select:focus {
            border-color: #0284c7;
        }

        /* =========================
           LOGIN BUTTON
        ========================== */

        .btn-login {
            width: 100%;

            padding: 12px;

            background: linear-gradient(90deg, #8b5cf6, #0284c7);

            border: none;

            border-radius: 25px;

            color: white;

            font-size: 15px;

            font-weight: bold;

            cursor: pointer;
        }

        .btn-login:hover {
            opacity: 0.9;
        }

        /* =========================
           ERROR
        ========================== */

        .alert-error {
            color: #ef4444;

            font-size: 13px;

            margin-bottom: 12px;

            display: none;
        }

        /* =========================
           DASHBOARD
        ========================== */

        .dashboard-screen {
            display: none;

            background: #ffffff;

            color: #0f172a;

            padding: 35px;

            border-radius: 16px;

            max-width: 550px;

            width: 100%;

            text-align: left;

            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
        }

        .badge {
            display: inline-block;

            padding: 4px 12px;

            border-radius: 15px;

            color: white;

            font-weight: bold;

            background: #8b5cf6;

            font-size: 13px;
        }

        .logout-btn {
            margin-top: 25px;

            padding: 10px 20px;

            background: #ef4444;

            color: white;

            border: none;

            border-radius: 8px;

            cursor: pointer;

            font-weight: bold;
        }

        .logout-btn:hover {
            background: #dc2626;
        }

        /* =========================
           SUCCESS MESSAGE
        ========================== */

        .success-msg {
            background-color: #d1fae5;

            color: #065f46;

            padding: 10px;

            border-radius: 8px;

            margin-bottom: 15px;

            font-size: 13px;

            display: none;
        }

        /* =========================
           MOBILE
        ========================== */

        @media (max-width: 520px) {

            .navbar {
                padding: 12px 18px;
            }

            .navbar-brand {
                font-size: 18px;
            }

            .logo-circle {
                width: 36px;
                height: 36px;
            }

            .header-title {
                font-size: 21px;
            }

            .header-subtitle {
                font-size: 14px;
                margin-bottom: 25px;
            }

            .role-card {
                width: 100%;
                max-width: 300px;
            }

            .system-engine {
                padding: 18px 20px;
            }

            .dashboard-screen {
                padding: 25px 20px;
            }

        }
    </style>
</head>


<body>


    <!-- =========================
         NAVBAR
    ========================== -->

    <div class="navbar">

        <div class="navbar-brand">

            <div class="logo-circle">
                আইকন
            </div>

            Icon Computer

        </div>

    </div>


    <!-- =========================
         PORTAL HOME
    ========================== -->

    <div class="main-container" id="portalHome">

        <div class="header-title">
            Icon Computer - Seller Portal
        </div>

        <div class="header-subtitle">
            Seller Authentication & Product Submission
        </div>


        <!-- =========================
             ONLY ONE CARD
        ========================== -->

        <div class="roles-grid">

            <div class="role-card"
                onclick="openLogin('Seller', 'seller@icon.com')">

                <div class="role-title">
                    Seller
                </div>

                <div class="role-info">

                    Username:

                    <span>
                        seller@icon.com
                    </span>

                </div>

                <div class="click-hint">
                    লগইন করতে ক্লিক করুন ➔
                </div>

            </div>

        </div>


        <!-- =========================
             SYSTEM ENGINE
        ========================== -->

        <div class="system-engine">

            <h3>
                Icon Computer System Engine
            </h3>

            <p>
                Seller Login Authentication & Product Offer Management
            </p>

        </div>

    </div>


    <!-- =========================
         LOGIN MODAL
    ========================== -->

    <div class="modal-overlay" id="loginModal">

        <div class="login-card">

            <span class="close-btn"
                onclick="closeLogin()">

                &times;

            </span>


            <div class="avatar-badge">
                ICON
            </div>


            <h2>
                Login Here
            </h2>


            <div class="selected-role-name"
                id="roleBadge">

                Role: Seller

            </div>


            <div class="alert-error"
                id="errorMsg">

                ভুল পাসওয়ার্ড দিয়েছেন!

            </div>


            <form onsubmit="handleLogin(event)">

                <div class="input-group">

                    <input
                        type="email"
                        id="email"
                        placeholder="Email Address"
                        required
                        readonly>

                </div>


                <div class="input-group">

                    <input
                        type="password"
                        id="password"
                        placeholder="Password"
                        required
                        autofocus>

                </div>


                <button
                    type="submit"
                    class="btn-login">

                    Open Software

                </button>

            </form>

        </div>

    </div>


    <!-- =========================
         DASHBOARD
    ========================== -->

    <div class="main-container"
        id="dashboardContainer"
        style="display:none;">


        <!-- =========================
             SELLER PANEL
        ========================== -->

        <div class="dashboard-screen"
            id="sellerPanel">


            <h2>
                Seller Portal
            </h2>


            <p style="margin: 8px 0 20px 0;">

                আপনার ইউজার রোল:

                <span class="badge">
                    Seller / Vendor
                </span>

            </p>


            <!-- SUCCESS MESSAGE -->

            <div class="success-msg"
                id="sellerSuccess">

                পণ্যের তথ্য সফলভাবে জমা দেওয়া হয়েছে!

            </div>


            <!-- PRODUCT FORM -->

            <form onsubmit="handleProductSubmit(event)">


                <!-- CATEGORY -->

                <div class="input-group">

                    <label>
                        ১) Category (ক্যাটাগরি)
                    </label>

                    <select
                        id="prodCategory"
                        required>

                        <option value="">
                            ক্যাটাগরি সিলেক্ট করুন
                        </option>

                        <option value="Printer">
                            Printer
                        </option>

                        <option value="Desktop Hardware">
                            Desktop Hardware
                        </option>

                        <option value="Laptop Accessory">
                            Laptop Accessory
                        </option>

                        <option value="Networking">
                            Networking
                        </option>

                    </select>

                </div>


                <!-- BRAND -->

                <div class="input-group">

                    <label>
                        ২) Brand (ব্র্যান্ড)
                    </label>

                    <input
                        type="text"
                        id="prodBrand"
                        placeholder="যেমন: Canon, HP, Rapoo"
                        required>

                </div>


                <!-- MODEL -->

                <div class="input-group">

                    <label>
                        ৩) Model (মডেল)
                    </label>

                    <input
                        type="text"
                        id="prodModel"
                        placeholder="যেমন: LBP 2900B, G3010"
                        required>

                </div>


                <!-- QUANTITY -->

                <div class="input-group">

                    <label>
                        ৪) Quantity (পরিমাণ)
                    </label>

                    <input
                        type="number"
                        id="prodQty"
                        placeholder="কতগুলো বিক্রি করতে চান"
                        min="1"
                        required>

                </div>


                <!-- RATE -->

                <div class="input-group">

                    <label>
                        ৫) Rate (আপনার বিক্রয় মূল্য - ₹)
                    </label>

                    <input
                        type="number"
                        id="prodRate"
                        placeholder="যে মূল্যে মালটি আমায় দিতে পারবেন"
                        min="1"
                        required>

                </div>


                <!-- SUBMIT -->

                <button
                    type="submit"
                    class="btn-login">

                    Submit Product Offer

                </button>

            </form>


            <!-- LOGOUT -->

            <button
                class="logout-btn"
                onclick="handleLogout()">

                লগআউট (Logout)

            </button>

        </div>

    </div>


    <!-- =========================
         JAVASCRIPT
    ========================== -->

    <script>

        /* =========================
           SELLER LOGIN DETAILS
        ========================== */

        const user = {

            email: "seller@icon.com",

            password: "sell1234",

            role: "Seller",

            name: "Vendor / Seller"

        };


        let currentEmail = "";


        /* =========================
           OPEN LOGIN
        ========================== */

        function openLogin(roleName, email) {

            currentEmail = email;


            document.getElementById("email").value =
                email;


            document.getElementById("roleBadge").innerText =
                "Role: " + roleName;


            document.getElementById("password").value =
                "";


            document.getElementById("errorMsg").style.display =
                "none";


            document.getElementById("loginModal").style.display =
                "flex";


            setTimeout(function () {

                document.getElementById("password").focus();

            }, 100);

        }


        /* =========================
           CLOSE LOGIN
        ========================== */

        function closeLogin() {

            document.getElementById("loginModal").style.display =
                "none";

        }


        /* =========================
           LOGIN
        ========================== */

        function handleLogin(e) {

            e.preventDefault();


            const password =
                document.getElementById("password").value.trim();


            const errorMsg =
                document.getElementById("errorMsg");


            if (
                currentEmail === user.email &&
                password === user.password
            ) {

                /* CLOSE MODAL */

                closeLogin();


                /* HIDE HOME */

                document.getElementById("portalHome").style.display =
                    "none";


                /* SHOW DASHBOARD */

                document.getElementById("dashboardContainer").style.display =
                    "flex";


                /* SHOW SELLER PANEL */

                document.getElementById("sellerPanel").style.display =
                    "block";

            }

            else {

                errorMsg.style.display =
                    "block";

            }

        }


        /* =========================
           PRODUCT SUBMIT
        ========================== */

        function handleProductSubmit(e) {

            e.preventDefault();


            const successBox =
                document.getElementById("sellerSuccess");


            successBox.style.display =
                "block";


            /* RESET FORM */

            document.getElementById("prodCategory").value =
                "";

            document.getElementById("prodBrand").value =
                "";

            document.getElementById("prodModel").value =
                "";

            document.getElementById("prodQty").value =
                "";

            document.getElementById("prodRate").value =
                "";


            /* HIDE SUCCESS MESSAGE */

            setTimeout(function () {

                successBox.style.display =
                    "none";

            }, 3000);

        }


        /* =========================
           LOGOUT
        ========================== */

        function handleLogout() {

            /* HIDE DASHBOARD */

            document.getElementById("dashboardContainer").style.display =
                "none";


            /* HIDE SELLER PANEL */

            document.getElementById("sellerPanel").style.display =
                "none";


            /* SHOW HOME */

            document.getElementById("portalHome").style.display =
                "flex";


            /* RESET CURRENT USER */

            currentEmail = "";

        }


        /* =========================
           CLOSE MODAL BY CLICKING
           OUTSIDE LOGIN BOX
        ========================== */

        document.getElementById("loginModal")
            .addEventListener("click", function (e) {

                if (e.target === this) {

                    closeLogin();

                }

            });


        /* =========================
           ESC KEY CLOSE MODAL
        ========================== */

        document.addEventListener("keydown", function (e) {

            if (e.key === "Escape") {

                closeLogin();

            }

        });

    </script>

</body>

</html>