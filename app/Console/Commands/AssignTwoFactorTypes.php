<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class AssignTwoFactorTypes extends Command
{
    protected $signature = 'users:assign-2fa';
    protected $description = 'Assign 2FA type to existing users who do not have one yet';

    public function handle(): int
    {
        $updated = 0;

        User::whereNull('two_factor_type')->chunk(100, function ($users) use (&$updated) {
            foreach ($users as $user) {
                $type = $user->hasRole('Cliente') ? 'email' : 'totp';
                $user->update([
                    'two_factor_type' => $type,
                    'two_factor_confirmed_at' => $type === 'email' ? now() : null,
                ]);
                $updated++;
            }
        });

        $this->info("Assigned 2FA type to {$updated} users.");

        return self::SUCCESS;
    }
}
