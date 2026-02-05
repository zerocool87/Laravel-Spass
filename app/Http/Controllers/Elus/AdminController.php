<?php

namespace App\Http\Controllers\Elus;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;

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

        return view('elus.admin.index', compact('stats', 'recentUsers'));
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

        // Filter by territory
        if ($request->filled('territory')) {
            $query->where('territory', $request->territory);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('commune', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->orderBy('name')->paginate(20);

        // Get unique territories for filter
        $territories = User::whereNotNull('territory')
            ->distinct()
            ->pluck('territory')
            ->sort();

        return view('elus.admin.users', compact('users', 'territories'));
    }

    /**
     * Toggle élu status for a user.
     */
    public function toggleElu(User $user): RedirectResponse
    {
        $user->update(['is_elu' => !$user->is_elu]);

        $status = $user->is_elu ? 'ajouté aux élus' : 'retiré des élus';

        return back()->with('success', "{$user->name} a été {$status}.");
    }

    /**
     * Update user territory.
     */
    public function updateTerritory(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'territory' => 'nullable|string|max:255',
        ]);

        $user->update(['territory' => $request->territory]);

        return back()->with('success', "Territoire de {$user->name} mis à jour.");
    }

    /**
     * Quick create a new élu user.
     */
    public function storeElu(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'fonction' => 'nullable|string|max:255',
            'commune' => 'nullable|string|max:255',
            'territory' => 'nullable|string|max:255',
        ]);

        // Generate a random password
        $tempPassword = bin2hex(random_bytes(5));

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($tempPassword),
            'is_elu' => true,
            'fonction' => $validated['fonction'] ?? null,
            'commune' => $validated['commune'] ?? 'SEHV',
            'territory' => $validated['territory'] ?? null,
        ]);

        return redirect()
            ->route('elus.admin.users')
            ->with('success', "Élu {$user->name} créé. Mot de passe temporaire: {$tempPassword}");
    }
}
