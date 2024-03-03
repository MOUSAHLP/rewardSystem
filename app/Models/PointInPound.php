<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointInPound extends Model
{
    use HasFactory;

    protected $table = 'point_in_pound';
    public $timestamps = false;
    protected $fillable = [
        'value'
    ];

    public static function point_value(){
        return PointInPound::first()->value;
    }
}
