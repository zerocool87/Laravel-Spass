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

    public function rules(): array
    {
        $rules = [
            'title' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'visible_to_all' => ['required','boolean'],
            'assigned_users' => ['nullable','array'],
            'assigned_users.*' => ['exists:users,id'],
        ];

        if ($this->isMethod('post')) {
            $rules['file'] = ['required','file','mimes:pdf,doc,docx,xlsx,xls,txt,jpg,jpeg,png','max:10240'];
        } else {
            $rules['file'] = ['nullable','file','mimes:pdf,doc,docx,xlsx,xls,txt,jpg,jpeg,png','max:10240'];
        }

        return $rules;
    }
}
