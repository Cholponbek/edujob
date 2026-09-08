<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Вопросы, сгенерированные под предмет/уровень образования
            // вакансии (GenerateScreeningQuestionsJob) — кандидат отвечает
            // на них в screening_answers (уже есть), затем
            // ScoreScreeningApplicationJob выставляет вердикт.
            $table->json('screening_questions')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('screening_questions');
        });
    }
};
