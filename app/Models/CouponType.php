<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CouponType extends Model
{
    use HasFactory;

    protected $table = 'coupons_types';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'image',
        "is_percentage"
    ];

    public function coupons()
    {
        return $this->hasMany(Coupon::class);
    }

    public function getImageAttribute($value)
    {
        return env('APP_URL') . '/storage/images/couponsType/' . $value;
    }
    protected $casts = [
        "is_percentage" => "boolean"
    ];
}
