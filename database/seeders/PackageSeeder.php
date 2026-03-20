<?php

namespace Database\Seeders;

use App\Models\EventType;
use App\Models\Package;
use App\Models\PackageInclude;
use App\Models\ServiceAddon;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Event Types ────────────────────────────
        $eventTypes = [
            ['name' => 'Boda', 'slug' => 'boda', 'sort_order' => 1],
            ['name' => 'XV Años', 'slug' => 'xv-anos', 'sort_order' => 2],
            ['name' => 'Bautizo', 'slug' => 'bautizo', 'sort_order' => 3],
            ['name' => 'Posada', 'slug' => 'posada', 'sort_order' => 4],
            ['name' => 'Cumpleaños', 'slug' => 'cumpleanos', 'sort_order' => 5],
            ['name' => 'Evento Corporativo', 'slug' => 'corporativo', 'sort_order' => 6],
            ['name' => 'Bar / Restaurante', 'slug' => 'bar-restaurante', 'sort_order' => 7],
            ['name' => 'Graduación', 'slug' => 'graduacion', 'sort_order' => 8],
            ['name' => 'Otro', 'slug' => 'otro', 'sort_order' => 9],
        ];

        foreach ($eventTypes as $et) {
            EventType::firstOrCreate(['slug' => $et['slug']], $et);
        }

        // ─── Package: Altavox ───────────────────────
        $altavox = Package::firstOrCreate(['slug' => 'altavox'], [
            'name' => 'Altavox',
            'description' => 'Cinco horas de repertorio musical versátil. Más que una banda, una experiencia en vivo que transforma tu evento en un recuerdo inolvidable.',
            'price' => 43000.00,
            'duration_hours' => 5,
            'is_active' => true,
            'is_featured' => true,
            'sort_order' => 1,
        ]);

        $altavoxIncludes = [
            ['description' => 'Servicio de audio e iluminación profesional', 'is_highlighted' => true, 'sort_order' => 1],
            ['description' => 'Bono de animación con 6 efectos de pirotecnia fría', 'is_highlighted' => true, 'sort_order' => 2],
            ['description' => 'Collares hawaianos de tela y accesorios luminosos', 'is_highlighted' => false, 'sort_order' => 3],
            ['description' => 'Aproximadamente 100 artículos de animación', 'is_highlighted' => true, 'sort_order' => 4],
            ['description' => 'Dos botargas temáticas (Juan Gabriel y Luis Miguel)', 'is_highlighted' => false, 'sort_order' => 5],
        ];

        foreach ($altavoxIncludes as $include) {
            PackageInclude::firstOrCreate(
                ['package_id' => $altavox->id, 'description' => $include['description']],
                $include + ['package_id' => $altavox->id]
            );
        }

        // Assign Altavox to event types
        $altavox->eventTypes()->sync(
            EventType::whereIn('slug', ['boda', 'xv-anos', 'bautizo', 'cumpleanos', 'graduacion'])->pluck('id')
        );

        // ─── Service Add-ons ────────────────────────
        $addons = [
            // Extra hours
            ['name' => 'Hora extra - Norteño', 'category' => 'extra_hours', 'description' => 'Una hora adicional con grupo norteño', 'price' => 5500.00, 'unit' => 'por hora', 'sort_order' => 1],
            ['name' => 'Hora extra - Norteño Banda', 'category' => 'extra_hours', 'description' => 'Una hora adicional con norteño banda', 'price' => 5500.00, 'unit' => 'por hora', 'sort_order' => 2],
            ['name' => 'Hora extra - Banda Sinaloense', 'category' => 'extra_hours', 'description' => 'Una hora adicional con banda sinaloense', 'price' => 8000.00, 'unit' => 'por hora', 'sort_order' => 3],
            ['name' => 'Hora extra - DJ', 'category' => 'extra_hours', 'description' => 'Una hora adicional de DJ', 'price' => 3000.00, 'unit' => 'por hora', 'sort_order' => 4],

            // Equipment
            ['name' => 'Pantalla LED', 'category' => 'equipment', 'description' => 'Pantalla LED para proyección de fotos y videos', 'price' => 0.00, 'unit' => 'por evento', 'sort_order' => 5],

            // Effects
            ['name' => 'Máquina de CO2', 'category' => 'effects', 'description' => 'Efecto de humo criogénico con CO2', 'price' => 0.00, 'unit' => 'por evento', 'sort_order' => 6],
            ['name' => 'Pirotecnia fría adicional', 'category' => 'effects', 'description' => 'Efectos adicionales de pirotecnia fría', 'price' => 0.00, 'unit' => 'por evento', 'sort_order' => 7],

            // Entertainment
            ['name' => 'Grupo Estelar', 'category' => 'entertainment', 'description' => 'Contratación de agrupación estelar / artista famoso para evento privado', 'price' => 0.00, 'unit' => 'por evento', 'sort_order' => 8],

            // Other
            ['name' => 'Viáticos por traslado', 'category' => 'other', 'description' => 'Cargo adicional para eventos fuera de la zona metropolitana de Saltillo, Coahuila. Cubre transporte, logística y gastos operativos.', 'price' => 0.00, 'unit' => 'por evento', 'sort_order' => 9],
        ];

        foreach ($addons as $addon) {
            ServiceAddon::firstOrCreate(['name' => $addon['name']], $addon);
        }
    }
}
