<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\RespondsWithAdminJson;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRolesRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserAdminController extends Controller
{
    use RespondsWithAdminJson;

    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        return view('admin.users.index', [
            'users' => User::query()->with('roles')->orderBy('name')->paginate(30),
        ]);
    }

    public function edit(User $user): View
    {
        $this->authorize('view', $user);

        $roles = Role::query()->where('guard_name', 'web')->orderBy('name')->get();

        return view('admin.users.edit-roles', [
            'user' => $user->load('roles'),
            'roles' => $roles,
            'canManageRoles' => auth()->user()?->can('manageRoles', $user) ?? false,
        ]);
    }

    public function updateRoles(UpdateUserRolesRequest $request, User $user): JsonResponse|RedirectResponse
    {
        $names = $request->validated('roles') ?? [];
        $user->syncRoles($names);

        return $this->adminRespond($request, 'Roles updated.', route('admin.users.edit', $user));
    }
}
