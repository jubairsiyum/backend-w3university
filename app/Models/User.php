<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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
        'badges',
        'daily_goal_minutes',
        'email_notifications',
        'is_public',
        'current_streak',
        'longest_streak',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
            'programming_languages' => 'array',
            'interests' => 'array',
            'badges' => 'array',
            'email_notifications' => 'boolean',
            'is_public' => 'boolean',
        ];
    }

    /**
     * Get the user's favorites
     */
    public function favorites()
    {
        return $this->hasMany(\App\Models\Favorite::class);
    }

    /**
     * Get the user's activities
     */
    public function activities()
    {
        return $this->hasMany(\App\Models\Activity::class);
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
