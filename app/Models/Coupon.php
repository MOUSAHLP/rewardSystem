<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Coupon extends Model
{
    use HasFactory;

    protected $table = 'coupons';
    public $timestamps = false;
    protected $fillable = [
        'coupon_type_id',
        'value',
        'coupon_code',
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

    public function price()
    {
        return $this->belongsTo(CouponPrice::class);
    }

    public static function generateCode(){
        $code = str::random(8);
        $exists = Coupon::where('coupon_code', $code)->count();
        if($exists > 0){
            Coupon::generateCode();
        }
        return $code;
    }
}
