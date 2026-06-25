<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\ReunionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReunionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    protected function prepareForValidation(): void
    {
        // Combine date + time fields into full datetime values
        if ($this->filled('date') && $this->filled('start_time') && $this->filled('end_time')) {
            $this->merge([
                'start_time' => $this->date.' '.$this->start_time,
                'end_time' => $this->date.' '.$this->end_time,
            ]);
        }

        // Normalize participants: accept array or newline-separated text
        $participants = $this->input('participants');
        if (! is_array($participants)) {
            $participantsText = $this->input('participants_text', '');
            $participants = array_filter(
                array_map('trim', explode("\n", $participantsText)),
                fn ($p) => ! empty($p)
            );
        }
        $this->merge(['participants' => array_values($participants)]);

        // Normalize visibility: nullify titres when visible_to_all
        $this->merge(['visible_to_all' => $this->has('visible_to_all') ? 1 : 0]);
        if ($this->boolean('visible_to_all')) {
            $this->merge(['titres' => null]);
        }
    }

    public function rules(): array
    {
        return [
            'instance_id' => ['required', 'exists:instances,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:Y-m-d H:i'],
            'end_time' => ['required', 'date_format:Y-m-d H:i', 'after:start_time'],
            'location' => ['nullable', 'string', 'max:255'],
            'participants' => ['nullable', 'array'],
            'status' => ['required', 'string', Rule::in(array_column(ReunionStatus::cases(), 'value'))],
            'ordre_du_jour' => ['nullable', 'string'],
            'compte_rendu' => ['nullable', 'string'],
            'titres' => ['nullable', 'array'],
            'titres.*' => ['string', 'max:255', Rule::in(config('options.titres', []))],
            'visible_to_all' => ['boolean'],
        ];
    }
}
