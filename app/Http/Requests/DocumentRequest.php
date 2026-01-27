<?php

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
        // Ensure visible_to_all is always present as 1 or 0 (checkbox behavior)
        $this->merge(['visible_to_all' => $this->has('visible_to_all') ? 1 : 0]);
    }

    public function rules(): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'visible_to_all' => ['required', 'boolean'],
            'assigned_users' => ['required_if:visible_to_all,0', 'array'],
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
