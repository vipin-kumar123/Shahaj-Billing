<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileService
{

    public function editProfile()
    {
        return User::find(Auth::id());
    }

    public function profileUpdate(array $data)
    {
        $user = User::find(Auth::id());

        $folder = 'assets/backend/uploads/users/';

        if (isset($data['photo']) && $data['photo']->isValid()) {

            if ($user->photo && file_exists(public_path($folder . $user->photo))) {
                @unlink(public_path($folder . $user->photo));
            }

            $filename = time() . '_' . uniqid() . '.' . $data['photo']->getClientOriginalExtension();
            $data['photo']->move(public_path($folder), $filename);

            $data['photo'] = $folder . $filename;
        } else {
            unset($data['photo']);
        }
        return $user->update($data);
    }


    public function updatePassword(array $data)
    {
        $user = Auth::user();

        if (!Hash::check($data['old_password'], $user->password)) {
            return false;
        }

        $user->password = Hash::make($data['password']);
        return $user->save();
    }
}
