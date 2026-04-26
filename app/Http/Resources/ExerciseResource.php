<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExerciseResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'area' => $this->area,
            'category' => $this->category,
            'difficulty' => $this->difficulty,
            'description' => $this->description,
            'video_url' => $this->video_url,
            'illustration_url' => $this->illustration_url,
            'default_sets' => $this->default_sets,
            'default_reps' => $this->default_reps,
            'default_hold_seconds' => $this->default_hold_seconds,
            'is_personalized' => (bool) $this->is_personalized,
            'exercise_type' => $this->exercise_type,
            'tracking_config' => $this->tracking_config,
        ];
    }
}
