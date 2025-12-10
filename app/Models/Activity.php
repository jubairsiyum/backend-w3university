<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'minutes_active',
        'lessons_completed',
        'exercises_completed',
        'quizzes_completed',
        'blogs_read',
        'comments_posted',
        'code_snippets_created',
    ];

    protected $casts = [
        'minutes_active' => 'integer',
        'lessons_completed' => 'integer',
        'exercises_completed' => 'integer',
        'quizzes_completed' => 'integer',
        'blogs_read' => 'integer',
        'comments_posted' => 'integer',
        'code_snippets_created' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
