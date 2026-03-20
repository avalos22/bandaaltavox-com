<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SettingController extends Controller
{
    private array $groupLabels = [
        'general' => 'General',
        'social' => 'Redes Sociales',
        'branding' => 'Marca',
        'quotation' => 'Cotizaciones',
        'email' => 'Correo Electrónico',
    ];

    private array $fieldLabels = [
        'business_name' => 'Nombre del negocio',
        'business_slogan' => 'Eslogan',
        'business_phone' => 'Teléfono',
        'business_email' => 'Correo electrónico',
        'business_address' => 'Dirección',
        'business_logo' => 'Logo del negocio',
        'facebook_url' => 'Facebook',
        'instagram_url' => 'Instagram',
        'youtube_url' => 'YouTube',
        'tiktok_url' => 'TikTok',
        'whatsapp_number' => 'WhatsApp',
        'primary_color' => 'Color primario',
        'secondary_color' => 'Color secundario',
        'quotation_validity_days' => 'Días de vigencia de cotización',
        'deposit_percentage' => 'Porcentaje de anticipo (%)',
        'resend_api_key' => 'API Key de Resend',
        'mail_from_address' => 'Correo de envío (From)',
        'mail_from_name' => 'Nombre de envío',
        'mail_reply_to' => 'Responder a (Reply-To)',
    ];

    public function index()
    {
        $settings = Setting::all()->groupBy('group')->map(function ($group) {
            return $group->map(function ($setting) {
                // Never send the raw secret value to the frontend.
                // Only inform whether a value is already stored.
                $isSecret = $setting->type === 'secret';
                return [
                    'id'       => $setting->id,
                    'key'      => $setting->key,
                    'value'    => $isSecret ? '' : $setting->value,
                    'hasValue' => $isSecret ? ! empty($setting->value) : null,
                    'type'     => $setting->type,
                    'label'    => $this->fieldLabels[$setting->key] ?? $setting->key,
                ];
            });
        });

        return Inertia::render('Admin/Settings/Index', [
            'settingGroups' => $settings,
            'groupLabels' => $this->groupLabels,
        ]);
    }

    public function update(Request $request)
    {
        $settings = $request->input('settings', []);

        foreach ($settings as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            if (! $setting) {
                continue;
            }

            if ($setting->type === 'image') {
                // Images are handled separately via uploadImage
                continue;
            }

            // Secret fields: only update if a new non-empty value is provided, then encrypt
            if ($setting->type === 'secret') {
                if (empty($value)) {
                    continue;
                }
                $setting->update(['value' => Crypt::encryptString($value)]);
                continue;
            }

            $setting->update(['value' => $value ?? '']);
        }

        return back()->with('success', 'Ajustes guardados correctamente.');
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'key' => ['required', 'string', 'exists:settings,key'],
            'image' => ['required', 'image', 'max:2048'],
        ]);

        $setting = Setting::where('key', $request->key)->first();

        if ($setting->type !== 'image') {
            return back()->with('error', 'Este campo no acepta imágenes.');
        }

        // Delete old image if exists
        if ($setting->value && Storage::disk('public')->exists($setting->value)) {
            Storage::disk('public')->delete($setting->value);
        }

        $path = $request->file('image')->store('settings', 'public');
        $setting->update(['value' => $path]);

        return back()->with('success', 'Imagen actualizada correctamente.');
    }

    public function deleteImage(Request $request)
    {
        $request->validate([
            'key' => ['required', 'string', 'exists:settings,key'],
        ]);

        $setting = Setting::where('key', $request->key)->first();

        if ($setting->value && Storage::disk('public')->exists($setting->value)) {
            Storage::disk('public')->delete($setting->value);
        }

        $setting->update(['value' => '']);

        return back()->with('success', 'Imagen eliminada.');
    }
}
