<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AccountSettingsController extends Controller
{
    public function edit(Request $request)
    {
        $userId = (int) $request->session()->get('user_id');
        $user = User::findOrFail($userId);

        return view('account.settings', [
            'sidebar_active' => null,
            'user' => $user,
        ]);
    }

    public function update(Request $request)
    {
        $userId = (int) $request->session()->get('user_id');
        $user = User::findOrFail($userId);

        $validator = Validator::make($request->all(), [
            'fullname'       => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'username'       => ['required', 'string', 'max:255'],
            'password'       => ['nullable', 'string', 'min:6', 'max:255'],
            'profile_image'  => ['nullable', 'image', 'max:2048'],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('account.settings.edit')
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        if (empty($data['password'] ?? null)) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            if ($file && $file->isValid()) {
                // delete old image if exists
                if ($user->profile_image) {
                    Storage::disk('public')->delete($user->profile_image);
                }
                $path = $file->store('profile-images', 'public');
                $data['profile_image'] = $path;
            }
        }

        $user->update($data);

        // refresh session for header display
        $request->session()->put('user_name', $user->fullname);
        $request->session()->put('user_email', $user->email);
        $request->session()->put('user_username', $user->username);
        $request->session()->put('user_profile_image', $user->profile_image);

        return redirect()
            ->route('account.settings.edit')
            ->with('success', 'Account settings updated successfully.');
    }

    public function profileImage(Request $request)
    {
        $userId = (int) $request->session()->get('user_id');
        $user = User::findOrFail($userId);

        if (!$user->profile_image || !Storage::disk('public')->exists($user->profile_image)) {
            abort(404);
        }

        return Storage::disk('public')->response($user->profile_image);
    }
}

