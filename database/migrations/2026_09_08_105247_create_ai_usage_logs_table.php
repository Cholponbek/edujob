<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Логирование стоимости/токенов каждого ИИ-вызова с первого дня
        // (ARCHITECTURE.md, чеклист "ИИ") — контроль расходов, не только
        // отладка.
        Schema::create('ai_usage_logs', function (Blueprint $table) {
            $table->id();
            $table->string('job_class');
            $table->string('model');
            $table->unsignedInteger('input_tokens');
            $table->unsignedInteger('output_tokens');
            $table->decimal('estimated_cost_usd', 10, 4);
            $table->nullableMorphs('related');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_usage_logs');
    }
};
