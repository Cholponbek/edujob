<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('institutions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // Дошкольное/школьное/СПО/высшее — определяет, какие уровни
            // образования вправе публиковать вакансии (см. ARCHITECTURE.md §1).
            $table->enum('type', ['kindergarten', 'school', 'vocational', 'university']);
            $table->string('region');
            $table->string('district')->nullable();
            // Верификация — вручную модератором на старте (принятое решение,
            // см. ARCHITECTURE.md §5). Без интеграции с реестром Минобра.
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('institutions');
    }
};
