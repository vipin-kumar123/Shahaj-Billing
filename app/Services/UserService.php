<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Request;

class UserService
{

    public function userList()
    {
        return User::latest();
    }

    public function getrole()
    {
        return Role::get();
    }

    public function storeUser(array $data)
    {
        $user = User::create([
            'name' => trim($data['name']),
            'email' => trim($data['email']),
            'phone' => isset($data['phone']) ? trim($data['phone']) : null,
            'password' => Hash::make($data['password']),
        ]);

        // Assign role
        $role = Role::find($data['role_id']);
        $user->assignRole($role); // OR ->syncRoles()

        return $user;
    }

    public function editUser($id)
    {
        return User::findOrFail($id);
    }


    public function showUser($id)
    {
        return User::with('roles.permissions')->findOrFail($id);
    }


    public function updateUser(array $data, $id)
    {
        $user = User::findOrFail($id);

        // Update basic fields
        $user->name  = $data['name'];
        $user->email = $data['email'] ?? $user->email;
        $user->phone = $data['phone'] ?? $user->phone;

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        // ---- ROLE UPDATE SAME AS YOUR WORKING CODE ----
        if (isset($data['role_id'])) {

            // 1. Delete all old roles
            DB::table('model_has_roles')->where('model_id', $id)->delete();

            // 2. Find role name from role_id
            $role = Role::findById($data['role_id']);  // ID → ROLE MODEL

            // 3. Assign role by name
            $user->assignRole($role->name);
        }

        return $user;
    }


    public function userIsactive(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->is_active = $request->status;
        $user->save();
        return $user;
    }
}
