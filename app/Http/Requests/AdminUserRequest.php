<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'prenom' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];

        if ($this->isMethod('post')) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        $rules += [
            'titres' => ['nullable', 'array'],
            'titres.*' => ['string', 'max:255', Rule::in(config('options.titres', []))],
            'commune' => ['nullable', 'string', 'max:255', Rule::in(config('options.communes_haute_vienne', []))],
            'is_admin' => ['boolean'],
            'is_elu' => ['boolean'],
            'code_insee' => ['nullable', 'string', 'max:255'],
            'civilite' => ['nullable', 'string', 'max:255'],
            'epci_commune' => ['nullable', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:255'],
            'adresse_1' => ['nullable', 'string', 'max:255'],
            'adresse_2' => ['nullable', 'string', 'max:255'],
            'code_postal' => ['nullable', 'string', 'max:255'],
            'profession' => ['nullable', 'string', 'max:255'],
            'societe' => ['nullable', 'string', 'max:255'],
            'secteur' => ['nullable', 'string', 'max:255'],
            'nom_secteur' => ['nullable', 'string', 'max:255'],
            'rt_ds_dt' => ['nullable', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:255'],
            'mail_personnel' => ['nullable', 'email', 'max:255'],
            'mail_2' => ['nullable', 'email', 'max:255'],
            'date_deliberation' => ['nullable', 'date'],
            'date_naissance' => ['nullable', 'date'],
            'visa_prefecture' => ['nullable', 'string', 'max:255'],
            'probleme_delib' => ['nullable', 'string'],
            'ordre_suppleants' => ['nullable', 'integer'],
            'newsletter' => ['boolean'],
            'frais_route' => ['boolean'],
            'rib_fourni' => ['boolean'],
            'chevaux_fiscaux' => ['nullable', 'string', 'max:255'],
        ];

        if ($this->isMethod('post')) {
            $rules['email'][] = Rule::unique('users', 'email');
        } else {
            $rules['email'][] = Rule::unique('users', 'email')->ignore($this->route('user'));
        }

        return $rules;
    }
}
