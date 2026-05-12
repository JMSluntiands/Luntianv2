<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\DashboardJobStatsService;
use App\Services\PublicHolidayService;
use App\Support\LoginReturnPath;
use Illuminate\Http\Request;
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

        if (! $user || ! Hash::check($request->password, $user->password)) {
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

        $branch = $user->branch ?? null;
        $branch = ($branch !== null && trim((string) $branch) !== '') ? trim((string) $branch) : '';

        session([
            'user_id' => $user->id,
            'user_name' => $user->fullname,
            'user_email' => $user->email,
            'user_username' => $user->username,
            'user_role' => $user->role,
            'user_branch' => $branch,
            'user_profile_image' => $user->profile_image,
        ]);

        $redirectTarget = $this->resolveSafeRedirectTarget($request);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => $redirectTarget,
            ]);
        }

        return redirect()->to($redirectTarget);
    }

    public function dashboard()
    {
        if (! session()->has('user_id')) {
            $uri = request()->getRequestUri();
            if (LoginReturnPath::isAllowed($uri)) {
                session(['url.intended' => LoginReturnPath::absoluteUrlFor(request(), $uri)]);

                return redirect('/?return_to='.rawurlencode($uri))
                    ->with('error', 'Please log in first.');
            }
            session(['url.intended' => request()->fullUrl()]);

            return redirect('/')->with('error', 'Please log in first.');
        }
        $holidaysYear = (int) now()->format('Y');

        return view('dashboard', [
            'dashboardStats' => DashboardJobStatsService::fetch(),
            'dashboardPublicHolidays' => PublicHolidayService::forYear($holidaysYear),
            'dashboardPublicHolidaysYear' => $holidaysYear,
        ]);
    }

    public function logout(Request $request)
    {
        session()->forget(['user_id', 'user_name', 'user_email', 'user_username', 'user_role', 'user_branch', 'user_profile_image']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Use session url.intended (set by redirect()->guest()) only if it stays on this app (avoid open redirects).
     */
    private function resolveSafeRedirectTarget(Request $request): string
    {
        $fallback = route('dashboard');
        $candidate = $request->session()->pull('url.intended');
        if ($candidate === null || trim((string) $candidate) === '') {
            return $fallback;
        }

        $candidate = trim((string) $candidate);
        $requestHost = strtolower((string) $request->getHost());
        $appUrlHost = parse_url(rtrim((string) config('app.url'), '/'), PHP_URL_HOST);
        $appUrlHost = is_string($appUrlHost) ? strtolower($appUrlHost) : null;

        if (str_starts_with($candidate, '/') && ! str_starts_with($candidate, '//')) {
            return rtrim($request->getSchemeAndHttpHost(), '/') . $candidate;
        }

        if (! filter_var($candidate, FILTER_VALIDATE_URL)) {
            return $fallback;
        }

        $candidateHost = parse_url($candidate, PHP_URL_HOST);
        $candidateHost = is_string($candidateHost) ? strtolower($candidateHost) : null;
        if ($candidateHost === null || $candidateHost === '') {
            return $fallback;
        }

        if ($candidateHost === $requestHost || ($appUrlHost !== null && $candidateHost === $appUrlHost)) {
            return $candidate;
        }

        return $fallback;
    }
}
