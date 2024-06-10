<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Translatable\HasTranslations;

class CouponType extends Model
{
    use HasFactory;

    use HasTranslations;
    public $translatable = ['name'];

    protected $table = 'coupons_types';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'image',
        "type"
    ];
    public function coupons()
    {
        return $this->hasMany(Coupon::class);
    }

}
