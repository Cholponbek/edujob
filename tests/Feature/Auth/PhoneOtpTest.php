<?php

use App\Models\PhoneOtpCode;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('login screen can be rendered', function () {
    $this->get('/login')->assertOk();
});

test('requesting a code creates an otp record', function () {
    $response = $this->post('/otp/request', ['phone' => '+996555123456']);

    $response->assertSessionHasNoErrors();
    $response->assertSessionHas('status', 'otp-sent');

    $this->assertDatabaseHas('phone_otp_codes', ['phone' => '+996555123456']);
});

test('a new user can verify a code and is logged in', function () {
    $phone = '+996555123456';

    PhoneOtpCode::create([
        'phone' => $phone,
        'code_hash' => Hash::make('123456'),
        'expires_at' => now()->addMinutes(5),
    ]);

    $response = $this->post('/otp/verify', ['phone' => $phone, 'code' => '123456']);

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertAuthenticated();

    $user = User::where('phone', $phone)->first();
    expect($user)->not->toBeNull();
    expect($user->phone_verified_at)->not->toBeNull();
});

test('an existing user can log back in with a new code', function () {
    $user = User::factory()->create(['phone' => '+996555123456']);

    PhoneOtpCode::create([
        'phone' => $user->phone,
        'code_hash' => Hash::make('654321'),
        'expires_at' => now()->addMinutes(5),
    ]);

    $response = $this->post('/otp/verify', ['phone' => $user->phone, 'code' => '654321']);

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertAuthenticatedAs($user);
});

test('wrong code is rejected', function () {
    $phone = '+996555123456';

    PhoneOtpCode::create([
        'phone' => $phone,
        'code_hash' => Hash::make('123456'),
        'expires_at' => now()->addMinutes(5),
    ]);

    $response = $this->post('/otp/verify', ['phone' => $phone, 'code' => '000000']);

    $response->assertSessionHasErrors('code');
    $this->assertGuest();
});

test('expired code is rejected', function () {
    $phone = '+996555123456';

    PhoneOtpCode::create([
        'phone' => $phone,
        'code_hash' => Hash::make('123456'),
        'expires_at' => now()->subMinute(),
    ]);

    $response = $this->post('/otp/verify', ['phone' => $phone, 'code' => '123456']);

    $response->assertSessionHasErrors('code');
    $this->assertGuest();
});

test('daily rate limit is enforced', function () {
    $phone = '+996555123456';

    foreach (range(1, config('sms.otp_rate_limit_per_day')) as $i) {
        PhoneOtpCode::create([
            'phone' => $phone,
            'code_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(5),
        ]);
    }

    $response = $this->post('/otp/request', ['phone' => $phone]);

    $response->assertSessionHasErrors('phone');
});

test('test phone numbers always use the fixed code without sending sms', function () {
    config(['sms.test_phone_numbers' => ['+996555000000']]);

    $this->post('/otp/request', ['phone' => '+996555000000'])->assertSessionHasNoErrors();

    $response = $this->post('/otp/verify', [
        'phone' => '+996555000000',
        'code' => config('sms.test_otp_code'),
    ]);

    $response->assertRedirect(route('dashboard', absolute: false));
    $this->assertAuthenticated();
});
