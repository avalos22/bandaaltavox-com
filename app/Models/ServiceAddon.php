<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceAddon extends Model
{
    protected $fillable = [
        'name', 'category', 'description', 'price', 'unit', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public const CATEGORIES = [
        'extra_hours' => 'Horas Extra',
        'equipment' => 'Equipo',
        'effects' => 'Efectos Especiales',
        'entertainment' => 'Entretenimiento',
        'other' => 'Otros',
    ];
}
