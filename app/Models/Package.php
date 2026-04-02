<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Package extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'price', 'duration_hours',
        'required_addon_subcategory',
        'image', 'is_active', 'is_featured', 'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Package $package) {
            if (empty($package->slug)) {
                $package->slug = Str::slug($package->name);
            }
        });
    }

    public function includes(): HasMany
    {
        return $this->hasMany(PackageInclude::class)->orderBy('sort_order');
    }

    public function eventTypes(): BelongsToMany
    {
        return $this->belongsToMany(EventType::class);
    }
}
