<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class EventType extends Model
{
    protected $fillable = ['name', 'slug', 'is_active', 'sort_order'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (EventType $eventType) {
            if (empty($eventType->slug)) {
                $eventType->slug = Str::slug($eventType->name);
            }
        });
    }

    public function packages(): BelongsToMany
    {
        return $this->belongsToMany(Package::class);
    }
}
