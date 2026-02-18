<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'fonction' => ['nullable', 'string', 'max:255'],
            'commune' => ['nullable', 'string', 'max:255', Rule::in(config('options.communes_haute_vienne', []))],
        ];

        if ($this->isMethod('post')) {
            $rules['email'][] = Rule::unique('users', 'email');
        } else {
            $rules['email'][] = Rule::unique('users', 'email')->ignore($this->route('user'));
        }

        return $rules;
    }
}
