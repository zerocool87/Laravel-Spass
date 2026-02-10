<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'can:admin']);
    }

    private function communes(): array
    {
        $list = config('options.communes_haute_vienne', []);
        sort($list, SORT_STRING | SORT_FLAG_CASE);

        return $list;
    }

    public function index(Request $request): View
    {
        $users = User::orderBy('id', 'desc')->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.create', ['communes' => $this->communes()]);
    }

    public function store(AdminUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // generate a secure temporary password when creating from admin
        $temp = bin2hex(random_bytes(5)); // 10 chars

        $data['password'] = Hash::make($temp);
        $data['is_admin'] = $request->boolean('is_admin');
        $data['is_elu'] = $request->boolean('is_elu');
        $data['fonction'] = $request->input('fonction');
        // default to fictive commune SEHV when none selected
        $data['commune'] = $request->input('commune') ?: 'SEHV';

        $user = User::create($data);

        // flash the temporary password for admin to copy (only on creation)
        return Redirect::to('/elus/admin/users')->with('success', "User created. Temporary password: {$temp}");
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', ['user' => $user, 'communes' => $this->communes()]);
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
        $data['fonction'] = $request->input('fonction');
        // default to fictive commune SEHV when none selected
        $data['commune'] = $request->input('commune') ?: 'SEHV';

        $user->update($data);

        return Redirect::to('/elus/admin/users')->with('success', 'User updated.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return Redirect::route('admin.users.index')->with('success', 'User deleted.');
    }
}
