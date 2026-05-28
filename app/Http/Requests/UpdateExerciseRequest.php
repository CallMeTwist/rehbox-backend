<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExerciseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $areas = ['back', 'chest', 'elbow_forearm_wrist', 'general', 'head_neck', 'lower_limbs', 'pelvic', 'upper_limbs'];
        $categories = ['strengthening', 'stretching', 'rom', 'functional', 'endurance',
            'lung_expansion', 'chest_wall_mobilization', 'airways_clearance',
            'chest_abs', 'cool_down', 'core_stability', 'legs', 'strengthening_arm'];

        return [
            'title' => ['required', 'string', 'max:255'],
            'area' => ['required', Rule::in($areas)],
            'category' => ['required', Rule::in($categories)],
            'difficulty' => ['nullable', Rule::in(['beginner', 'intermediate', 'advanced'])],

            'access_tier' => ['required', Rule::in(['free', 'paid'])],
            'video_source' => ['required', Rule::in(['youtube', 'upload'])],

            'video_path' => ['nullable', 'string', 'max:500',
                Rule::requiredIf(fn () => $this->input('video_source') === 'upload')],
            'youtube_url' => ['nullable', 'string', 'max:500', 'regex:/^https?:\/\/(www\.)?(youtube\.com|youtu\.be)\//i',
                Rule::requiredIf(fn () => $this->input('video_source') === 'youtube')],

            'thumbnail_path' => ['nullable', 'string', 'max:500'],
            'illustration_url' => ['nullable', 'string', 'max:500'],

            'default_sets' => ['nullable', 'integer', 'min:1', 'max:20'],
            'default_reps' => ['nullable', 'integer', 'min:1', 'max:200'],
            'default_hold_seconds' => ['nullable', 'integer', 'min:0', 'max:600'],

            'instructions_en' => ['nullable', 'string'],
            'instructions_pcm' => ['nullable', 'string'],
            'instructions_yo' => ['nullable', 'string'],
            'instructions_ig' => ['nullable', 'string'],
            'instructions_ha' => ['nullable', 'string'],

            'is_active' => ['boolean'],
            'correct_angles' => ['nullable', 'array'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'youtube_url.regex' => 'YouTube URL must be on youtube.com or youtu.be.',
        ];
    }

    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function ($v) {
            $area = $this->input('area');
            $tier = $this->input('access_tier');
            $source = $this->input('video_source');

            if ($area === 'general' && $tier !== 'free') {
                $v->errors()->add('access_tier', 'General Exercises must be set to the free tier.');
            }
            if ($tier === 'paid' && $source !== 'upload') {
                $v->errors()->add('video_source', 'Paid exercises must use an uploaded video.');
            }
        });
    }
}
