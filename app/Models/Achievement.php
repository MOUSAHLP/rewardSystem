<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;
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
}
