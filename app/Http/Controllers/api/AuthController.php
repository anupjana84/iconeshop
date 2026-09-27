<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Jobs\SendWhatsappMessage;
use App\Models\Salesmen;
use App\Models\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Validator;

class AuthController extends Controller
{
    private function formatWhatsAppNumber($phone)
    {
        $phone = preg_replace('/\D/', '', $phone);
        $phone = ltrim($phone, '0');

        if (preg_match('/^91\d{10}$/', $phone)) {
            return $phone;
        }

        if (preg_match('/^\d{10}$/', $phone)) {
            return '91' . $phone;
        }

        return '91' . $phone;
    }
    public function getUser($id)
    {
        $authUser = User::find($id);
        $details = null;
        if ($authUser->role == 'subdealer') {
            $details = Salesmen::where('user_id', '=', $authUser->id)->get();
        }
        $response = [
            'status' => 1,
            'message' => 'Profile data',
            'result' => $authUser,
            'salesman' => $details,
        ];
        return response()->json($response, 200);
    }

    public function loginUser(Request $request)
    {
        $validateUser = Validator::make(
            $request->all(),
            [
                'phone' => 'required|string|regex:/^[0-9]{10}$/',
                'password' => 'required|string|min:6',
            ]
        );
        if ($validateUser->fails()) {
            $response = [
                'status' => 0,
                'message' => 'Validation Error',
                'errors' => $validateUser->errors()
            ];
            return response()->json($response, 404);
        }

        if (Auth::attempt(['phone' => $request->phone, 'password' => $request->password])) {
            $authUser = Auth::user();
            if ($authUser->status !== 'active') {
                $authUser->tokens()->delete();
                Auth::logout();
                return response()->json([
                    'status' => 0,
                    'message' => 'You have no login access and permission'
                ], 403);
            }
            $details = null;
            if ($authUser->role == 'subdealer') {
                $details = Salesmen::where('user_id', '=', $authUser->id)->get();
            }
            $response = [
                'status' => 1,
                'message' => 'Login Success',
                'token' => $authUser->createToken("API Token")->plainTextToken,
                'token_type' => 'bearer',
                'result' => $authUser,
                'salesman' => $details,
            ];
            return response()->json($response, 200);
        } else {
            $response = [
                'status' => 0,
                'message' => 'The provided credentials are incorrect',
            ];
            return response()->json($response, 401);
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        // 🔥 Delete ALL Sanctum tokens of this user
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Logged out from all devices successfully'
        ], 200);
    }

    /**
     * Request OTP for Forgot Password (with Anti-Misuse / Rate-Limiting Protection)
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|regex:/^[0-9]{10}$/',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('phone', $request->phone)->first();

        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => 'No user account found with this phone number'
            ], 404);
        }

        $phone = $request->phone;

        // Anti-Misuse 1: 60-Second Cooldown Check (Prevents Spamming)
        if (Cache::has("forgot_cooldown_{$phone}")) {
            return response()->json([
                'status' => 0,
                'message' => 'Please wait 60 seconds before requesting another OTP.'
            ], 429);
        }

        // Anti-Misuse 2: Hourly Limit Check (Max 3 OTP requests per hour)
        $hourlyCount = Cache::get("forgot_hourly_{$phone}", 0);
        if ($hourlyCount >= 3) {
            return response()->json([
                'status' => 0,
                'message' => 'Hourly OTP request limit reached. Please try again after 1 hour.'
            ], 429);
        }

        // Generate 6-digit random OTP
        $otp = rand(100000, 999999);

        // Store OTP in Cache (Expires in 5 minutes = 300 seconds)
        Cache::put("forgot_otp_{$phone}", [
            'otp' => (string) $otp,
            'attempts' => 0
        ], 300);

        // Set Cooldown flag for 60 seconds
        Cache::put("forgot_cooldown_{$phone}", true, 60);

        // Increment Hourly Count (Expires in 1 hour = 3600 seconds)
        Cache::put("forgot_hourly_{$phone}", $hourlyCount + 1, 3600);

        // Dispatch OTP via WhatsApp
        $message = "🔐 *Password Reset Request*\n\n"
            . "Your OTP for password reset is: *{$otp}*\n\n"
            . "This OTP is valid for *5 minutes*.\n"
            . "_Do not share this OTP with anyone for your security._\n\n"
            . "*Team IconComputer* 💻";

        $rawNumber = $user->wpnumber ?? $user->phone ?? $phone;
        $number = $this->formatWhatsAppNumber($rawNumber);

        try {
            Http::connectTimeout(3)->timeout(6)->get('https://nextsms.co.in/api/whatsapp/send', [
                'receiver' => $number,
                'msgtext' => $message,
                'token' => config('services.whatsapp.token'),
            ]);
        } catch (\Throwable $e) {
            \Log::warning('Direct WhatsApp OTP send failed, fallback to queue: ' . $e->getMessage());
            SendWhatsappMessage::dispatch($number, $message);
        }

        return response()->json([
            'status' => 1,
            'message' => 'OTP sent to your WhatsApp number successfully.',
            'expires_in_seconds' => 300
        ], 200);
    }

    /**
     * Reset Password using OTP (with Brute-Force Protection)
     */
    public function resetPasswordWithOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|regex:/^[0-9]{10}$/',
            'otp' => 'required|numeric|digits:6',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $phone = $request->phone;
        $otpData = Cache::get("forgot_otp_{$phone}");

        if (!$otpData) {
            return response()->json([
                'status' => 0,
                'message' => 'OTP has expired or is invalid. Please request a new OTP.'
            ], 400);
        }

        // Anti-Misuse 3: Brute-force Protection (Max 3 failed attempts)
        if ($otpData['otp'] !== (string) $request->otp) {
            $otpData['attempts'] += 1;

            if ($otpData['attempts'] >= 3) {
                Cache::forget("forgot_otp_{$phone}");
                return response()->json([
                    'status' => 0,
                    'message' => 'Too many incorrect OTP attempts. For security reasons, this OTP has been invalidated. Please request a new OTP.'
                ], 429);
            }

            Cache::put("forgot_otp_{$phone}", $otpData, 300);
            $remaining = 3 - $otpData['attempts'];

            return response()->json([
                'status' => 0,
                'message' => "Invalid OTP. You have {$remaining} attempt(s) remaining."
            ], 400);
        }

        // Reset Password
        $user = User::where('phone', $phone)->first();
        if (!$user) {
            return response()->json([
                'status' => 0,
                'message' => 'User not found.'
            ], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Anti-Misuse 4: Revoke all existing sessions/tokens after password reset
        $user->tokens()->delete();

        // Clear OTP from Cache
        Cache::forget("forgot_otp_{$phone}");

        return response()->json([
            'status' => 1,
            'message' => 'Password has been reset successfully. Please log in with your new password.'
        ], 200);
    }
}