<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\ReunionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReunionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'instance_id' => ['required', 'exists:instances,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'location' => ['nullable', 'string', 'max:255'],
            'participants' => ['nullable', 'array'],
            'status' => ['required', 'string', Rule::in(array_column(ReunionStatus::cases(), 'value'))],
            'ordre_du_jour' => ['nullable', 'string'],
            'compte_rendu' => ['nullable', 'string'],
            'titres' => ['nullable', 'array'],
            'titres.*' => ['string', 'max:255'],
            'visible_to_all' => ['boolean'],
        ];
    }
}
