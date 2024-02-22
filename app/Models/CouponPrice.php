<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponPrice extends Model
{
    use HasFactory;

    protected $table = 'coupon_prices';
    public $timestamps = false;
    protected $fillable = [
        'coupon_points',
        'coupon_value'
    ];
}
