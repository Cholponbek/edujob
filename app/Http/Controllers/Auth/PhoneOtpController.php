<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PhoneOtpCode;
use App\Models\User;
use App\Services\Sms\SmsGateway;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PhoneOtpController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Login');
    }

    /**
     * Отправляет одноразовый код на телефон. Один и тот же флоу
     * обслуживает и вход, и неявную регистрацию — учётная запись
     * создаётся при первом успешном подтверждении кода (verify()).
     */
    public function requestCode(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $phone = $this->normalizePhone($data['phone']);

        $sentToday = PhoneOtpCode::where('phone', $phone)
            ->where('created_at', '>=', now()->startOfDay())
            ->count();

        if ($sentToday >= config('sms.otp_rate_limit_per_day')) {
            throw ValidationException::withMessages([
                'phone' => 'Превышен дневной лимит кодов на этот номер. Попробуйте завтра.',
            ]);
        }

        $isTestNumber = in_array($phone, config('sms.test_phone_numbers'), true);
        $code = $isTestNumber ? config('sms.test_otp_code') : (string) random_int(100000, 999999);

        PhoneOtpCode::create([
            'phone' => $phone,
            'code_hash' => Hash::make($code),
            'expires_at' => now()->addMinutes(config('sms.otp_ttl_minutes')),
        ]);

        if (! $isTestNumber) {
            app(SmsGateway::class)->send($phone, "EduJob: ваш код подтверждения — {$code}");
        }

        return back()->with('status', 'otp-sent')->with('phone', $phone);
    }

    public function verify(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:20'],
            'code' => ['required', 'string'],
        ]);

        $phone = $this->normalizePhone($data['phone']);

        $otp = PhoneOtpCode::where('phone', $phone)
            ->whereNull('consumed_at')
            ->latest()
            ->first();

        if (! $otp || $otp->isExpired() || $otp->attempts >= config('sms.otp_max_attempts')) {
            throw ValidationException::withMessages([
                'code' => 'Код недействителен или истёк. Запросите новый.',
            ]);
        }

        if (! $otp->matches($data['code'])) {
            $otp->increment('attempts');

            throw ValidationException::withMessages([
                'code' => 'Неверный код.',
            ]);
        }

        $otp->update(['consumed_at' => now()]);

        $user = User::firstOrCreate(
            ['phone' => $phone],
            ['name' => $phone, 'phone_verified_at' => now()]
        );

        if ($user->phone_verified_at === null) {
            $user->update(['phone_verified_at' => now()]);
        }

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    private function normalizePhone(string $phone): string
    {
        return preg_replace('/[^\d+]/', '', $phone);
    }
}
