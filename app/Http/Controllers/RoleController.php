<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index(Request $request): View
    {
        $roles = Role::orderBy('id', 'DESC')->paginate(10);
        return view('role.index', compact('roles'))
            ->with('i', ($request->input('page', 1) - 1) * 10);
    }

    public function create(): View
    {
        return view('role.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
        ]);

        Role::create(['name' => $request->input('name')]);

        return redirect()->route('roles.index')
            ->with('success', 'Rôle créé avec succès');
    }

    public function show($id): View
    {
        $role = Role::find($id);
        $permissions = Permission::all();
        $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id", $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();

        return view('role.show', compact('role', 'permissions', 'rolePermissions'));
    }

    public function edit($id): View
    {
        $role = Role::find($id);
        return view('role.edit', compact('role'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();

        return redirect()->route('roles.index')
            ->with('success', 'Rôle mis à jour avec succès');
    }

    public function destroy($id): RedirectResponse
    {
        DB::table("roles")->where('id', $id)->delete();
        return redirect()->route('roles.index')
            ->with('success', 'Rôle supprimé avec succès');
    }

    public function updatePermissions(Request $request, $id): RedirectResponse
    {
        $role = Role::find($id);
        $role->syncPermissions($request->input('permissions') ?? []);

        return redirect()->route('roles.show', $id)
            ->with('success', 'Permissions mises à jour avec succès');
    }
}
