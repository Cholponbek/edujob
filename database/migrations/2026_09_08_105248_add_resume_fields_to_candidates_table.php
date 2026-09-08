<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            // Исходный загруженный файл (PDF/изображение) до разбора
            // ИИ — ParseResumeDocumentJob читает его и заполняет
            // структурированные поля профиля (subject, education_levels и т.д.)
            $table->string('resume_source_path')->nullable()->after('bio');
            // Двуязычная генерация резюме — killer-фича под рынок КР
            // (ARCHITECTURE.md §4), GenerateBilingualResumeJob.
            $table->text('resume_text_ru')->nullable()->after('resume_source_path');
            $table->text('resume_text_ky')->nullable()->after('resume_text_ru');
        });
    }

    public function down(): void
    {
        Schema::table('candidates', function (Blueprint $table) {
            $table->dropColumn(['resume_source_path', 'resume_text_ru', 'resume_text_ky']);
        });
    }
};
