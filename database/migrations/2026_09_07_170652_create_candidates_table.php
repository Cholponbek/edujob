<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();

            $table->string('subject')->nullable();
            $table->json('education_levels')->nullable();

            // Принятое решение: диплом и категория/аттестация обязательны
            // для полноценного профиля; судимость/мед.книжка — не в MVP
            // (ARCHITECTURE.md §5).
            $table->string('diploma_document_path')->nullable();
            $table->string('teaching_category')->nullable();
            $table->string('category_document_path')->nullable();

            // Killer-фича: региональный дисбаланс (ARCHITECTURE.md §4).
            $table->boolean('relocation_ready')->default(false);
            $table->text('relocation_conditions')->nullable();

            // Killer-фича: совместительство/частичная занятость как
            // первоклассный сценарий (ARCHITECTURE.md §4).
            $table->enum('employment_type', ['full_time', 'part_time', 'either'])->default('either');
            $table->decimal('desired_stake_fraction', 3, 2)->nullable();

            $table->text('bio')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
