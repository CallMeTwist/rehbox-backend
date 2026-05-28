<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;

class ExerciseResource extends JsonResource
{
    private const AREA_LABELS = [
        'back' => 'Back', 'chest' => 'Chest', 'elbow_forearm_wrist' => 'Elbow, Forearm & Wrist',
        'general' => 'General Exercises', 'head_neck' => 'Head & Neck', 'lower_limbs' => 'Lower Limbs',
        'pelvic' => 'Pelvic', 'upper_limbs' => 'Upper Limbs',
    ];

    private const CATEGORY_LABELS = [
        'strengthening' => 'Strengthening', 'stretching' => 'Stretching', 'rom' => 'Range of Motion',
        'functional' => 'Functional', 'endurance' => 'Endurance', 'lung_expansion' => 'Lung Expansion',
        'chest_wall_mobilization' => 'Chest Wall Mobilization', 'airways_clearance' => 'Airways Clearance',
        'chest_abs' => 'Chest & Abs', 'cool_down' => 'Cool Down', 'core_stability' => 'Core Stability',
        'legs' => 'Legs', 'strengthening_arm' => 'Strengthening (Arm)',
    ];

    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $request->user();
        $client = $user?->client;
        $isFree = $client && $client->isFree();
        $isLocked = $isFree && $this->access_tier === 'paid';

        $payload = [
            'id' => $this->id,
            'title' => $this->title,
            'area' => $this->area,
            'area_label' => self::AREA_LABELS[$this->area] ?? $this->area,
            'category' => $this->category,
            'category_label' => self::CATEGORY_LABELS[$this->category] ?? $this->category,
            'difficulty' => $this->difficulty,
            'access_tier' => $this->access_tier,
            'is_locked' => $isLocked,
            'video' => [
                'source' => $this->video_source,
                'url' => $isLocked ? null : $this->resolveVideoUrl(),
                'youtube_id' => $isLocked ? null : $this->youtubeId(),
            ],
            'thumbnail_url' => $this->resolveThumbnailUrl(),
            'default_sets' => $this->default_sets,
            'default_reps' => $this->default_reps,
            'default_hold_seconds' => $this->default_hold_seconds,
            'is_personalized' => (bool) $this->is_personalized,
            'instructions' => $this->resolveInstructions($request),
        ];

        if (! $isLocked && ! empty($this->correct_angles)) {
            $payload['correct_angles'] = $this->correct_angles;
        }

        return $payload;
    }

    private function resolveVideoUrl(): ?string
    {
        if ($this->video_source === 'youtube') {
            return null;
        }
        if ($this->video_source === 'upload' && $this->video_path) {
            $disk = Storage::disk(config('rehbox.exercise_video_disk'));
            $adapter = $disk->getAdapter();

            if ($adapter instanceof AwsS3V3Adapter) {
                return $disk->temporaryUrl($this->video_path, now()->addMinutes(30));
            }

            return '/storage/'.$this->video_path;
        }

        return null;
    }

    private function resolveThumbnailUrl(): ?string
    {
        if ($this->illustration_url) {
            return '/storage/'.$this->illustration_url;
        }
        if ($this->video_source === 'youtube' && ($id = $this->youtubeId())) {
            return "https://i.ytimg.com/vi/{$id}/hqdefault.jpg";
        }
        if ($this->thumbnail_path) {
            return '/storage/'.$this->thumbnail_path;
        }

        return null;
    }

    private function resolveInstructions(Request $request): ?string
    {
        $lang = $request->user()?->client?->language_preference ?? 'en';

        return $this->getInstructionsForLanguage($lang);
    }
}
