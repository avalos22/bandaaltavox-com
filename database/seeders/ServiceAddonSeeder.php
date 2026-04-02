<?php

namespace Database\Seeders;

use App\Models\ServiceAddon;
use Illuminate\Database\Seeder;

class ServiceAddonSeeder extends Seeder
{
    public function run(): void
    {
        $addons = [
            // ── Audio ──────────────────────────────────────────────────────
            [
                'name' => 'Audio BOSSE para 50 - 100 personas',
                'category' => 'audio', 'subcategory' => 'Sistemas de audio',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => 4000.00, 'supplier_price' => null, 'sort_order' => 1,
            ],
            [
                'name' => 'Audio BOSSE para 100 - 250 personas',
                'category' => 'audio', 'subcategory' => 'Sistemas de audio',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => 6000.00, 'supplier_price' => null, 'sort_order' => 2,
            ],
            [
                'name' => 'Audio BOSSE para 250 - 350 personas',
                'category' => 'audio', 'subcategory' => 'Sistemas de audio',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => 8000.00, 'supplier_price' => null, 'sort_order' => 3,
            ],

            // ── Pantallas y Video ───────────────────────────────────────────
            [
                'name' => 'Pantalla 3X2',
                'category' => 'pantallas_video', 'subcategory' => 'Pantallas',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => 5500.00, 'supplier_price' => null, 'sort_order' => 10,
            ],
            [
                'name' => 'Pantalla 4X3',
                'category' => 'pantallas_video', 'subcategory' => 'Pantallas',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => 11000.00, 'supplier_price' => null, 'sort_order' => 11,
            ],
            [
                'name' => 'Pantalla 5X2',
                'category' => 'pantallas_video', 'subcategory' => 'Pantallas',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => 11000.00, 'supplier_price' => null, 'sort_order' => 12,
            ],
            [
                'name' => 'Pantalla 6X2',
                'category' => 'pantallas_video', 'subcategory' => 'Pantallas',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => 11000.00, 'supplier_price' => null, 'sort_order' => 13,
            ],
            [
                'name' => 'Pantalla de Proyección 6x8',
                'category' => 'pantallas_video', 'subcategory' => 'Pantallas',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => 2500.00, 'supplier_price' => 500.00, 'sort_order' => 14,
            ],

            // ── Iluminación ─────────────────────────────────────────────────
            [
                'name' => 'Iluminación Ambiental',
                'category' => 'iluminacion', 'subcategory' => 'Iluminación ambiental',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => null, 'supplier_price' => null, 'sort_order' => 20,
            ],

            // ── Efectos Especiales ──────────────────────────────────────────
            [
                'name' => 'Lluvia de papeles metálicos CO₂ por Disparo',
                'category' => 'efectos_especiales', 'subcategory' => 'Confeti / CO₂',
                'unit' => 'Por disparo', 'duration' => null,
                'price' => 1500.00, 'supplier_price' => null, 'sort_order' => 30,
            ],
            [
                'name' => 'Lluvia de papeles mariposa CO₂ por Disparo',
                'category' => 'efectos_especiales', 'subcategory' => 'Confeti / CO₂',
                'unit' => 'Por disparo', 'duration' => null,
                'price' => 1800.00, 'supplier_price' => null, 'sort_order' => 31,
            ],
            [
                'name' => 'Niebla baja para vals',
                'category' => 'efectos_especiales', 'subcategory' => 'Niebla',
                'unit' => 'por servicio', 'duration' => null,
                'price' => 1800.00, 'supplier_price' => null,
                'description' => 'Efecto de niebla baja para primer baile',
                'sort_order' => 32,
            ],
            [
                'name' => 'Pirotecnia fría (10 disparos)',
                'category' => 'efectos_especiales', 'subcategory' => 'Pirotecnia fría',
                'unit' => 'Por disparo', 'duration' => null,
                'price' => 1500.00, 'supplier_price' => null, 'sort_order' => 33,
            ],

            // ── Mobiliario — Decoración ─────────────────────────────────────
            [
                'name' => 'Letras GIGANTES - 160',
                'category' => 'mobiliario', 'subcategory' => 'Decoración',
                'unit' => 'por pieza', 'duration' => null,
                'price' => 500.00, 'supplier_price' => null, 'sort_order' => 40,
            ],

            // Pistas de baile
            [
                'name' => 'Pista Gold 5x5',
                'category' => 'mobiliario', 'subcategory' => 'Pistas de baile',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => 6500.00, 'supplier_price' => 4500.00, 'sort_order' => 50,
            ],
            [
                'name' => 'Pista Gold 8x5',
                'category' => 'mobiliario', 'subcategory' => 'Pistas de baile',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => 9500.00, 'supplier_price' => 7500.00, 'sort_order' => 51,
            ],
            [
                'name' => 'Pista Gold 8x8',
                'category' => 'mobiliario', 'subcategory' => 'Pistas de baile',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => 12900.00, 'supplier_price' => 10900.00, 'sort_order' => 52,
            ],
            [
                'name' => 'Pista Vintage (madera) 5x5',
                'category' => 'mobiliario', 'subcategory' => 'Pistas de baile',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => 5900.00, 'supplier_price' => 3900.00, 'sort_order' => 53,
            ],
            [
                'name' => 'Pista Vintage (madera) 8x5',
                'category' => 'mobiliario', 'subcategory' => 'Pistas de baile',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => 7900.00, 'supplier_price' => 5900.00, 'sort_order' => 54,
            ],
            [
                'name' => 'Pista Vintage (madera) 8x8',
                'category' => 'mobiliario', 'subcategory' => 'Pistas de baile',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => 10900.00, 'supplier_price' => 8900.00, 'sort_order' => 55,
            ],
            [
                'name' => 'Pista Iluminada 5x5',
                'category' => 'mobiliario', 'subcategory' => 'Pistas de baile',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => 5900.00, 'supplier_price' => 3900.00, 'sort_order' => 56,
            ],
            [
                'name' => 'Pista Iluminada 8x5',
                'category' => 'mobiliario', 'subcategory' => 'Pistas de baile',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => 7900.00, 'supplier_price' => 5900.00, 'sort_order' => 57,
            ],
            [
                'name' => 'Pista Iluminada 8x8',
                'category' => 'mobiliario', 'subcategory' => 'Pistas de baile',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => 10900.00, 'supplier_price' => 8900.00, 'sort_order' => 58,
            ],
            [
                'name' => 'Pista Charol Negro 5x5',
                'category' => 'mobiliario', 'subcategory' => 'Pistas de baile',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => 7900.00, 'supplier_price' => 5900.00, 'sort_order' => 59,
            ],
            [
                'name' => 'Pista Charol Negro 8x5',
                'category' => 'mobiliario', 'subcategory' => 'Pistas de baile',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => 10900.00, 'supplier_price' => 8900.00, 'sort_order' => 60,
            ],
            [
                'name' => 'Pista Charol Negro 8x8',
                'category' => 'mobiliario', 'subcategory' => 'Pistas de baile',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => 13900.00, 'supplier_price' => 11900.00, 'sort_order' => 61,
            ],
            [
                'name' => 'Pista Charol Blanco 5x5',
                'category' => 'mobiliario', 'subcategory' => 'Pistas de baile',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => 7900.00, 'supplier_price' => 5900.00, 'sort_order' => 62,
            ],
            [
                'name' => 'Pista Charol Blanco 8x5',
                'category' => 'mobiliario', 'subcategory' => 'Pistas de baile',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => 10900.00, 'supplier_price' => 8900.00, 'sort_order' => 63,
            ],
            [
                'name' => 'Pista Charol Blanco 8x8',
                'category' => 'mobiliario', 'subcategory' => 'Pistas de baile',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => 13900.00, 'supplier_price' => 11900.00, 'sort_order' => 64,
            ],
            [
                'name' => 'Pista Personalizada 5x5',
                'category' => 'mobiliario', 'subcategory' => 'Pistas de baile',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => 7900.00, 'supplier_price' => 5900.00, 'sort_order' => 65,
            ],
            [
                'name' => 'Pista Personalizada 8x5',
                'category' => 'mobiliario', 'subcategory' => 'Pistas de baile',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => 10900.00, 'supplier_price' => 8900.00, 'sort_order' => 66,
            ],
            [
                'name' => 'Pista Personalizada 8x8',
                'category' => 'mobiliario', 'subcategory' => 'Pistas de baile',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => 13900.00, 'supplier_price' => 11900.00, 'sort_order' => 67,
            ],

            // Sillas y Mesas
            [
                'name' => 'Silla Tiffani',
                'category' => 'mobiliario', 'subcategory' => 'Sillas',
                'unit' => 'por pieza', 'duration' => null,
                'price' => 23.00, 'supplier_price' => 20.00, 'sort_order' => 70,
            ],
            [
                'name' => 'Silla Banquetera',
                'category' => 'mobiliario', 'subcategory' => 'Sillas',
                'unit' => 'por pieza', 'duration' => null,
                'price' => 15.00, 'supplier_price' => 13.00, 'sort_order' => 71,
            ],
            [
                'name' => 'Mesas',
                'category' => 'mobiliario', 'subcategory' => 'Mesas',
                'unit' => 'por pieza', 'duration' => null,
                'price' => null, 'supplier_price' => null, 'sort_order' => 72,
            ],

            // ── Entretenimiento — Botargas ──────────────────────────────────
            [
                'name' => 'Doctor SIMI',
                'category' => 'entretenimiento', 'subcategory' => 'Botargas',
                'unit' => 'Por show', 'duration' => '40 minutos',
                'price' => 1600.00, 'supplier_price' => null, 'sort_order' => 80,
            ],
            [
                'name' => 'Monja',
                'category' => 'entretenimiento', 'subcategory' => 'Botargas',
                'unit' => 'Por show', 'duration' => '40 minutos',
                'price' => 1600.00, 'supplier_price' => null, 'sort_order' => 81,
            ],
            [
                'name' => 'Robot Led',
                'category' => 'entretenimiento', 'subcategory' => 'Botargas',
                'unit' => 'Por show', 'duration' => '40 minutos',
                'price' => 1600.00, 'supplier_price' => null, 'sort_order' => 82,
            ],
            [
                'name' => 'Luis Miguel',
                'category' => 'entretenimiento', 'subcategory' => 'Botargas',
                'unit' => 'Por show', 'duration' => '40 minutos',
                'price' => 1600.00, 'supplier_price' => null, 'sort_order' => 83,
            ],
            [
                'name' => 'Juan Gabriel',
                'category' => 'entretenimiento', 'subcategory' => 'Botargas',
                'unit' => 'Por show', 'duration' => '40 minutos',
                'price' => 1600.00, 'supplier_price' => null, 'sort_order' => 84,
            ],
            [
                'name' => 'Máscara',
                'category' => 'entretenimiento', 'subcategory' => 'Botargas',
                'unit' => 'Por show', 'duration' => '40 minutos',
                'price' => 1600.00, 'supplier_price' => null, 'sort_order' => 85,
            ],

            // Grupos musicales
            [
                'name' => 'Norteño',
                'category' => 'entretenimiento', 'subcategory' => 'Grupo musical',
                'unit' => 'por hora', 'duration' => null,
                'price' => 5500.00, 'supplier_price' => null, 'sort_order' => 90,
            ],
            [
                'name' => 'Norteño Banda',
                'category' => 'entretenimiento', 'subcategory' => 'Grupo musical',
                'unit' => 'por hora', 'duration' => null,
                'price' => 5500.00, 'supplier_price' => null, 'sort_order' => 91,
            ],
            [
                'name' => 'Banda Sinaloense',
                'category' => 'entretenimiento', 'subcategory' => 'Grupo musical',
                'unit' => 'por hora', 'duration' => null,
                'price' => 8000.00, 'supplier_price' => null, 'sort_order' => 92,
            ],
            [
                'name' => 'DJ',
                'category' => 'entretenimiento', 'subcategory' => 'Grupo musical',
                'unit' => 'por hora', 'duration' => null,
                'price' => 3000.00, 'supplier_price' => null, 'sort_order' => 93,
            ],
            [
                'name' => 'Country',
                'category' => 'entretenimiento', 'subcategory' => 'Grupo musical',
                'unit' => 'por hora', 'duration' => null,
                'price' => 8000.00, 'supplier_price' => null, 'sort_order' => 94,
            ],

            // ── Producción / Estructuras — Escenarios ──────────────────────
            [
                'name' => 'Escenario Delux - 4.88 x 3.66 (hojas 6)',
                'category' => 'produccion_estructuras', 'subcategory' => 'Escenarios',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => 4500.00, 'supplier_price' => null, 'sort_order' => 100,
            ],
            [
                'name' => 'Escenario Gama - 7.32 x 3.66 (hojas 9)',
                'category' => 'produccion_estructuras', 'subcategory' => 'Escenarios',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => 6750.00, 'supplier_price' => null, 'sort_order' => 101,
            ],
            [
                'name' => 'Escenario Ultra - 7.32 x 4.88 (hojas 11)',
                'category' => 'produccion_estructuras', 'subcategory' => 'Escenarios',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => 8250.00, 'supplier_price' => null, 'sort_order' => 102,
            ],
            [
                'name' => 'Hoja de escenario 2.44 x 1.22 (por pieza)',
                'category' => 'produccion_estructuras', 'subcategory' => 'Escenarios',
                'unit' => 'por pieza', 'duration' => null,
                'price' => 750.00, 'supplier_price' => null, 'sort_order' => 103,
            ],

            // Toldos
            [
                'name' => 'Toldo 10x10',
                'category' => 'produccion_estructuras', 'subcategory' => 'Toldos',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => 10000.00, 'supplier_price' => null,
                'description' => 'Recomendado para Escenario Ultra y Gama',
                'sort_order' => 110,
            ],
            [
                'name' => 'Toldo 6x6',
                'category' => 'produccion_estructuras', 'subcategory' => 'Toldos',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => 2000.00, 'supplier_price' => null,
                'description' => 'Recomendado para Escenario Ultra y Gama',
                'sort_order' => 111,
            ],
            [
                'name' => 'Toldo 3x3',
                'category' => 'produccion_estructuras', 'subcategory' => 'Toldos',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => 1200.00, 'supplier_price' => null, 'sort_order' => 112,
            ],
            [
                'name' => 'Toldo 5x5',
                'category' => 'produccion_estructuras', 'subcategory' => 'Toldos',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => 1500.00, 'supplier_price' => null, 'sort_order' => 113,
            ],
            [
                'name' => 'Toldo 6x3',
                'category' => 'produccion_estructuras', 'subcategory' => 'Toldos',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => 1500.00, 'supplier_price' => null, 'sort_order' => 114,
            ],
            [
                'name' => 'Toldo Árabe 6x6',
                'category' => 'produccion_estructuras', 'subcategory' => 'Toldos',
                'unit' => 'por evento', 'duration' => '5 Horas',
                'price' => 1800.00, 'supplier_price' => null,
                'description' => 'Recomendado para Escenario Delux',
                'sort_order' => 115,
            ],

            // ── Producción / Logística ──────────────────────────────────────
            [
                'name' => 'Traslado de Mobiliario',
                'category' => 'produccion_logistica', 'subcategory' => 'Transporte',
                'unit' => 'por servicio', 'duration' => null,
                'price' => null, 'supplier_price' => null, 'sort_order' => 120,
            ],
            [
                'name' => 'Acomodo de Mobiliario',
                'category' => 'produccion_logistica', 'subcategory' => 'Montaje',
                'unit' => 'por servicio', 'duration' => null,
                'price' => null, 'supplier_price' => null, 'sort_order' => 121,
            ],

            // ── Video ───────────────────────────────────────────────────────
            [
                'name' => 'Cabina 360',
                'category' => 'video', 'subcategory' => null,
                'unit' => 'por hora', 'duration' => null,
                'price' => 1500.00, 'supplier_price' => null, 'sort_order' => 130,
            ],
        ];

        foreach ($addons as $addon) {
            ServiceAddon::firstOrCreate(
                ['name' => $addon['name']],
                array_merge(['description' => null, 'is_active' => true], $addon)
            );
        }
    }
}
