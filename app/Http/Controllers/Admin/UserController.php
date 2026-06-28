<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUserRequest;
use App\Models\EluProfile;
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

        $data['is_admin'] = $request->boolean('is_admin');
        $data['is_elu'] = $request->boolean('is_elu');
        $data['titres'] = $request->input('titres', []);
        $data['commune'] = $request->input('commune');

        $user->update($data);

        $profileData = [];

        // Boolean fields need explicit handling (unchecked checkboxes are not sent)
        foreach (['newsletter', 'frais_route', 'rib_fourni'] as $field) {
            $profileData[$field] = $request->boolean($field);
        }

        // Text and date fields
        foreach (['code_insee', 'civilite', 'epci_commune', 'telephone',
            'adresse_1', 'adresse_2', 'code_postal', 'profession', 'societe',
            'secteur', 'nom_secteur', 'rt_ds_dt', 'contact',
            'mail_personnel', 'mail_2', 'date_deliberation', 'date_naissance',
            'visa_prefecture', 'probleme_delib', 'chevaux_fiscaux',
        ] as $field) {
            if ($request->has($field)) {
                $profileData[$field] = $request->input($field) ?: null;
            }
        }

        // Integer field with null handling
        if ($request->has('ordre_suppleants')) {
            $profileData['ordre_suppleants'] = $request->input('ordre_suppleants') !== null
                ? (int) $request->input('ordre_suppleants')
                : null;
        }

        // Sync titres to elu_profiles
        if ($request->has('titres')) {
            $profileData['titres'] = $request->input('titres', []);
        }

        if ($profileData !== []) {
            EluProfile::updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );
        }

        return redirect()->route('elus.admin.users')->with('success', __('Utilisateur mis à jour.'));
    }
}
