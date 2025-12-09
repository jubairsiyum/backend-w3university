<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'username',
        'phone',
        'bio',
        'avatar',
        'github_url',
        'linkedin_url',
        'twitter_url',
        'portfolio_url',
        'location',
        'timezone',
        'date_of_birth',
        'skill_level',
        'programming_languages',
        'interests',
        'daily_goal_minutes',
        'email_notifications',
        'is_public',
    ];

    protected $casts = [
        'programming_languages' => 'array',
        'interests' => 'array',
        'date_of_birth' => 'date',
        'email_notifications' => 'boolean',
        'is_public' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
