<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StaffRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id', 'created_by', 'hiring_campaign_id',
        'title', 'subject', 'education_level', 'required_category',
        'employment_type', 'stake_fraction', 'is_next_school_year',
        'description', 'salary_from', 'salary_to', 'status', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'stake_fraction' => 'decimal:2',
            'is_next_school_year' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(Institution::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function hiringCampaign(): BelongsTo
    {
        return $this->belongsTo(HiringCampaign::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function recommendations(): HasMany
    {
        return $this->hasMany(CandidateRecommendation::class);
    }
}
