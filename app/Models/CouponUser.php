<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CouponUser extends Model
{
    use HasFactory;
    protected $table = 'coupons_users';
    public $timestamps = false;
    protected $fillable = [
        'user_id',
        'coupon_id',
        'coupon_code',
        'used_at',
        'expire_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function coupon()
    {
        return $this->belongsTo(Coupon::class,"coupon_id","id");
    }

    public static function generateCode(){
        $code = str::random(8);
        $exists = CouponUser::where('coupon_code', $code)->count();
        if($exists > 0){
            CouponUser::generateCode();
        }
        return $code;
    }
}
