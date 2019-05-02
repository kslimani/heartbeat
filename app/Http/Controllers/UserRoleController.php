<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Role;
use App\User;

class UserRoleController extends Controller
{
    public function index($id)
    {
        $user = User::findOrFail($id);

        $userRoles = $user->roles()
            ->orderBy('name')
            ->paginate(config('app.pagination_limit'));

        $roles = Role::orderBy('name')->get();

        return view('user-roles/index', [
            'user' => $user,
            'userRoles' => $userRoles,
            'roles' => $roles,
        ]);
    }

    public function add(Request $request, $userId)
    {
        $request->validate([
            'role' => ['required', 'integer', 'min:1'],
        ]);

        $roleId = $request->input('role');

        $user = User::findOrFail($userId);
        $role = Role::findOrFail($roleId);

        $user->roles()->syncWithoutDetaching([$role->id]);

        return redirect()->route('user-roles.index', ['user' => $user->id]);
    }

    public function remove(Request $request, $userId, $roleId)
    {
        $user = User::findOrFail($userId);
        $role = Role::findOrFail($roleId);

        // Admin role can be removed only by another admin user
        if ($request->user()->id === $user->id
            && $role->name === Role::ADMIN) {

            return redirect()
                ->route('user-roles.index', ['user' => $user->id])
                ->with('alert.danger', __('app.not_permitted'));
        }

        $user->roles()->detach($role->id);

        return redirect()->route('user-roles.index', ['user' => $user->id]);
    }
}
