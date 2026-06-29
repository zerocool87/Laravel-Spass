<?php

declare(strict_types=1);

namespace App\Http\Controllers\Elus;

use App\Http\Controllers\Controller;
use App\Models\EluProfile;
use App\Models\User;
use App\Services\CsvImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        return view('elus.admin.index');
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

        $users = $query->orderBy('name')->paginate(20)->withQueryString();

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

        $result = (new CsvImportService($this->communes()))->import($request->file('csv_file'));

        $message = "{$result['created']} élus importés avec succès.";
        $skippedCount = count($result['skipped']);
        if ($skippedCount > 0) {
            $message .= " {$skippedCount} lignes ignorées.";
        }

        return redirect()
            ->route('elus.admin.users.import.form')
            ->with('success', $message)
            ->with('skipped', $result['skipped'])
            ->with('created_count', $result['created']);
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
            'titres' => ['nullable', 'array'],
            'titres.*' => ['string', Rule::in(config('options.titres', []))],
            'commune' => ['nullable', 'string', 'max:255', Rule::in($this->communes())],
        ]);

        $user = DB::transaction(function () use ($validated) {
            $tempPassword = Str::random(16);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $tempPassword,
                'is_elu' => true,
                'titres' => $validated['titres'] ?? null,
                'commune' => $validated['commune'] ?? null,
            ]);

            EluProfile::create([
                'user_id' => $user->id,
                'titres' => $validated['titres'] ?? null,
            ]);

            return $user;
        });

        $resetLink = url(route('password.request', [], false));

        return redirect()
            ->route('elus.admin.users')
            ->with('success', "Élu {$user->name} créé avec succès. Un lien de réinitialisation de mot de passe lui a été attribué.")
            ->with('resetLink', $resetLink);
    }

    /**
     * Delete a user.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', __('Vous ne pouvez pas supprimer votre propre compte.'));
        }

        $user->delete();

        return redirect()
            ->route('elus.admin.users')
            ->with('success', "{$user->name} a été supprimé.");
    }
}
