<?php

namespace Database\Seeders;

use App\Models\EventType;
use App\Models\Package;
use App\Models\PackageInclude;
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

        // ─── Packages ───────────────────────────────
        $packages = [
            [
                'slug' => 'altavox-1-delux',
                'name' => 'Paquete Altavox 1 Delux',
                'description' => 'Cinco horas de repertorio musical versátil. Más que una banda, una experiencia en vivo que transforma tu evento en un recuerdo inolvidable.',
                'price' => 43000.00,
                'duration_hours' => 5,
                'is_featured' => true,
                'sort_order' => 1,
                'event_type_slugs' => ['boda', 'xv-anos', 'bautizo', 'cumpleanos', 'graduacion'],
                'includes' => [
                    ['description' => 'Servicio de audio e iluminación profesional', 'is_highlighted' => true, 'sort_order' => 1],
                    ['description' => 'Bono de animación con 6 efectos de pirotecnia fría', 'is_highlighted' => true, 'sort_order' => 2],
                    ['description' => 'Collares hawaianos de tela y accesorios luminosos', 'is_highlighted' => false, 'sort_order' => 3],
                    ['description' => 'Aproximadamente 100 artículos de animación', 'is_highlighted' => true, 'sort_order' => 4],
                    ['description' => 'Dos botargas temáticas (Juan Gabriel y Luis Miguel)', 'is_highlighted' => false, 'sort_order' => 5],
                ],
            ],
            [
                'slug' => 'altavox-2-gold',
                'name' => 'Paquete Altavox 2 Gold',
                'description' => null,
                'price' => 40000.00,
                'duration_hours' => 4,
                'required_addon_subcategory' => 'Grupo musical',
                'is_featured' => true,
                'sort_order' => 2,
                'event_type_slugs' => ['boda', 'xv-anos', 'bautizo', 'cumpleanos', 'graduacion'],
                'includes' => [
                    ['description' => '4 horas Altavox versátil', 'is_highlighted' => true, 'sort_order' => 1],
                    ['description' => 'Grupo musical adicional', 'is_highlighted' => true, 'sort_order' => 2],
                ],
            ],
            [
                'slug' => 'altavox-eventos-pequenos',
                'name' => 'AltaVox Eventos Pequeños',
                'description' => null,
                'price' => 4000.00,
                'duration_hours' => 1,
                'is_featured' => false,
                'sort_order' => 3,
                'event_type_slugs' => ['bautizo', 'cumpleanos', 'corporativo', 'bar-restaurante', 'otro'],
                'includes' => [
                    ['description' => '3 voces en vivo', 'is_highlighted' => true, 'sort_order' => 1],
                    ['description' => 'Audio con pista', 'is_highlighted' => false, 'sort_order' => 2],
                ],
            ],
            [
                'slug' => 'dj-protec',
                'name' => 'DJ - Protec',
                'description' => null,
                'price' => 3000.00,
                'duration_hours' => 5,
                'is_featured' => false,
                'sort_order' => 4,
                'event_type_slugs' => ['boda', 'xv-anos', 'bautizo', 'cumpleanos', 'graduacion', 'corporativo', 'bar-restaurante'],
                'includes' => [
                    ['description' => 'Iluminación profesional', 'is_highlighted' => true, 'sort_order' => 1],
                ],
            ],
            [
                'slug' => 'gender-reveal',
                'name' => 'Gender Reveal',
                'description' => null,
                'price' => 5800.00,
                'duration_hours' => null,
                'is_featured' => false,
                'sort_order' => 5,
                'event_type_slugs' => ['otro'],
                'includes' => [
                    ['description' => 'Caja de cuetes', 'is_highlighted' => false, 'sort_order' => 1],
                    ['description' => 'Chisperos de pirotecnia fría', 'is_highlighted' => true, 'sort_order' => 2],
                    ['description' => 'Lluvia de papel', 'is_highlighted' => false, 'sort_order' => 3],
                    ['description' => 'Bombas de humo', 'is_highlighted' => false, 'sort_order' => 4],
                ],
            ],
            [
                'slug' => 'posada-grupo-estelar',
                'name' => 'Posada con Grupo ESTELAR',
                'description' => null,
                'price' => null,
                'duration_hours' => 1,
                'is_featured' => false,
                'sort_order' => 6,
                'event_type_slugs' => ['posada'],
                'includes' => [],
            ],
            [
                'slug' => 'produccion-soporte-tecnico',
                'name' => 'Producción / Soporte Técnico',
                'description' => null,
                'price' => null,
                'duration_hours' => null,
                'is_featured' => false,
                'sort_order' => 7,
                'event_type_slugs' => [],
                'includes' => [
                    ['description' => 'Operador de audio', 'is_highlighted' => false, 'sort_order' => 1],
                    ['description' => 'Montaje', 'is_highlighted' => false, 'sort_order' => 2],
                    ['description' => 'Técnico en sitio', 'is_highlighted' => false, 'sort_order' => 3],
                ],
            ],
        ];

        // Also migrate the legacy 'altavox' slug to the new slug if it exists
        Package::where('slug', 'altavox')->update(['slug' => 'altavox-1-delux', 'name' => 'Paquete Altavox 1 Delux']);

        foreach ($packages as $data) {
            $includes = $data['includes'];
            $eventTypeSlugs = $data['event_type_slugs'];
            unset($data['includes'], $data['event_type_slugs']);

            $package = Package::updateOrCreate(
                ['slug' => $data['slug']],
                array_merge(['is_active' => true], $data)
            );

            foreach ($includes as $include) {
                PackageInclude::firstOrCreate(
                    ['package_id' => $package->id, 'description' => $include['description']],
                    $include + ['package_id' => $package->id]
                );
            }

            if (!empty($eventTypeSlugs)) {
                $package->eventTypes()->sync(
                    EventType::whereIn('slug', $eventTypeSlugs)->pluck('id')
                );
            }
        }
    }
}
