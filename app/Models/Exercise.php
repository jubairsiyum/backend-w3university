<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Exercise extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'title_bn',
        'description',
        'description_bn',
        'instructions',
        'instructions_bn',
        'difficulty',
        'difficulty_bn',
        'duration',
        'duration_bn',
        'category',
        'category_bn',
        'tags',
        'tags_bn',
        'starter_code',
        'solution_code',
        'programming_language',
        'image_url',
        'slug',
        'status',
        'views',
        'completions',
        'published_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tags' => 'array',
        'tags_bn' => 'array',
        'published_at' => 'datetime',
        'views' => 'integer',
        'completions' => 'integer',
        'duration' => 'integer',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate slug from title if not provided
        static::creating(function ($exercise) {
            if (empty($exercise->slug)) {
                $exercise->slug = Str::slug($exercise->title);
            }
        });

        // Update slug if title changes
        static::updating(function ($exercise) {
            if ($exercise->isDirty('title') && empty($exercise->slug)) {
                $exercise->slug = Str::slug($exercise->title);
            }
        });
    }

    /**
     * Scope a query to only include published exercises.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    /**
     * Scope a query to only include draft exercises.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to filter by difficulty.
     */
    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    /**
     * Increment the views count.
     */
    public function incrementViews()
    {
        $this->increment('views');
    }

    /**
     * Increment the completions count.
     */
    public function incrementCompletions()
    {
        $this->increment('completions');
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
