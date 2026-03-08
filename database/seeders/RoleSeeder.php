<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Artisan::call('permission:generate');
        $adminRole = \Spatie\Permission\Models\Role::updateOrCreate(['name' => 'Super-admin'], [
            'name' => 'Super-admin',
            'guard_name' => 'web',
        ]);
        $adminRole->syncPermissions(Permission::pluck('id')->toArray());
        $permissions = config('permissions', []);
        foreach ($permissions as $value) {
            Permission::updateOrCreate(['name' => $value], [
                'name' => $value,
                'guard_name' => 'web',
            ]);
        }

        $roles = [
            'Super-admin' => [
                Role::class => ['list', 'view', 'create', 'update', 'delete'], // This is just a placeholder, Super-admin will get all permissions anyway
                User::class => ['list', 'view', 'create', 'update', 'delete'],
                Permission::class => ['list', 'view', 'create', 'update', 'delete'],
            ], // All permissions are automatically assigned to Super-admin

        ];

        foreach ($roles as $role => $permArray) {
            $role = \Spatie\Permission\Models\Role::updateOrCreate(['name' => $role], [
                'name' => $role,
                'guard_name' => 'web',
            ]);
            foreach ($permArray as $class => $permissionsList) {
                $table = (new $class)->getTable();
                foreach ($permissionsList as $p) {
                    $pname = $p . ' ' . $table;
                    Permission::updateOrCreate(['name' => $pname], [
                        'name' => $pname,
                        'guard_name' => 'web',
                    ]);
                    $role->givePermissionTo($pname);
                }
            }
        }
    }
}
