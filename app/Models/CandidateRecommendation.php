<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidateRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'candidate_id', 'staff_request_id', 'score', 'reasoning', 'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'generated_at' => 'datetime',
        ];
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function staffRequest(): BelongsTo
    {
        return $this->belongsTo(StaffRequest::class);
    }
}
