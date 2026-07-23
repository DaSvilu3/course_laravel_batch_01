<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = '%'.$request->string('q').'%';
                $q->where(fn ($w) => $w->where('name', 'like', $term)->orWhere('email', 'like', $term));
            })
            ->when($request->filled('role'), fn ($q) => $q->where('role', $request->string('role')))
            ->withCount('orders')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', ['users' => $users]);
    }

    public function create(): View
    {
        return view('admin.users.form', ['user' => new User(['role' => UserRole::User])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateUser($request);
        $data['email_verified_at'] = now();

        User::create($data);

        return redirect()->route('admin.users.index')->with('status', __('admin.saved'));
    }

    public function edit(User $user): View
    {
        return view('admin.users.form', ['user' => $user]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $this->validateUser($request, $user);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        // Never let the last admin lock everyone out of the panel.
        if ($user->isAdmin() && ($data['role'] ?? null) !== UserRole::Admin->value && User::admins()->count() <= 1) {
            return back()->withErrors(['role' => __('admin.last_admin')]);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('status', __('admin.saved'));
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['user' => __('admin.cannot_delete_self')]);
        }

        if ($user->isAdmin() && User::admins()->count() <= 1) {
            return back()->withErrors(['user' => __('admin.last_admin')]);
        }

        $user->delete();

        return back()->with('status', __('admin.deleted'));
    }

    private function validateUser(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user)],
            'phone' => ['nullable', 'string', 'max:32'],
            'role' => ['required', Rule::in(array_column(UserRole::cases(), 'value'))],
            'is_active' => ['boolean'],
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Password::defaults()],
        ]) + ['is_active' => $request->boolean('is_active')];
    }
}
