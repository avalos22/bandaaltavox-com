<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageInclude extends Model
{
    protected $fillable = ['package_id', 'description', 'is_highlighted', 'sort_order'];

    protected $casts = [
        'is_highlighted' => 'boolean',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}
