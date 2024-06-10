<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Rank extends Model
{
    use HasFactory;

    use HasTranslations;
    public $translatable = ['name', 'description'];

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
