<?php

namespace App\Http\Requests\Chat;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'body' => ['nullable', 'string', 'max:5000'],
            'file' => [
                'nullable',
                'file',
                'max:10240',
                'mimetypes:image/jpeg,image/png,image/gif,image/webp,application/pdf',
            ],
            'client_id' => ['sometimes', 'integer', 'exists:clients,id'],
            'receiver_id' => ['sometimes', 'integer', 'exists:users,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'file.mimetypes' => 'Only JPG, PNG, GIF, WebP images and PDF files are allowed.',
            'file.max' => 'File must be 10 MB or smaller.',
            'body.max' => 'Message cannot exceed 5000 characters.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if (blank($this->input('body')) && ! $this->hasFile('file')) {
                $validator->errors()->add('body', 'Message body or file is required.');
            }
        });
    }
}
