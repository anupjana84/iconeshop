<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Icon Computer - Login</title>
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
        /* Top Navigation Bar */
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
            font-size: 14px;
        }

        /* Hero / Main Area */
        .main-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
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

        /* 4 Roles Grid */
        .roles-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            width: 100%;
            max-width: 1000px;
            margin-bottom: 40px;
        }

        .role-card {
            background-color: #1e293b;
            border-radius: 12px;
            padding: 25px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        }

        .role-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 20px rgba(0,0,0,0.5);
        }

        .role-card.admin { border: 2px solid #ef4444; }
        .role-card.admin:hover { background-color: rgba(239, 68, 68, 0.1); }

        .role-card.manager { border: 2px solid #f59e0b; }
        .role-card.manager:hover { background-color: rgba(245, 158, 11, 0.1); }

        .role-card.sales { border: 2px solid #10b981; }
        .role-card.sales:hover { background-color: rgba(16, 185, 129, 0.1); }

        .role-card.seller { border: 2px solid #8b5cf6; }
        .role-card.seller:hover { background-color: rgba(139, 92, 246, 0.1); }

        .role-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 12px;
        }
        .role-card.admin .role-title { color: #ef4444; }
        .role-card.manager .role-title { color: #f59e0b; }
        .role-card.sales .role-title { color: #10b981; }
        .role-card.seller .role-title { color: #8b5cf6; }

        .role-info {
            font-size: 12px;
            color: #94a3b8;
            line-height: 1.6;
        }
        .role-info span { color: #38bdf8; display: block; margin-top: 4px; font-weight: 600;}

        .click-hint {
            display: inline-block;
            margin-top: 15px;
            padding: 6px 12px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            font-size: 11px;
            color: #cbd5e1;
        }

        /* Central Engine Box */
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

        /* Modal / Popup Form */
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
        }

        .login-card {
            background: #ffffff;
            padding: 40px 30px;
            border-radius: 20px;
            width: 360px;
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

        .avatar-badge {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #f59e0b, #d97706);
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
        .input-group input, .input-group select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #cbd5e1;
            border-radius: 25px;
            font-size: 14px;
            outline: none;
        }
        .input-group input:focus, .input-group select:focus {
            border-color: #0284c7;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(90deg, #10b981, #0284c7);
            border: none;
            border-radius: 25px;
            color: white;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        .alert-error {
            color: #ef4444;
            font-size: 13px;
            margin-bottom: 12px;
            display: none;
        }

        /* Dashboard Screen */
        .dashboard-screen {
            display: none;
            background: #ffffff;
            color: #0f172a;
            padding: 35px;
            border-radius: 16px;
            max-width: 550px;
            width: 100%;
            text-align: left;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
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
        .success-msg {
            background-color: #d1fae5;
            color: #065f46;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 13px;
            display: none;
        }
    </style>
</head>
<body>

    <!-- Header Navbar -->
    <div class="navbar" style="justify-content: space-between; align-items: center;">
        <a href="{{ route('home') }}" class="navbar-brand" style="text-decoration: none; cursor: pointer;">
            <div class="logo-circle">আইকন</div>
            Icon Computer
        </a>
        <a href="{{ route('home') }}" style="background: #38bdf8; color: #0f172a; padding: 8px 18px; border-radius: 20px; text-decoration: none; font-weight: bold; font-size: 14px; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s;">
            🏠 Home
        </a>
    </div>

    <!-- Main Content Area -->
    <div class="main-container" id="portalHome">
        <div class="header-title">Icon Computer - Multi-Role Authentication System</div>
        <div class="header-subtitle">User Roles & Access Architecture</div>

        <!-- 4 Role Option Cards -->
        <div class="roles-grid">
            <a href="{{ route('login_page2') }}">
            <div class="role-card admin" ">
                <div class="role-title">1. Administrator</div>
                <div class="role-info">Username:<span>admin@icon.com</span></div>
                <div class="click-hint">লগইন করতে ক্লিক করুন ➔</div>
            </div>
</a>
 <a href="{{ route('login_page2') }}">
            <div class="role-card manager" >
                <div class="role-title">2. Manager</div>
                <div class="role-info">Username:<span>manager@icon.com</span></div>
                <div class="click-hint">লগইন করতে ক্লিক করুন ➔</div>
            </div>
            </a>
 <a href="{{ route('login_page2') }}">
            <div class="role-card sales" >
                <div class="role-title">3. Salesman</div>
                <div class="role-info">Username:<span>sales@icon.com</span></div>
                <div class="click-hint">লগইন করতে ক্লিক করুন ➔</div>
            </div>
 </a>
            <!--<div class="role-card seller" onclick="openLogin('Seller', 'seller@icon.com')">-->
            <!--    <div class="role-title">4. Seller</div>-->
            <!--    <div class="role-info">Username:<span>seller@icon.com</span></div>-->
            <!--    <div class="click-hint">লগইন করতে ক্লিক করুন ➔</div>-->
            <!--</div>-->
        </div>

        <!-- Engine Label Box -->
        <div class="system-engine">
            <h3>Icon Computer System Engine</h3>
            <p>Login Authentication & Role-Based Dashboard Access</p>
        </div>
    </div>

    <!-- Login Popup Window -->
    <div class="modal-overlay" id="loginModal">
        <div class="login-card">
            <span class="close-btn" onclick="closeLogin()">&times;</span>
            <div class="avatar-badge">ICON</div>
            <h2>Login Here</h2>
            <div class="selected-role-name" id="roleBadge">Role: Administrator</div>

            <div class="alert-error" id="errorMsg">ভুল পাসওয়ার্ড দিয়েছেন!</div>

            <form onsubmit="handleLogin(event)">
                <div class="input-group">
                    <input type="email" id="email" placeholder="Email Address" required readonly>
                </div>
                <div class="input-group">
                    <input type="password" id="password" placeholder="Password" required autofocus>
                </div>
                <div style="text-align: right; margin-bottom: 15px;">
                    <a href="javascript:void(0)" onclick="openForgotPasswordModal()" style="font-size: 12px; color: #0284c7; font-weight: 600; text-decoration: none;">
                        🔑 Forgot Password?
                    </a>
                </div>
                <button type="submit" class="btn-login">Open Software</button>
            </form>
        </div>
    </div>

    <!-- Forgot Password Modal Popup -->
    <div class="modal-overlay" id="forgotModal" style="display:none;">
        <div class="login-card" style="width: 380px;">
            <span class="close-btn" onclick="closeForgotPasswordModal()">&times;</span>
            <div class="avatar-badge" style="background: linear-gradient(135deg, #0284c7, #2563eb);">🔑</div>
            <h2 style="font-size: 20px; color: #0f172a; margin-bottom: 12px;">Forgot Password</h2>
            
            <!-- Step 1: Request WhatsApp OTP -->
            <div id="forgotStep1">
                <p style="font-size: 12px; color: #64748b; margin-bottom: 15px;">আপনার নিবন্ধিত ১০-ডিজিটের ফোন নম্বর লিখুন। আপনার হোয়াটসঅ্যাপে একটি সিকিউর OTP পাঠানো হবে।</p>
                <div id="forgotMsg" class="text-xs font-semibold mb-3" style="font-size: 12px; margin-bottom: 10px;"></div>
                <form onsubmit="requestForgotOtp(event)">
                    <div class="input-group">
                        <label style="font-size: 12px; font-weight: bold;">Phone Number</label>
                        <input type="text" id="forgot_phone" maxlength="10" placeholder="10-Digit Phone Number" required style="border-radius: 12px; padding: 10px 14px;">
                    </div>
                    <button type="submit" id="sendOtpBtn" class="btn-login" style="background: #0284c7; border-radius: 12px; margin-top: 5px;">Send OTP via WhatsApp</button>
                </form>
            </div>

            <!-- Step 2: Verify OTP & Reset Password -->
            <div id="forgotStep2" style="display:none;">
                <p style="font-size: 12px; color: #64748b; margin-bottom: 15px;">আপনার হোয়াটসঅ্যাপে প্রাপ্ত ৬-ডিজিটের OTP ও নতুন পাসওয়ার্ড লিখুন।</p>
                <div id="resetMsg" style="font-size: 12px; margin-bottom: 10px;"></div>
                <form onsubmit="submitResetPassword(event)">
                    <input type="hidden" id="reset_phone">
                    <div class="input-group" style="margin-bottom: 10px;">
                        <label style="font-size: 12px; font-weight: bold;">6-Digit OTP</label>
                        <input type="text" id="reset_otp" maxlength="6" placeholder="Enter OTP" required style="border-radius: 12px; padding: 10px 14px;">
                    </div>
                    <div class="input-group" style="margin-bottom: 10px;">
                        <label style="font-size: 12px; font-weight: bold;">New Password</label>
                        <input type="password" id="reset_password" minlength="6" placeholder="New Password (min 6 chars)" required style="border-radius: 12px; padding: 10px 14px;">
                    </div>
                    <div class="input-group" style="margin-bottom: 15px;">
                        <label style="font-size: 12px; font-weight: bold;">Confirm Password</label>
                        <input type="password" id="reset_password_confirmation" minlength="6" placeholder="Confirm New Password" required style="border-radius: 12px; padding: 10px 14px;">
                    </div>
                    <button type="submit" id="resetBtn" class="btn-login" style="background: #10b981; border-radius: 12px;">Reset Password</button>
                </form>
            </div>

            <div style="margin-top: 15px;">
                <a href="javascript:void(0)" onclick="closeForgotPasswordModal()" style="font-size: 12px; color: #64748b; text-decoration: none;">← Back to Login</a>
            </div>
        </div>
    </div>

    <!-- Software Dashboard Screen -->
    <div class="main-container" id="dashboardContainer" style="display:none;">
        
        <!-- Seller Specific Panel (Product Submission Form) -->
        <div class="dashboard-screen" id="sellerPanel" style="display:none;">
            <h2>Seller Portal - Product Offer Entry</h2>
            <p style="margin: 8px 0 20px 0;">আপনার ইউজার রোল: <span class="badge">Seller / Vendor</span></p>
            
            <div class="success-msg" id="sellerSuccess">পণ্যের তথ্য সফলভাবে জমা দেওয়া হয়েছে!</div>

            <form onsubmit="handleProductSubmit(event)">
                <div class="input-group">
                    <label>১) Category (ক্যাটাগরি)</label>
                    <select id="prodCategory" required>
                        <option value="">ক্যাটাগরি সিলেক্ট করুন</option>
                        <option value="Printer">Printer</option>
                        <option value="Desktop Hardware">Desktop Hardware</option>
                        <option value="Laptop Accessory">Laptop Accessory</option>
                        <option value="Networking">Networking</option>
                    </select>
                </div>

                <div class="input-group">
                    <label>২) Brand (ব্র্যান্ড)</label>
                    <input type="text" id="prodBrand" placeholder="যেমন: Canon, HP, Rapoo" required>
                </div>

                <div class="input-group">
                    <label>৩) Model (মডেল)</label>
                    <input type="text" id="prodModel" placeholder="যেমন: LBP 2900B, G3010" required>
                </div>

                <div class="input-group">
                    <label>৪) Quantity (পরিমাণ)</label>
                    <input type="number" id="prodQty" placeholder="কতগুলো বিক্রি করতে চান" min="1" required>
                </div>

                <div class="input-group">
                    <label>৫) Rate (আপনার বিক্রয় মূল্য - ₹)</label>
                    <input type="number" id="prodRate" placeholder="যে মূল্যে মালটি আমায় দিতে পারবেন" min="1" required>
                </div>

                <button type="submit" class="btn-login" style="background: linear-gradient(90deg, #8b5cf6, #0284c7); margin-top: 10px;">Submit Product Offer</button>
            </form>

            <button class="logout-btn" onclick="handleLogout()">লগআউট (Logout)</button>
        </div>

        <!-- General Panel for Admin, Manager, Sales -->
        <div class="dashboard-screen" id="generalPanel" style="display:none;">
            <h2 id="welcomeName">স্বাগতম!</h2>
            <p style="margin: 12px 0;">আপনার ইউজার রোল: <span class="badge" id="userRole" style="background:#0284c7;">Role</span></p>
            <hr style="margin: 15px 0; border: none; border-top: 1px solid #e2e8f0;">
            <h3>সফটওয়্যার ড্যাশবোর্ড এক্সেস:</h3>
            <ul id="roleFeatures" style="margin-left: 20px; margin-top: 12px; line-height: 1.8;"></ul>
            <button class="logout-btn" onclick="handleLogout()">লগআউট (Logout)</button>
        </div>

    </div>

    <!-- Javascript Logic -->
    <script>
        const users = {
            "admin@icon.com": { pass: "admin123", role: "Administrator", name: "System Administrator", features: ["সম্পূর্ণ সিস্টেম নিয়ন্ত্রণ এবং ইউজার ম্যানেজমেন্ট", "সকল সেলস এবং ইনভেন্টরি রিপোর্ট", "সিস্টেম সেটিংস ও পারমিশন কন্ট্রোল"] },
            "manager@icon.com": { pass: "mgr12345", role: "Manager", name: "Branch Manager", features: ["দোকান এবং ব্রাঞ্চ পরিচালনা", "স্টাফ ও স্টক তদারকি", "দৈনিক ক্যাশ ও সেলস ভেরিফিকেশন"] },
            "sales@icon.com": { pass: "sales123", role: "Salesman", name: "Sales Executive", features: ["নতুন বিল তৈরি ও কাস্টমার সেলস", "দৈনিক সেলস এন্ট্রি", "প্রোডাক্ট ক্যাটালগ দেখা"] },
            "seller@icon.com": { pass: "sell1234", role: "Seller", name: "Vendor / Seller", features: [] }
        };

        let currentEmail = "";

        function openLogin(roleName, email) {
            currentEmail = email;
            document.getElementById('email').value = email;
            document.getElementById('roleBadge').innerText = "Role: " + roleName;
            document.getElementById('password').value = "";
            document.getElementById('errorMsg').style.display = 'none';
            document.getElementById('loginModal').style.display = 'flex';
        }

        function closeLogin() {
            document.getElementById('loginModal').style.display = 'none';
        }

        function handleLogin(e) {
            e.preventDefault();
            const pass = document.getElementById('password').value.trim();
            const errorMsg = document.getElementById('errorMsg');

            if (users[currentEmail] && users[currentEmail].pass === pass) {
                closeLogin();
                document.getElementById('portalHome').style.display = 'none';
                document.getElementById('dashboardContainer').style.display = 'flex';

                if (currentEmail === "seller@icon.com") {
                    // Show Seller Product Entry Form
                    document.getElementById('sellerPanel').style.display = 'block';
                    document.getElementById('generalPanel').style.display = 'none';
                } else {
                    // Show General Dashboard
                    document.getElementById('sellerPanel').style.display = 'none';
                    document.getElementById('generalPanel').style.display = 'block';

                    const user = users[currentEmail];
                    document.getElementById('welcomeName').innerText = "স্বাগতম, " + user.name + "!";
                    document.getElementById('userRole').innerText = user.role;

                    const featuresList = document.getElementById('roleFeatures');
                    featuresList.innerHTML = '';
                    user.features.forEach(f => {
                        let li = document.createElement('li');
                        li.innerText = f;
                        featuresList.appendChild(li);
                    });
                }
            } else {
                errorMsg.style.display = 'block';
            }
        }

        function handleProductSubmit(e) {
            e.preventDefault();
            const successBox = document.getElementById('sellerSuccess');
            successBox.style.display = 'block';

            // Reset form fields
            document.getElementById('prodCategory').value = '';
            document.getElementById('prodBrand').value = '';
            document.getElementById('prodModel').value = '';
            document.getElementById('prodQty').value = '';
            document.getElementById('prodRate').value = '';

            setTimeout(() => {
                successBox.style.display = 'none';
            }, 3000);
        }

        function handleLogout() {
            document.getElementById('dashboardContainer').style.display = 'none';
            document.getElementById('portalHome').style.display = 'flex';
        }

        /* Forgot Password Modal Functions */
        function openForgotPasswordModal() {
            closeLogin();
            document.getElementById('forgotModal').style.display = 'flex';
            document.getElementById('forgotStep1').style.display = 'block';
            document.getElementById('forgotStep2').style.display = 'none';
            document.getElementById('forgot_phone').value = '';
            document.getElementById('reset_otp').value = '';
            document.getElementById('reset_password').value = '';
            document.getElementById('reset_password_confirmation').value = '';
            document.getElementById('forgotMsg').innerText = '';
            document.getElementById('resetMsg').innerText = '';
        }

        function closeForgotPasswordModal() {
            document.getElementById('forgotModal').style.display = 'none';
        }

        async function requestForgotOtp(e) {
            e.preventDefault();
            const phone = document.getElementById('forgot_phone').value.trim();
            const msgDiv = document.getElementById('forgotMsg');
            const sendBtn = document.getElementById('sendOtpBtn');

            if (!/^\d{10}$/.test(phone)) {
                msgDiv.innerText = '❌ অনুগ্রহ করে ১০-ডিজিটের ভ্যালিড ফোন নম্বর দিন';
                msgDiv.style.color = '#ef4444';
                return;
            }

            sendBtn.disabled = true;
            sendBtn.innerText = '⏳ Sending OTP...';
            msgDiv.innerText = '⏳ আপনার হোয়াটসঅ্যাপে OTP পাঠানো হচ্ছে...';
            msgDiv.style.color = '#0284c7';

            try {
                const res = await fetch('/api/forgot-password', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ phone: phone })
                });
                const data = await res.json();

                if (res.ok && data.status === 1) {
                    msgDiv.innerText = '✅ ' + data.message;
                    msgDiv.style.color = '#10b981';
                    
                    setTimeout(() => {
                        document.getElementById('reset_phone').value = phone;
                        document.getElementById('forgotStep1').style.display = 'none';
                        document.getElementById('forgotStep2').style.display = 'block';
                    }, 1000);
                } else {
                    msgDiv.innerText = '❌ ' + (data.message || 'OTP পাঠাতে ব্যর্থ হয়েছে');
                    msgDiv.style.color = '#ef4444';
                    sendBtn.disabled = false;
                    sendBtn.innerText = 'Send OTP via WhatsApp';
                }
            } catch (err) {
                console.error(err);
                msgDiv.innerText = '❌ সার্ভার কানেকশন ত্রুটি হয়েছে';
                msgDiv.style.color = '#ef4444';
                sendBtn.disabled = false;
                sendBtn.innerText = 'Send OTP via WhatsApp';
            }
        }

        async function submitResetPassword(e) {
            e.preventDefault();
            const phone = document.getElementById('reset_phone').value.trim();
            const otp = document.getElementById('reset_otp').value.trim();
            const password = document.getElementById('reset_password').value;
            const passwordConfirm = document.getElementById('reset_password_confirmation').value;
            const msgDiv = document.getElementById('resetMsg');
            const submitBtn = document.getElementById('resetBtn');

            if (password !== passwordConfirm) {
                msgDiv.innerText = '❌ পাসওয়ার্ড দুটি মেলেনি!';
                msgDiv.style.color = '#ef4444';
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerText = '⏳ Resetting Password...';
            msgDiv.innerText = '⏳ পাসওয়ার্ড রিসেট করা হচ্ছে...';
            msgDiv.style.color = '#0284c7';

            try {
                const res = await fetch('/api/reset-password', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({
                        phone: phone,
                        otp: otp,
                        password: password,
                        password_confirmation: passwordConfirm
                    })
                });
                const data = await res.json();

                if (res.ok && data.status === 1) {
                    msgDiv.innerText = '🎉 ' + data.message;
                    msgDiv.style.color = '#10b981';
                    setTimeout(() => {
                        closeForgotPasswordModal();
                        alert('পাসওয়ার্ড রিসেট সফল হয়েছে! এখন নতুন পাসওয়ার্ড দিয়ে লগইন করুন।');
                    }, 1200);
                } else {
                    msgDiv.innerText = '❌ ' + (data.message || 'পাসওয়ার্ড রিসেট ব্যর্থ হয়েছে');
                    msgDiv.style.color = '#ef4444';
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Reset Password';
                }
            } catch (err) {
                console.error(err);
                msgDiv.innerText = '❌ সার্ভার কানেকশন ত্রুটি হয়েছে';
                msgDiv.style.color = '#ef4444';
                submitBtn.disabled = false;
                submitBtn.innerText = 'Reset Password';
            }
        }
    </script>
</body>
</html>