<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    protected $profileservice;

    public function __construct(ProfileService $profileservice)
    {
        $this->profileservice = $profileservice;
    }

    public function edit()
    {
        $user = $this->profileservice->editProfile();
        return view('backend.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,gif',
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|digits:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $this->profileservice->profileUpdate($validator->validate());

        return response()->json([
            'success' => true,
            'message' => 'Profile has been updated successfully!'
        ]);
    }


    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password'     => 'required',
            'password'     => 'required|min:6',
            'confirm_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $data = $this->profileservice->updatePassword($validator->validate());

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Password not updated'
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully'
        ]);
    }
}
