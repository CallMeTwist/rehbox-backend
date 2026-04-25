<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExercisePlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'physiotherapist_id', 'client_id', 'created_by_client_id', 'is_self_built',
        'title', 'notes', 'status', 'duration_weeks', 'frequency', 'reminder_times', 'start_date',
    ];

    protected $casts = [
        'reminder_times' => 'array',
        'start_date' => 'date',
        'is_self_built' => 'bool',
    ];

    public function physiotherapist(): BelongsTo
    {
        return $this->belongsTo(Physiotherapist::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function createdByClient(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'created_by_client_id');
    }

    public function exercises(): BelongsToMany
    {
        return $this->belongsToMany(Exercise::class, 'plan_exercises')
            ->withPivot(['order', 'sets', 'reps', 'hold_seconds', 'pt_notes', 'scheduled_days'])
            ->orderByPivot('order')
            ->withTimestamps();
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ExerciseSession::class);
    }

    public function scopeSelfBuilt($query)
    {
        return $query->where('is_self_built', true);
    }

    // Compliance: % of sessions completed vs expected
    public function getComplianceRateAttribute(): int
    {
        $total = $this->sessions()->count();
        $completed = $this->sessions()->where('status', 'completed')->count();

        return $total > 0 ? (int) round(($completed / $total) * 100) : 0;
    }
}
