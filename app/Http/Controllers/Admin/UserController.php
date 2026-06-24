<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUserRequest;
use App\Models\EluProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
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

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $data['is_admin'] = $request->boolean('is_admin');
        $data['is_elu'] = $request->boolean('is_elu');
        $data['titres'] = $request->input('titres', []);
        $data['commune'] = $request->input('commune');

        $user->update($data);

        // Update or create elu profile
        $profileFields = [
            'code_insee', 'civilite', 'epci_commune', 'telephone',
            'adresse_1', 'adresse_2', 'code_postal', 'profession', 'societe',
            'secteur', 'nom_secteur', 'rt_ds_dt', 'contact',
            'mail_personnel', 'mail_2', 'date_deliberation', 'date_naissance',
            'visa_prefecture', 'probleme_delib', 'ordre_suppleants',
            'newsletter', 'frais_route', 'rib_fourni', 'chevaux_fiscaux',
        ];

        $profileData = [];
        foreach ($profileFields as $field) {
            if ($request->has($field)) {
                if (in_array($field, ['newsletter', 'frais_route', 'rib_fourni'], true)) {
                    $profileData[$field] = $request->boolean($field);
                } elseif (in_array($field, ['ordre_suppleants'], true)) {
                    $profileData[$field] = $request->input($field) !== null ? (int) $request->input($field) : null;
                } else {
                    $profileData[$field] = $request->input($field) ?: null;
                }
            }
        }

        // Sync titres from users.titres to elu_profiles.titres
        if ($request->has('titres')) {
            $profileData['titres'] = $request->input('titres', []);
        }

        if (! empty($profileData)) {
            EluProfile::updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );
        }

        return redirect()->route('elus.admin.users')->with('success', 'Utilisateur mis à jour.');
    }
}
