<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnnualPlan extends Model
{
    protected $primaryKey = 'plan_id';

    protected $fillable = [
        'site_id',
        'year',
        'category',
        'activity_type',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
        ];
    }

    public function site(): BelongsTo
    {
        return $this->belongsTo(Site::class, 'site_id', 'site_id');
    }

    public function realizedActivities(): HasMany
    {
        return $this->hasMany(RealizedActivity::class, 'plan_id', 'plan_id');
    }
}
