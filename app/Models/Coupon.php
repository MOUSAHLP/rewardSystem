<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Coupon extends Model
{
    use HasFactory;

    protected $table = 'coupons';
    public $timestamps = false;
    protected $fillable = [
        'coupon_type_id',
        'value',
        'price',
        'description',
        'created_at',
    ];

    public function couponUser()
    {
        return $this->hasMany(CouponUser::class);
    }

    public function couponType()
    {
        return $this->belongsTo(CouponType::class);
    }
}
