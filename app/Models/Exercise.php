<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'category', 'difficulty', 'description',
        'video_url', 'illustration_url', 'default_sets', 'default_reps',
        'default_hold_seconds', 'instructions_en', 'instructions_pcm',
        'instructions_yo', 'instructions_ig', 'instructions_ha', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Return instructions in client's preferred language
    public function getInstructionsForLanguage(string $lang): ?string
    {
        $column = 'instructions_' . $lang;
        return $this->$column ?? $this->instructions_en;
    }

    public function plans()
    {
        return $this->belongsToMany(ExercisePlan::class, 'plan_exercises')
            ->withPivot(['order', 'sets', 'reps', 'hold_seconds', 'pt_notes'])
            ->withTimestamps();
    }

    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
