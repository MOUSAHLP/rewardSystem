<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointInSyrianPound extends Model
{
    use HasFactory;

    protected $table = 'point_in_syrian_pound';
    public $timestamps = false;
    protected $fillable = [
        'value'
    ];

    public static function point_value(){
        return PointInSyrianPound::first()->value;
    }
}
