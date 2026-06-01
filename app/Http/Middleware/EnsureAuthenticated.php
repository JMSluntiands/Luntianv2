<?php

namespace App\Http\Middleware;

use App\Support\LoginReturnPath;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! session()->has('user_id')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            // Session-only url.intended can be dropped across redirects (e.g. Slack in-app browser).
            // Also append return_to on / so the login shell can restore the target from the query string.
            $uri = $request->getRequestUri();
            if (LoginReturnPath::isAllowed($uri)) {
                $request->session()->put('url.intended', LoginReturnPath::absoluteUrlFor($request, $uri));

                return redirect('/?return_to='.rawurlencode($uri))
                    ->with('error', 'Please log in first.');
            }

            $request->session()->put('url.intended', $request->fullUrl());

            return redirect('/')->with('error', 'Please log in first.');
        }

        return $next($request);
    }
}
