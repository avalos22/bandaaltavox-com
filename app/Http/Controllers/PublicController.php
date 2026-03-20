<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Setting;
use Inertia\Inertia;

class PublicController extends Controller
{
    public function home()
    {
        $settings = [
            'business_name' => Setting::get('business_name', 'Banda Alta Vox'),
            'business_slogan' => Setting::get('business_slogan', 'La mejor banda versátil para tu evento'),
            'business_phone' => Setting::get('business_phone'),
            'business_email' => Setting::get('business_email'),
            'business_address' => Setting::get('business_address'),
            'whatsapp_number' => Setting::get('whatsapp_number'),
            'facebook_url' => Setting::get('facebook_url'),
            'instagram_url' => Setting::get('instagram_url'),
            'youtube_url' => Setting::get('youtube_url'),
            'tiktok_url' => Setting::get('tiktok_url'),
        ];

        $packages = Package::where('is_active', true)
            ->where('is_featured', true)
            ->with('includes')
            ->orderBy('sort_order')
            ->get();

        return Inertia::render('Public/Home', [
            'settings' => $settings,
            'packages' => $packages,
        ]);
    }
}
