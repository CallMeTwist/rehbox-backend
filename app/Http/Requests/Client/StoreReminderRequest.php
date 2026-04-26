<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReminderRequest extends FormRequest
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
            'type' => ['required', Rule::in(['exercise', 'posture', 'hydration', 'diet'])],
            'times' => ['required', 'array', 'min:1'],
            'times.*' => ['required', 'string', 'regex:/^\d{2}:\d{2}$/'],
            'days' => ['required', 'array', 'min:1'],
            'days.*' => ['required', 'string', Rule::in(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
