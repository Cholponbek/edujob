<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_request_id', 'candidate_id', 'status',
        'screening_verdict', 'screening_match_percent', 'screening_answers', 'screened_at',
    ];

    protected function casts(): array
    {
        return [
            'screening_answers' => 'array',
            'screened_at' => 'datetime',
        ];
    }

    public function staffRequest(): BelongsTo
    {
        return $this->belongsTo(StaffRequest::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }
}
