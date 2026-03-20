<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        // Override mail config from database settings if available
        try {
            $encryptedKey = Setting::where('key', 'resend_api_key')->value('value');
            $apiKey = null;
            if ($encryptedKey) {
                try {
                    $apiKey = \Illuminate\Support\Facades\Crypt::decryptString($encryptedKey);
                } catch (\Throwable) {
                    // Value not yet encrypted (migration period) — use as-is
                    $apiKey = $encryptedKey;
                }
            }
            if ($apiKey) {
                $fromAddress = Setting::where('key', 'mail_from_address')->value('value');
                $fromName    = Setting::where('key', 'mail_from_name')->value('value');
                $replyTo     = Setting::where('key', 'mail_reply_to')->value('value');

                config([
                    'mail.mailer'           => 'resend',
                    'mail.from.address'     => $fromAddress ?: config('mail.from.address'),
                    'mail.from.name'        => $fromName    ?: config('mail.from.name'),
                    'services.resend.key'   => $apiKey,
                ]);

                if ($replyTo) {
                    config(['mail.reply_to.address' => $replyTo]);
                }
            }
        } catch (\Throwable) {
            // DB may not be available during migrations — silently skip
        }
    }
}
