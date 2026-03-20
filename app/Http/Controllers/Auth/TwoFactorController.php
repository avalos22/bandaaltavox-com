<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\TwoFactorCodeNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorController extends Controller
{
    /**
     * Show the 2FA setup page (QR code for TOTP users).
     */
    public function setup(Request $request): Response|RedirectResponse
    {
        $user = $request->user();

        if ($user->hasTwoFactorEnabled()) {
            return $this->redirectByRole($user);
        }

        // Email-based 2FA doesn't need setup — auto-confirm and go to challenge
        if ($user->two_factor_type === 'email') {
            $user->update(['two_factor_confirmed_at' => now()]);
            return redirect()->route('two-factor.challenge');
        }

        if ($user->two_factor_type !== 'totp') {
            return $this->redirectByRole($user);
        }

        $google2fa = new Google2FA();

        // Generate secret if not yet set
        if (!$user->two_factor_secret) {
            $secret = $google2fa->generateSecretKey();
            $user->update(['two_factor_secret' => Crypt::encryptString($secret)]);
        } else {
            $secret = Crypt::decryptString($user->two_factor_secret);
        }

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            'Banda Alta Vox',
            $user->email,
            $secret
        );

        return Inertia::render('Auth/TwoFactorSetup', [
            'qrCodeUrl' => $qrCodeUrl,
            'secret' => $secret,
        ]);
    }

    /**
     * Confirm the TOTP setup by verifying the first code.
     */
    public function confirmSetup(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = $request->user();
        $secret = Crypt::decryptString($user->two_factor_secret);

        $google2fa = new Google2FA();

        if (!$google2fa->verifyKey($secret, $request->code)) {
            return back()->withErrors(['code' => 'El código es inválido. Intenta de nuevo.']);
        }

        $user->update(['two_factor_confirmed_at' => now()]);
        $request->session()->put('two_factor_verified', true);

        return $this->redirectByRole($user);
    }

    /**
     * Show the 2FA challenge page (after login).
     */
    public function challenge(Request $request): Response|RedirectResponse
    {
        $user = $request->user();

        if (!$user || !$user->hasTwoFactorEnabled()) {
            return redirect()->route('login');
        }

        if ($request->session()->get('two_factor_verified')) {
            return $this->redirectByRole($user);
        }

        // For email-based 2FA, send the code automatically
        if ($user->two_factor_type === 'email') {
            $this->sendEmailCode($user);
        }

        return Inertia::render('Auth/TwoFactorChallenge', [
            'method' => $user->two_factor_type,
            'email' => Str::mask($user->email, '*', 3, -10),
        ]);
    }

    /**
     * Verify the 2FA code.
     */
    public function verify(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        $user = $request->user();

        if ($user->two_factor_type === 'totp') {
            $valid = $this->verifyTotpCode($user, $request->code);
        } else {
            $valid = $this->verifyEmailCode($user, $request->code);
        }

        if (!$valid) {
            return back()->withErrors(['code' => 'El código es inválido o ha expirado.']);
        }

        $request->session()->put('two_factor_verified', true);

        // Clear email code after successful verification
        if ($user->two_factor_type === 'email') {
            $user->update([
                'two_factor_email_code' => null,
                'two_factor_email_expires_at' => null,
            ]);
        }

        return $this->redirectByRole($user);
    }

    /**
     * Resend the email 2FA code.
     */
    public function resend(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->two_factor_type !== 'email') {
            return back();
        }

        $this->sendEmailCode($user);

        return back()->with('status', 'Se ha enviado un nuevo código a tu correo.');
    }

    /**
     * Send a 6-digit code via email.
     */
    private function sendEmailCode(User $user): void
    {
        // Don't resend if there's a valid unexpired code (anti-spam)
        if ($user->two_factor_email_code && $user->two_factor_email_expires_at?->isFuture()) {
            return;
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'two_factor_email_code' => $code,
            'two_factor_email_expires_at' => now()->addMinutes(10),
        ]);

        $user->notify(new TwoFactorCodeNotification($code));
    }

    private function verifyTotpCode(User $user, string $code): bool
    {
        $secret = Crypt::decryptString($user->two_factor_secret);
        $google2fa = new Google2FA();

        return $google2fa->verifyKey($secret, $code);
    }

    private function verifyEmailCode(User $user, string $code): bool
    {
        if (!$user->two_factor_email_code || !$user->two_factor_email_expires_at) {
            return false;
        }

        if ($user->two_factor_email_expires_at->isPast()) {
            return false;
        }

        return hash_equals($user->two_factor_email_code, $code);
    }

    private function redirectByRole(User $user): RedirectResponse
    {
        if ($user->hasRole('Cliente')) {
            return redirect()->intended(route('client.dashboard'));
        }

        return redirect()->intended(route('admin.dashboard'));
    }
}
