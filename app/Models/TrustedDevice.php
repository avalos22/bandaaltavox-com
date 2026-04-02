<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrustedDevice extends Model
{
    protected $fillable = ['user_id', 'token', 'expires_at'];

    protected $casts = ['expires_at' => 'datetime'];

    public $timestamps = false;

    public static function findValid(int $userId, string $plainToken): ?self
    {
        return static::where('user_id', $userId)
            ->where('token', hash('sha256', $plainToken))
            ->where('expires_at', '>', now())
            ->first();
    }
}
