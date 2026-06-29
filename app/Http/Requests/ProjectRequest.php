<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['required', 'string', Rule::in(array_column(ProjectType::cases(), 'value'))],
            'status' => ['required', 'string', Rule::in(array_column(ProjectStatus::cases(), 'value'))],
            'commune' => ['nullable', 'string', 'max:255', Rule::in(config('options.communes_haute_vienne', []))],
            'territories' => ['nullable', 'array'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'indicators' => ['nullable', 'array'],
        ];
    }
}
