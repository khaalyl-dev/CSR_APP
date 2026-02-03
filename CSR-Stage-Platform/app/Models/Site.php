<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    protected $primaryKey = 'site_id';

    protected $fillable = [
        'site_name',
        'location',
        'manager',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'site_id', 'site_id');
    }

    public function annualPlans(): HasMany
    {
        return $this->hasMany(AnnualPlan::class, 'site_id', 'site_id');
    }

    public function realizedActivities(): HasMany
    {
        return $this->hasMany(RealizedActivity::class, 'site_id', 'site_id');
    }

    public function changeRequests(): HasMany
    {
        return $this->hasMany(ChangeRequest::class, 'site_id', 'site_id');
    }
}
