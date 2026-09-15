<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Тарифы подписки на уровне учреждения (ARCHITECTURE.md §5 —
        // монетизация: подписка учреждения, кандидаты бесплатно). Цены
        // редактируются через Filament, не хардкодятся в коде.
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('price');
            $table->string('currency', 3)->default('KGS');
            $table->unsignedSmallInteger('billing_period_days')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
