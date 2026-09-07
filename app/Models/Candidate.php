<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidate extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'subject', 'education_levels',
        'diploma_document_path', 'teaching_category', 'category_document_path',
        'relocation_ready', 'relocation_conditions',
        'employment_type', 'desired_stake_fraction', 'bio',
    ];

    protected function casts(): array
    {
        return [
            'education_levels' => 'array',
            'relocation_ready' => 'boolean',
            'desired_stake_fraction' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function hasRequiredDocuments(): bool
    {
        return filled($this->diploma_document_path) && filled($this->teaching_category);
    }
}
