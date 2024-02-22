<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AchievementUser extends Model
{
    use HasFactory;

    protected $table = 'achievement_users';
    public $timestamps = false;
    protected $fillable = [
        'achievement_id',
        'user_id',
    ];
    public function achievement()
    {
        return $this->belongsTo(Achievement::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
