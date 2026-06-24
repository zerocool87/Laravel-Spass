<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Instance;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InstanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $communes = config('options.communes_haute_vienne', []);
        sort($communes, SORT_STRING | SORT_FLAG_CASE);

        return [
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'string', Rule::in(array_keys(Instance::TYPES))],
            'description' => ['nullable', 'string'],
            'territory' => ['nullable', 'string', 'max:255', Rule::in($communes)],
            'members' => ['nullable', 'array'],
            'members.*' => ['string'],
        ];
    }
}
