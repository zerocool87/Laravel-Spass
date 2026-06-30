<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserController extends Controller
{
    public function edit(User $user): View
    {
        return view('elus.admin.users.edit', [
            'user' => $user->load('eluProfile'),
            'communes' => $this->communes(),
        ]);
    }

    public function update(AdminUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->forceFill([
            'name' => $data['name'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'commune' => $data['commune'] ?? null,
            'titres' => $data['titres'] ?? [],
            'is_admin' => $data['is_admin'] ?? false,
            'is_elu' => $data['is_elu'] ?? false,
        ]);

        if (isset($data['password'])) {
            $user->password = $data['password'];
        }

        $user->saveQuietly();

        $user->eluProfile()->updateOrCreate(
            ['user_id' => $user->id],
            $this->profileData($request)
        );

        return redirect()->route('elus.admin.users')->with('success', __('Utilisateur mis à jour.'));
    }

    private function profileData(AdminUserRequest $request): array
    {
        $data = [];

        foreach (['newsletter', 'frais_route', 'rib_fourni'] as $field) {
            $data[$field] = $request->boolean($field);
        }

        foreach ($this->textProfileFields() as $field) {
            $value = $request->input($field);
            $data[$field] = $value !== '' ? $value : null;
        }

        if ($request->has('ordre_suppleants')) {
            $data['ordre_suppleants'] = $request->input('ordre_suppleants') !== null
                ? (int) $request->input('ordre_suppleants')
                : null;
        }

        if ($request->has('titres')) {
            $data['titres'] = $request->input('titres', []);
        }

        return $data;
    }

    /** @return list<string> */
    private function textProfileFields(): array
    {
        return [
            'code_insee', 'civilite', 'epci_commune', 'telephone',
            'adresse_1', 'adresse_2', 'code_postal', 'profession', 'societe',
            'secteur', 'nom_secteur', 'rt_ds_dt', 'contact',
            'mail_personnel', 'mail_2', 'date_deliberation', 'date_naissance',
            'visa_prefecture', 'probleme_delib', 'chevaux_fiscaux',
        ];
    }
}
