<?php

namespace App\Http\Requests;

use App\Enums\TaskStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return match ($this->method()) {
            'PUT' => [
                'user_id' => [
                    'missing',
                ],
                'status' => [
                    'required',
                    Rule::enum(TaskStatus::class),
                ],
                'description' => [
                    'required',
                    'string',
                ],
            ],
            'PATCH' => [
                'user_id' => [
                    'missing',
                ],
                'status' => [
                    'sometimes', // Validating when present
                    'required',
                    Rule::enum(TaskStatus::class),
                ],
                'description' => [
                    'sometimes', // Validating when present
                    'required',
                    'string',
                ],
            ],
            default => [], // Not possible.
        };
    }
}
