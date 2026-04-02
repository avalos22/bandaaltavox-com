<?php

namespace App\Http\Middleware;

use App\Models\TrustedDevice;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        // If user needs to set up 2FA (assigned but not confirmed)
        if ($user->needsTwoFactorSetup()) {
            if (!$request->routeIs('two-factor.*', 'logout')) {
                return redirect()->route('two-factor.setup');
            }
        }

        // If user has 2FA enabled but hasn't verified this session
        if ($user->hasTwoFactorEnabled() && !$request->session()->get('two_factor_verified')) {
            // Check if this device is trusted
            $cookieToken = $request->cookie('two_factor_trusted');
            if ($cookieToken && TrustedDevice::findValid($user->id, $cookieToken)) {
                $request->session()->put('two_factor_verified', true);
                return $next($request);
            }

            if (!$request->routeIs('two-factor.*', 'logout')) {
                return redirect()->route('two-factor.challenge');
            }
        }

        return $next($request);
    }
}
