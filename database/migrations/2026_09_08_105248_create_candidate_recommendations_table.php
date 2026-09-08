<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ИИ-подбор вакансий кандидату (ARCHITECTURE.md §3) — результат
        // ранжирования, пересчитывается по запросу (RecommendStaffRequestsJob).
        Schema::create('candidate_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();
            $table->foreignId('staff_request_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('score');
            $table->text('reasoning')->nullable();
            $table->timestamp('generated_at');
            $table->timestamps();

            $table->unique(['candidate_id', 'staff_request_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidate_recommendations');
    }
};
