<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('candidate_id')->constrained()->cascadeOnDelete();

            $table->enum('status', [
                'applied', 'screening', 'screened', 'hired', 'rejected',
            ])->default('applied');

            // Результат ИИ-скрининга (см. ARCHITECTURE.md §2 — всегда через
            // очередь, никогда синхронно). green/yellow/red — вердикт для
            // работодателя.
            $table->enum('screening_verdict', ['green', 'yellow', 'red'])->nullable();
            $table->unsignedTinyInteger('screening_match_percent')->nullable();
            $table->json('screening_answers')->nullable();
            $table->timestamp('screened_at')->nullable();

            $table->timestamps();

            $table->unique(['staff_request_id', 'candidate_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
