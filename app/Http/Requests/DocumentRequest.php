<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'visible_to_all' => $this->has('visible_to_all') ? 1 : 0,
            'titres' => $this->input('titres', []),
        ]);
    }

    public function rules(): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'visible_to_all' => ['required', 'boolean'],
            'titres' => ['nullable', 'array'],
            'titres.*' => ['string', 'max:255', Rule::in(config('options.titres', []))],
            'assigned_users' => ['nullable', 'array'],
            'assigned_users.*' => ['exists:users,id'],
            'category' => ['nullable', 'string', Rule::in(config('documents.categories', []))],
        ];

        if ($this->isMethod('post')) {
            $rules['file'] = ['required', 'file', 'mimes:pdf,doc,docx,xlsx,xls,txt,jpg,jpeg,png', 'max:10240'];
        } else {
            $rules['file'] = ['nullable', 'file', 'mimes:pdf,doc,docx,xlsx,xls,txt,jpg,jpeg,png', 'max:10240'];
        }

        return $rules;
    }
}
