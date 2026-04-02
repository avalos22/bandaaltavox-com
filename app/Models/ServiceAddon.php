<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceAddon extends Model
{
    protected $fillable = [
        'name', 'category', 'subcategory', 'description',
        'price', 'supplier_price', 'unit', 'duration',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'supplier_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public const CATEGORIES = [
        'audio'                  => 'Audio',
        'pantallas_video'        => 'Pantallas y Video',
        'iluminacion'            => 'Iluminación',
        'efectos_especiales'     => 'Efectos Especiales',
        'mobiliario'             => 'Mobiliario',
        'entretenimiento'        => 'Entretenimiento',
        'produccion_estructuras' => 'Producción / Estructuras',
        'produccion_logistica'   => 'Producción / Logística',
        'video'                  => 'Video',
    ];

    public const SUBCATEGORIES = [
        'audio'                  => ['Sistemas de audio'],
        'pantallas_video'        => ['Pantallas'],
        'iluminacion'            => ['Iluminación ambiental'],
        'efectos_especiales'     => ['Confeti / CO₂', 'Niebla', 'Pirotecnia fría'],
        'mobiliario'             => ['Decoración', 'Pistas de baile', 'Sillas', 'Mesas'],
        'entretenimiento'        => ['Botargas', 'Grupo musical'],
        'produccion_estructuras' => ['Escenarios', 'Toldos'],
        'produccion_logistica'   => ['Transporte', 'Montaje'],
        'video'                  => [],
    ];
}
