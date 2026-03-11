<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required',
        ]);

        $login = $request->input('login');
        $user = User::where(function ($q) use ($login) {
                $q->where('email', $login)->orWhere('username', $login);
            })
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid username/email or password.',
                ], 422);
            }
            throw ValidationException::withMessages([
                'login' => ['Invalid username/email or password.'],
            ]);
        }

        session([
            'user_id' => $user->id,
            'user_name' => $user->fullname,
            'user_email' => $user->email,
            'user_username' => $user->username,
            'user_role' => $user->role,
            'user_profile_image' => $user->profile_image,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => route('dashboard'),
            ]);
        }

        return redirect()->intended(route('dashboard'));
    }

    public function dashboard()
    {
        if (!session()->has('user_id')) {
            return redirect()->route('login')->with('error', 'Please log in first.');
        }
        return view('dashboard');
    }

    public function logout(Request $request)
    {
        session()->forget(['user_id', 'user_name', 'user_email', 'user_username', 'user_role', 'user_profile_image']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
