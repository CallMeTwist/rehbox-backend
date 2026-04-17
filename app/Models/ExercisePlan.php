<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExercisePlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'physiotherapist_id', 'client_id', 'title', 'notes',
        'status', 'duration_weeks', 'frequency', 'reminder_times', 'start_date',
    ];

    protected $casts = [
        'reminder_times' => 'array',
        'start_date' => 'date',
    ];

    public function physiotherapist()
    {
        return $this->belongsTo(Physiotherapist::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function exercises()
    {
        return $this->belongsToMany(Exercise::class, 'plan_exercises')
            ->withPivot(['order', 'sets', 'reps', 'hold_seconds', 'pt_notes'])
            ->orderByPivot('order')
            ->withTimestamps();
    }

    public function sessions()
    {
        return $this->hasMany(ExerciseSession::class);
    }

    // Compliance: % of sessions completed vs expected
    public function getComplianceRateAttribute(): int
    {
        $total = $this->sessions()->count();
        $completed = $this->sessions()->where('status', 'completed')->count();

        return $total > 0 ? (int) round(($completed / $total) * 100) : 0;
    }
}
