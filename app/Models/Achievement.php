<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Achievement extends Model
{
    use HasFactory;

    use HasTranslations;
    public $translatable = ['achievement','description'];

    protected $table = 'achievements';
    public $timestamps = false;
    protected $fillable = [
        'achievement',
        'points',
        'description',
        'segments',
    ];

    public function point()
    {
        return $this->belongsTo(Point::class,"id","achievement_id");
    }
    public function achievementsDone()
    {
        return $this->hasMany(AchievementUser::class);
    }
    protected $casts = [
        'segments'=>"integer"
    ];
}
