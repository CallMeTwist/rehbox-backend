<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'area', 'category', 'difficulty', 'description',
        'video_url', 'illustration_url', 'default_sets', 'default_reps',
        'default_hold_seconds', 'instructions_en', 'instructions_pcm',
        'instructions_yo', 'instructions_ig', 'instructions_ha', 'is_active',
        'correct_angles',
        'is_personalized',
        'exercise_type',
        'tracking_config',
        'access_tier', 'video_source', 'video_path', 'youtube_url', 'thumbnail_path',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'correct_angles' => 'array',
        'tracking_config' => 'array',
    ];

    protected static function booted(): void
    {
        static::observe(\App\Observers\ExerciseObserver::class);
    }

    // Return instructions in client's preferred language
    public function getInstructionsForLanguage(string $lang): ?string
    {
        $column = 'instructions_'.$lang;

        return $this->$column ?? $this->instructions_en;
    }

    public function youtubeId(): ?string
    {
        if ($this->video_source !== 'youtube' || ! $this->youtube_url) {
            return null;
        }

        if (preg_match('/(?:v=|youtu\.be\/)([A-Za-z0-9_\-]{11})/', $this->youtube_url, $m)) {
            return $m[1];
        }

        return null;
    }

    public function plans()
    {
        return $this->belongsToMany(ExercisePlan::class, 'plan_exercises')
            ->withPivot(['order', 'sets', 'reps', 'hold_seconds', 'pt_notes'])
            ->withTimestamps();
    }

    /** Filter by body-part area (neck, shoulder, etc.) */
    public function scopeArea($query, string $area)
    {
        return $query->where('area', $area);
    }

    /** Filter by exercise type (strengthening, stretching, rom, functional, endurance) */
    public function scopeCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
