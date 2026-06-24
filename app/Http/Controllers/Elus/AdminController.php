<?php

declare(strict_types=1);

namespace App\Http\Controllers\Elus;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\EluProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard for Espace Élus.
     */
    public function index(): View
    {
        $stats = [
            'total_users' => User::count(),
            'total_elus' => User::where('is_elu', true)->count(),
            'total_admins' => User::where('is_admin', true)->count(),
        ];

        $recentUsers = User::orderBy('created_at', 'desc')->take(5)->get();
        $recentDocuments = Document::orderBy('created_at', 'desc')->take(3)->get();

        return view('elus.admin.index', compact('stats', 'recentUsers', 'recentDocuments'));
    }

    /**
     * Display a listing of users (élus management).
     */
    public function users(Request $request): View
    {
        $query = User::query();

        // Filter by role
        if ($request->filled('role')) {
            if ($request->role === 'elu') {
                $query->where('is_elu', true);
            } elseif ($request->role === 'admin') {
                $query->where('is_admin', true);
            } elseif ($request->role === 'standard') {
                $query->where('is_elu', false)->where('is_admin', false);
            }
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('email', 'like', '%'.$request->search.'%')
                    ->orWhere('commune', 'like', '%'.$request->search.'%');
            });
        }

        $users = $query->orderBy('name')->paginate(20);

        $communes = $this->communes();

        return view('elus.admin.users', compact('users', 'communes'));
    }

    /**
     * Show the CSV import form.
     */
    public function importForm(): View
    {
        return view('elus.admin.users.import');
    }

    /**
     * Process CSV file import to create élu users with profiles.
     */
    public function importCsv(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        if (! $handle) {
            return redirect()
                ->route('elus.admin.users.import.form')
                ->with('error', __('Impossible de lire le fichier.'));
        }

        // Skip BOM if present
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $created = 0;
        $skipped = [];
        $rowNumber = 0;
        $communes = $this->communes();

        while (($row = fgetcsv($handle, 0, "\t", '"')) !== false) {
            $rowNumber++;

            // Skip completely empty rows
            if (count($row) < 2 || empty(array_filter(array_map('trim', $row)))) {
                continue;
            }

            // Detect and skip header row
            if ($rowNumber === 1) {
                $firstCell = strtoupper(trim((string) ($row[0] ?? '')));
                if (str_contains($firstCell, 'CODE') || str_contains($firstCell, 'INSEE')) {
                    continue;
                }
            }

            try {
                $email = trim((string) ($row[16] ?? ''));

                if ($email === '' || $email === '0') {
                    $skipped[] = "Ligne {$rowNumber} : email vide";

                    continue;
                }

                if (User::where('email', $email)->exists()) {
                    $skipped[] = "Ligne {$rowNumber} : {$email} déjà existant";

                    continue;
                }

                $nom = trim((string) ($row[8] ?? ''));
                $prenom = trim((string) ($row[9] ?? ''));
                $titre = trim((string) ($row[13] ?? ''));
                $commune = trim((string) ($row[22] ?? ''));
                $tempPassword = Str::random(16);

                if ($commune !== '' && ! in_array($commune, $communes, true)) {
                    $commune = null;
                }

                $user = User::create([
                    'name' => $nom,
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'email' => $email,
                    'password' => Hash::make($tempPassword),
                    'is_elu' => true,
                    'fonction' => $titre !== '' ? $titre : null,
                    'commune' => $commune !== '' ? $commune : null,
                ]);

                EluProfile::create([
                    'user_id' => $user->id,
                    'code_insee' => $this->nullableString($row[0] ?? ''),
                    'collectivite' => $this->nullableString($row[1] ?? ''),
                    'epci_commune' => $this->nullableString($row[2] ?? ''),
                    'secteur' => $this->nullableString($row[3] ?? ''),
                    'nom_secteur' => $this->nullableString($row[4] ?? ''),
                    'date_deliberation' => $this->parseDate($row[5] ?? ''),
                    'visa_prefecture' => $this->nullableString($row[6] ?? ''),
                    'probleme_delib' => $this->nullableString($row[7] ?? ''),
                    'civilite' => $this->nullableString($row[11] ?? ''),
                    'rt_ds_dt' => $this->nullableString($row[12] ?? ''),
                    'titre' => $titre !== '' ? $titre : null,
                    'ordre_suppleants' => $this->parseInt($row[14] ?? ''),
                    'contact' => $this->nullableString($row[15] ?? ''),
                    'mail_personnel' => $email,
                    'mail_2' => $this->nullableString($row[17] ?? ''),
                    'telephone' => $this->nullableString($row[18] ?? ''),
                    'adresse_1' => $this->nullableString($row[19] ?? ''),
                    'adresse_2' => $this->nullableString($row[20] ?? ''),
                    'code_postal' => $this->nullableString($row[21] ?? ''),
                    'profession' => $this->nullableString($row[23] ?? ''),
                    'societe' => $this->nullableString($row[24] ?? ''),
                    'date_naissance' => $this->parseDate($row[25] ?? ''),
                    'newsletter' => $this->parseBoolean($row[27] ?? ''),
                    'frais_route' => $this->parseBoolean($row[29] ?? ''),
                    'rib_fourni' => $this->parseBoolean($row[30] ?? ''),
                    'chevaux_fiscaux' => $this->nullableString($row[31] ?? ''),
                ]);

                $created++;
            } catch (\Exception $e) {
                $skipped[] = "Ligne {$rowNumber} : erreur — {$e->getMessage()}";
            }
        }

        fclose($handle);

        $message = "{$created} élus importés avec succès.";
        if (count($skipped) > 0) {
            $message .= ' '.count($skipped).' lignes ignorées.';
        }

        return redirect()
            ->route('elus.admin.users.import.form')
            ->with('success', $message)
            ->with('skipped', $skipped)
            ->with('created_count', $created);
    }

    private function nullableString(mixed $value): ?string
    {
        $v = trim((string) $value);

        return $v !== '' ? $v : null;
    }

    private function parseInt(mixed $value): ?int
    {
        $v = trim((string) $value);

        return is_numeric($v) ? (int) $v : null;
    }

    private function parseBoolean(mixed $value): bool
    {
        $v = strtolower(trim((string) $value));

        return in_array($v, ['1', 'true', 'oui', 'yes', 'x', 'ok'], true);
    }

    private function parseDate(mixed $value): ?string
    {
        $v = trim((string) $value);

        if ($v === '' || $v === '0') {
            return null;
        }

        // Try common French date formats
        foreach (['d/m/Y', 'Y-m-d', 'd-m-Y', 'm/d/Y'] as $format) {
            $date = \DateTime::createFromFormat($format, $v);
            if ($date && $date->format($format) === $v) {
                return $date->format('Y-m-d');
            }
        }

        return null;
    }

    /**
     * Toggle élu status for a user.
     */
    public function toggleElu(User $user): RedirectResponse
    {
        $user->update(['is_elu' => ! $user->is_elu]);

        $status = $user->is_elu ? 'ajouté aux élus' : 'retiré des élus';

        return back()->with('success', "{$user->name} a été {$status}.");
    }

    /**
     * Quick create a new élu user.
     */
    public function storeElu(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'fonction' => ['nullable', 'string', 'max:255', Rule::in(['Délégué suppléant', 'Délégué titulaire', 'Représentant'])],
            'commune' => ['nullable', 'string', 'max:255', Rule::in($this->communes())],
        ]);

        // Generate a secure temporary password
        $tempPassword = Str::random(16);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($tempPassword),
            'is_elu' => true,
            'fonction' => $validated['fonction'] ?? null,
            'commune' => $validated['commune'] ?? null,
        ]);

        EluProfile::create([
            'user_id' => $user->id,
            'titre' => $validated['fonction'] ?? null,
        ]);

        return redirect()
            ->route('elus.admin.users')
            ->with('success', "Élu {$user->name} créé avec succès. Mot de passe temporaire : {$tempPassword} (copiez-le maintenant, il ne sera plus affiché).")
            ->with('temporaryPassword', $tempPassword)
            ->with('newUserName', $user->name);
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === request()->user()->id) {
            return back()->with('error', __('Vous ne pouvez pas supprimer votre propre compte.'));
        }

        $user->delete();

        return redirect()
            ->route('elus.admin.users')
            ->with('success', "{$user->name} a été supprimé.");
    }
}
