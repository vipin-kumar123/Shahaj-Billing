<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;


class RoleService
{
    public function GetRoles()
    {
        return Role::latest();
    }

    public function getPermission()
    {
        return Permission::all();
    }

    public function storeRole(array $data): Role
    {
        $role = Role::create([
            'name' => $data['name'],
            'guard_name' => 'web',
        ]);

        // ATTACH PERMISSIONS (expects permission NAMES)
        $role->syncPermissions($data['permission_id']);

        return $role;
    }

    public function editRole(string $id)
    {
        return Role::findOrFail($id);
    }

    public function showRole($id)
    {
        return Role::with('permissions')->findOrFail($id);
    }


    public function roleUpdate(array $data, Role $role): Role
    {
        // Update role name
        $role->update([
            'name' => $data['name'],
        ]);

        // Sync permissions (expects NAMES)
        $role->syncPermissions($data['permission_id']);

        return $role;
    }
}
