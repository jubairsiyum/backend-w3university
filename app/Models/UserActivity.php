<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'activity_date',
        'minutes_active',
        'lessons_completed',
        'exercises_completed',
        'quizzes_completed',
        'blogs_read',
        'comments_posted',
        'code_snippets_created',
        'streak_days',
    ];

    protected $casts = [
        'activity_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get activities for a specific date range
     */
    public static function getActivitiesInRange($userId, $startDate, $endDate)
    {
        return self::where('user_id', $userId)
            ->whereBetween('activity_date', [$startDate, $endDate])
            ->orderBy('activity_date', 'desc')
            ->get();
    }

    /**
     * Get total active time for a user
     */
    public static function getTotalActiveTime($userId)
    {
        return self::where('user_id', $userId)->sum('minutes_active');
    }
}
