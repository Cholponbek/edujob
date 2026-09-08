<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AiUsageLog extends Model
{
    protected $fillable = [
        'job_class', 'model', 'input_tokens', 'output_tokens',
        'estimated_cost_usd', 'related_type', 'related_id',
    ];

    public function related(): MorphTo
    {
        return $this->morphTo();
    }
}
