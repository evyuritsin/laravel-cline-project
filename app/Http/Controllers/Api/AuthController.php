<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Request OTP code via SMS.
     */
    public function requestOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string|size:11',
        ]);

        $phone = $request->input('phone');
        $ipAddress = $request->ip();

        // Create OTP code (also invalidates previous ones, returns null if rate limited)
        $otp = OtpCode::createForPhone($phone, $ipAddress);

        if (!$otp) {
            return response()->json([
                'message' => 'Слишком много запросов. Попробуйте через минуту.',
            ], 429);
        }

        // In production, send SMS here via SMS gateway
        // For now, we'll log the code (development only)
        \Log::info("OTP for {$phone}: {$otp->code}");

        // Return success - in production, SMS would be sent by gateway
        return response()->json([
            'message' => 'Код отправлен',
            'expires_in' => 300, // 5 minutes
            // DEBUG ONLY - remove in production
            'debug_code' => $otp->code,
        ]);
    }

    /**
     * Verify OTP code and login.
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string|size:11',
            'code' => 'required|string|size:6',
        ]);

        $phone = $request->input('phone');
        $code = $request->input('code');

        // Find valid OTP for this phone
        $otp = OtpCode::where('phone', $phone)
            ->where('expires_at', '>', now())
            ->first();

        if (!$otp) {
            return response()->json([
                'message' => 'Код не найден или истёк',
            ], 404);
        }

        if (!$otp->verify($code)) {
            $remainingAttempts = 3 - $otp->attempts;
            
            if ($remainingAttempts <= 0) {
                $otp->delete();
                return response()->json([
                    'message' => 'Превышено число попыток. Запросите новый код.',
                ], 429);
            }

            return response()->json([
                'message' => 'Неверный код',
                'remaining_attempts' => $remainingAttempts,
            ], 401);
        }

        // OTP is valid - delete it
        $otp->delete();

        // Find or create user by phone
        $user = User::where('phone', $phone)->first();

        if (!$user) {
            // Create new user with phone
            $user = User::create([
                'phone' => $phone,
                'name' => 'Пользователь',
                'email' => $phone . '@sms.user',
                'password' => Hash::make(Str::random(16)),
                'tier' => 'free',
            ]);
        }

        // Create token
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Telegram login (kept for backward compatibility).
     */
    public function telegramLogin(Request $request): JsonResponse
    {
        $request->validate([
            'telegram_id' => 'required|numeric',
            'name' => 'required|string|max:255',
        ]);

        $user = User::where('telegram_id', $request->telegram_id)->first();

        if (!$user) {
            $user = User::create([
                'telegram_id' => $request->telegram_id,
                'name' => $request->name,
                'email' => $request->telegram_id . '@telegram.user',
                'tier' => 'free',
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Logout.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}