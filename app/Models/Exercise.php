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
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'correct_angles' => 'array',
    ];

    // Return instructions in client's preferred language
    public function getInstructionsForLanguage(string $lang): ?string
    {
        $column = 'instructions_'.$lang;

        return $this->$column ?? $this->instructions_en;
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
