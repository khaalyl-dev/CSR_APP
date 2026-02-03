<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RealizedActivity extends Model
{
    protected $primaryKey = 'activity_id';

    protected $fillable = [
        'plan_id',
        'site_id',
        'activity_name',
        'category',
        'activity_type',
        'cost',
        'status',
        'performed_at',
    ];

    protected function casts(): array
    {
        return [
            'cost' => 'decimal:2',
            'performed_at' => 'date',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(AnnualPlan::class, 'plan_id', 'plan_id');
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id', 'site_id');
    }
}
