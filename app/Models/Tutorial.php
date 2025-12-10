<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tutorial extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'language_id',
        'title',
        'content',
        'code_example',
        'order',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'order' => 'integer',
    ];

    // Scope to get only published tutorials
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    // Scope to filter by language
    public function scopeForLanguage($query, $languageId)
    {
        return $query->where('language_id', $languageId);
    }

    // Scope to order by custom order field
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }
}
