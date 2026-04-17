<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExerciseSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id', 'exercise_plan_id', 'exercise_id',
        'started_at', 'completed_at', 'status',
        'motion_data', 'form_score', 'coins_earned', 'rating',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'motion_data' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }

    public function plan()
    {
        return $this->belongsTo(ExercisePlan::class, 'exercise_plan_id');
    }
}
