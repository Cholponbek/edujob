<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Единственная роль, входящая через email/password (Filament-панель),
        // остальные — телефон+OTP (ARCHITECTURE.md §2).
        User::firstOrCreate(
            ['email' => 'admin@edujob.kg'],
            [
                'name' => 'Platform Admin',
                'phone' => '+996000000000',
                'phone_verified_at' => now(),
                'password' => Hash::make('password'),
                'is_platform_admin' => true,
            ]
        );

        // Плейсхолдер-тарифы — цены и названия нужно проверить и
        // отредактировать через Filament (PlanResource) перед запуском,
        // это не финальное решение по монетизации.
        foreach ([
            ['name' => 'Старт', 'price' => 990],
            ['name' => 'Бизнес', 'price' => 2900],
            ['name' => 'Про', 'price' => 5900],
        ] as $plan) {
            Plan::firstOrCreate(
                ['name' => $plan['name']],
                ['price' => $plan['price'], 'currency' => 'KGS', 'billing_period_days' => 30, 'is_active' => true]
            );
        }
    }
}
