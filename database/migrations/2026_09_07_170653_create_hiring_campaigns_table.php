<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Killer-фича "пакетный набор" (ARCHITECTURE.md §4): группирует
        // вакансии нескольких учреждений одного района/области под общей
        // публикацией с агрегированной статистикой дефицита по предметам.
        Schema::create('hiring_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('region');
            $table->string('district')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hiring_campaigns');
    }
};
