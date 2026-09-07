<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HiringCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'region', 'district', 'created_by', 'description',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function staffRequests(): HasMany
    {
        return $this->hasMany(StaffRequest::class);
    }

    /**
     * Дефицит по предметам среди опубликованных вакансий кампании —
     * то, ради чего "пакетный набор" существует как отдельная сущность
     * (ARCHITECTURE.md §4).
     */
    public function subjectDeficitCounts(): array
    {
        return $this->staffRequests()
            ->where('status', 'published')
            ->whereNotNull('subject')
            ->selectRaw('subject, count(*) as count')
            ->groupBy('subject')
            ->orderByDesc('count')
            ->pluck('count', 'subject')
            ->all();
    }
}
