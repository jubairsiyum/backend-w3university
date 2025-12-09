<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPerformance extends Model
{
    use HasFactory;

    protected $table = 'user_performance';

    protected $fillable = [
        'user_id',
        'total_courses_completed',
        'total_lessons_completed',
        'total_exercises_completed',
        'total_quizzes_completed',
        'average_quiz_score',
        'total_certificates_earned',
        'total_hours_learned',
        'current_streak',
        'longest_streak',
        'total_points',
        'experience_level',
        'badges',
        'skills_completed',
        'last_active_date',
        'joined_date',
    ];

    protected $casts = [
        'badges' => 'array',
        'skills_completed' => 'array',
        'last_active_date' => 'date',
        'joined_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate user's experience level based on points
     */
    public function calculateLevel()
    {
        // Simple level calculation: Level = floor(sqrt(points / 100))
        return floor(sqrt($this->total_points / 100)) + 1;
    }

    /**
     * Update streak based on activity
     */
    public function updateStreak($isActiveToday)
    {
        if ($isActiveToday) {
            $this->current_streak++;
            if ($this->current_streak > $this->longest_streak) {
                $this->longest_streak = $this->current_streak;
            }
        } else {
            $this->current_streak = 0;
        }
        $this->save();
    }
}
