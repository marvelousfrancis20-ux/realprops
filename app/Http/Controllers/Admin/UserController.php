<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $role = $request->query('role');
        $q = $request->query('q');

        $query = User::query()->with('agentProfile')->withCount('properties')->latest();

        if (filled($role)) {
            $query->role($role);
        }

        if (filled($q)) {
            $query->where(fn ($inner) => $inner
                ->where('name', 'like', '%'.$q.'%')
                ->orWhere('email', 'like', '%'.$q.'%'));
        }

        return view('admin.users.index', [
            'users' => $query->paginate(15)->withQueryString(),
            'counts' => [
                'all' => User::count(),
                'user' => User::role('user')->count(),
                'agent' => User::role('agent')->count(),
                'admin' => User::role('admin')->count(),
                'suspended' => User::where('is_suspended', true)->count(),
            ],
            'activeRole' => $role,
        ]);
    }

    public function show(User $user): View
    {
        return view('admin.users.show', [
            'user' => $user->load('agentProfile')->loadCount(['properties', 'receivedInquiries', 'tourRequests']),
            'properties' => $user->properties()->with('images')->latest()->take(9)->get(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorize('changeRole', $user);

        $data = $request->validate([
            'role' => ['required', Rule::in(array_column(UserRole::cases(), 'value'))],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update($data);
        $user->syncRoles([$data['role']]);

        if ($data['role'] === UserRole::Agent->value) {
            $user->ensureAgentProfile();
        }

        activity()
            ->performedOn($user)
            ->causedBy($request->user())
            ->log('updated user role');

        return back()->with('status', 'User updated.');
    }

    public function suspend(Request $request, User $user): RedirectResponse
    {
        $this->authorize('suspend', $user);

        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $user->update([
            'is_suspended' => true,
            'suspended_at' => now(),
            'suspension_reason' => $data['reason'] ?? null,
        ]);

        activity()->performedOn($user)->causedBy($request->user())->log('suspended user');

        return back()->with('status', $user->name.' has been suspended.');
    }

    public function unsuspend(Request $request, User $user): RedirectResponse
    {
        $this->authorize('suspend', $user);

        $user->update([
            'is_suspended' => false,
            'suspended_at' => null,
            'suspension_reason' => null,
        ]);

        activity()->performedOn($user)->causedBy($request->user())->log('reinstated user');

        return back()->with('status', $user->name.' has been reinstated.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $user->delete();

        activity()->performedOn($user)->causedBy($request->user())->log('deleted user');

        return redirect()->route('admin.users.index')->with('status', 'User deleted.');
    }
}
