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
            background: linear-gradient(135deg, #0b1120 0%, #0284c7 50%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
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
        .container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px 30px;
            border-radius: 20px;
            width: 100%;
            max-width: 380px;
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
            background-color: #fee2e2;
            color: #ef4444;
            padding: 10px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 15px;
            text-align: center;
            border: 1px solid #fca5a5;
        }
    </style>
</head>
<body>

    <div class="navbar" style="justify-content: space-between;">
        <a href="{{ route('home') }}" class="navbar-brand" style="text-decoration: none; cursor: pointer;">
            <div class="logo-circle">আইকন</div>
            Icon Computer
        </a>
        <a href="{{ route('home') }}" style="background: #0284c7; color: #ffffff; padding: 8px 16px; border-radius: 20px; text-decoration: none; font-weight: bold; font-size: 14px; display: inline-flex; align-items: center; gap: 6px; transition: opacity 0.2s;">
            🏠 Home
        </a>
    </div>

    <div class="container">
        <div class="login-card">
            <div class="avatar-badge">ICON</div>
            <h2>Login Here</h2>

            {{-- Flash error messages from controller --}}
            @if (session('fail'))
                <div class="alert-error">{{ session('fail') }}</div>
            @endif

            {{-- Validation errors --}}
            @if ($errors->any())
                <div class="alert-error">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="input-group">
                    <input type="text" name="phone" id="login_phone" value="{{ old('phone') }}" placeholder="Phone Number" required autofocus>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                </div>
                <div style="text-align: right; margin-bottom: 15px;">
                    <a href="javascript:void(0)" onclick="openForgotPasswordModal()" style="font-size: 13px; color: #0284c7; font-weight: 600; text-decoration: none;">
                        🔑 Forgot Password?
                    </a>
                </div>
                <button type="submit" class="btn-login">Login</button>
            </form>

            <div style="margin-top: 20px;">
                <a href="{{ route('home') }}" style="color: #0284c7; text-decoration: none; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px;">
                    ← Back to Home Page
                </a>
            </div>
        </div>
    </div>

    <!-- Forgot Password Modal Popup -->
    <div id="forgotModal" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(5px); justify-content: center; align-items: center; z-index: 1000;">
        <div class="login-card" style="width: 380px; background: white; border-radius: 20px; padding: 30px; text-align: center; position: relative;">
            <span onclick="closeForgotPasswordModal()" style="position: absolute; top: 15px; right: 20px; font-size: 22px; cursor: pointer; color: #64748b;">&times;</span>
            <div class="avatar-badge" style="background: linear-gradient(135deg, #0284c7, #2563eb);">🔑</div>
            <h2 style="font-size: 20px; color: #0f172a; margin-bottom: 12px;">Forgot Password</h2>
            
            <!-- Step 1: Request WhatsApp OTP -->
            <div id="forgotStep1">
                <p style="font-size: 12px; color: #64748b; margin-bottom: 15px;">আপনার নিবন্ধিত ১০-ডিজিটের ফোন নম্বর লিখুন। আপনার হোয়াটসঅ্যাপে একটি সিকিউর OTP পাঠানো হবে।</p>
                <div id="forgotMsg" style="font-size: 12px; margin-bottom: 10px;"></div>
                <form onsubmit="requestForgotOtp(event)">
                    <div class="input-group">
                        <label style="font-size: 12px; font-weight: bold; color: #334155;">Phone Number</label>
                        <input type="text" id="forgot_phone" maxlength="10" placeholder="10-Digit Phone Number" required style="border-radius: 12px; padding: 10px 14px; width: 100%; border: 1px solid #cbd5e1;">
                    </div>
                    <button type="submit" id="sendOtpBtn" class="btn-login" style="background: #0284c7; border-radius: 12px; margin-top: 5px; width: 100%; padding: 12px; color: white; font-weight: bold; border: none; cursor: pointer;">Send OTP via WhatsApp</button>
                </form>
            </div>

            <!-- Step 2: Verify OTP & Reset Password -->
            <div id="forgotStep2" style="display:none;">
                <p style="font-size: 12px; color: #64748b; margin-bottom: 15px;">আপনার হোয়াটসঅ্যাপে প্রাপ্ত ৬-ডিজিটের OTP ও নতুন পাসওয়ার্ড লিখুন।</p>
                <div id="resetMsg" style="font-size: 12px; margin-bottom: 10px;"></div>
                <form onsubmit="submitResetPassword(event)">
                    <input type="hidden" id="reset_phone">
                    <div class="input-group" style="margin-bottom: 10px;">
                        <label style="font-size: 12px; font-weight: bold; color: #334155;">6-Digit OTP</label>
                        <input type="text" id="reset_otp" maxlength="6" placeholder="Enter OTP" required style="border-radius: 12px; padding: 10px 14px; width: 100%; border: 1px solid #cbd5e1;">
                    </div>
                    <div class="input-group" style="margin-bottom: 10px;">
                        <label style="font-size: 12px; font-weight: bold; color: #334155;">New Password</label>
                        <input type="password" id="reset_password" minlength="6" placeholder="New Password (min 6 chars)" required style="border-radius: 12px; padding: 10px 14px; width: 100%; border: 1px solid #cbd5e1;">
                    </div>
                    <div class="input-group" style="margin-bottom: 15px;">
                        <label style="font-size: 12px; font-weight: bold; color: #334155;">Confirm Password</label>
                        <input type="password" id="reset_password_confirmation" minlength="6" placeholder="Confirm New Password" required style="border-radius: 12px; padding: 10px 14px; width: 100%; border: 1px solid #cbd5e1;">
                    </div>
                    <button type="submit" id="resetBtn" class="btn-login" style="background: #10b981; border-radius: 12px; width: 100%; padding: 12px; color: white; font-weight: bold; border: none; cursor: pointer;">Reset Password</button>
                </form>
            </div>

            <div style="margin-top: 15px;">
                <a href="javascript:void(0)" onclick="closeForgotPasswordModal()" style="font-size: 12px; color: #64748b; text-decoration: none;">← Back to Login</a>
            </div>
        </div>
    </div>

    <script>
        function openForgotPasswordModal() {
            const loginPhone = document.getElementById('login_phone');
            if (loginPhone && loginPhone.value.trim() !== '') {
                document.getElementById('forgot_phone').value = loginPhone.value.trim();
            }
            document.getElementById('forgotModal').style.display = 'flex';
            document.getElementById('forgotStep1').style.display = 'block';
            document.getElementById('forgotStep2').style.display = 'none';
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