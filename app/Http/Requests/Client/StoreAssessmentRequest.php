<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAssessmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->role === 'client';
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'medical_conditions' => ['nullable', 'string'],
            'height_cm' => ['required', 'integer', 'between:50,300'],
            'weight_kg' => ['required', 'integer', 'between:20,400'],
            'past_injuries' => ['nullable', 'string'],
            'allergies' => ['nullable', 'string'],
            'current_medications' => ['nullable', 'string'],
            'family_health_history' => ['nullable', 'string'],

            'smokes' => ['required', 'boolean'],
            'alcohol_consumption' => ['required', Rule::in(['rarely', 'occasionally', 'all_the_time'])],
            'diet_preferences' => ['nullable', 'string'],
            'stress_level' => ['required', 'integer', 'between:1,10'],

            'primary_goals' => ['required', 'array', 'min:1'],
            'primary_goals.*' => ['string'],
            'secondary_goals' => ['nullable', 'string'],
            'time_frame' => ['required', Rule::in(['30d', '60d', '90d', '6mo', '1yr'])],

            'exercise_habit' => ['required', Rule::in(['newbie', 'warrior', 'none'])],
            'weekly_schedule' => ['nullable', 'string', 'max:120'],
            'comfort_level' => ['required', 'array', 'min:1'],
            'comfort_level.*' => ['string'],
            'limitations' => ['nullable', 'string'],
            'best_time' => ['required', Rule::in(['morning', 'afternoon', 'evening'])],
            'feedback_frequency' => ['required', Rule::in(['daily', 'weekly', 'monthly', 'yearly'])],
            'feedback_type' => ['required', 'array', 'min:1'],
            'feedback_type.*' => ['string'],
            'feedback_channel' => ['required', Rule::in(['email', 'in_person', 'whatsapp'])],
        ];
    }
}
