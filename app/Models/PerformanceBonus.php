<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceBonus extends Model
{

    protected $fillable = [
        'seller_id',
        'brand_id',
        'target_revenue',
        'bonus_amount',
        'period_start',
        'period_end',
        'currency',
        'status'
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
