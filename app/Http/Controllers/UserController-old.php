<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $q = $request->string('q')->toString();
        $users = User::query()
            ->search($q)
            ->orderByDesc('created_at')
            ->paginate()
            ->appends(['q' => $q]);

        return view('user.index', compact('users', 'q'))
            ->with('i', ($request->input('page', 1) - 1) * $users->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $user = new User();
        $roles = Role::all();
        $permissions = Permission::all();

        return view('user.create', compact('user', 'roles', 'permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make('password'); // Default password or handle via request

        $user = User::create($data);

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        if ($request->has('permissions')) {
            $user->syncPermissions($request->permissions);
        }

        return Redirect::route('users.index')
            ->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $user = User::find($id);

        return view('user.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $user = User::find($id);
        $roles = Role::all();
        $permissions = Permission::all();

        return view('user.edit', compact('user', 'roles', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $user->update($request->validated());

        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        } else {
            $user->syncRoles([]);
        }

        if ($request->has('permissions')) {
            $user->syncPermissions($request->permissions);
        } else {
            $user->syncPermissions([]);
        }

        return Redirect::route('users.index')
            ->with('success', 'Utilisateur mis à jour avec succès');
    }

    public function destroy($id): RedirectResponse
    {
        User::find($id)->delete();

        return Redirect::route('users.index')
            ->with('success', 'Utilisateur supprimé avec succès');
    }
}