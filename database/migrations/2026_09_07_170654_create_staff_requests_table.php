<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institution_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('hiring_campaign_id')->nullable()->constrained()->nullOnDelete();

            $table->string('title');
            $table->string('subject')->nullable();
            $table->enum('education_level', ['preschool', 'primary', 'secondary', 'vocational', 'higher']);
            $table->string('required_category')->nullable();

            $table->enum('employment_type', ['full_time', 'part_time', 'either'])->default('full_time');
            $table->decimal('stake_fraction', 3, 2)->default(1.00);

            // Killer-фича: ранняя публикация на следующий учебный год
            // (ARCHITECTURE.md §4, сезонность).
            $table->boolean('is_next_school_year')->default(false);

            $table->text('description')->nullable();
            $table->unsignedInteger('salary_from')->nullable();
            $table->unsignedInteger('salary_to')->nullable();

            $table->enum('status', ['draft', 'published', 'closed'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_requests');
    }
};
