<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSelfPlanRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:80'],
            'exercise_ids' => ['required', 'array', 'min:1', 'max:3'],
            'exercise_ids.*' => [
                'integer',
                // integer 0 (not false) — Rule::exists where() binding doesn't coerce bool
                Rule::exists('exercises', 'id')->where('is_personalized', 0),
            ],
            'scheduled_days' => ['required', 'array', 'min:1'],
            'scheduled_days.*' => ['string', Rule::in(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'exercise_ids.max' => 'Free plans can include at most 3 exercises.',
            'exercise_ids.*.exists' => 'Only generalized exercises can be selected on free plans.',
        ];
    }
}
