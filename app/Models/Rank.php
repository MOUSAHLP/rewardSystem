<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rank extends Model
{
    use HasFactory;

    protected $table = 'user_ranks';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'limit',
        'features',
        'description',
        'color'
    ];

    protected $casts = [
        'features' => 'array'
    ];
}
