<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exercise>
 */
class ExerciseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->words(3, true),
            'area' => fake()->randomElement(['back', 'chest', 'elbow_forearm_wrist', 'general', 'head_neck', 'lower_limbs', 'pelvic', 'upper_limbs']),
            'category' => fake()->randomElement(['strengthening', 'stretching', 'rom', 'functional', 'endurance']),
            'difficulty' => 'beginner',
            'description' => fake()->sentence(),
            'is_active' => true,
            'access_tier' => 'paid',
            'video_source' => 'upload',
            'video_path' => 'exercises/videos/test/test.mp4',
        ];
    }

    public function free(): static
    {
        return $this->state(fn () => [
            'area' => 'general',
            'access_tier' => 'free',
            'video_source' => 'youtube',
            'video_path' => null,
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);
    }
}
