<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['group' => 'general', 'key' => 'business_name', 'value' => 'Banda Alta Vox', 'type' => 'text'],
            ['group' => 'general', 'key' => 'business_slogan', 'value' => 'La mejor banda versátil para tu evento', 'type' => 'text'],
            ['group' => 'general', 'key' => 'business_phone', 'value' => '', 'type' => 'text'],
            ['group' => 'general', 'key' => 'business_email', 'value' => '', 'type' => 'text'],
            ['group' => 'general', 'key' => 'business_address', 'value' => '', 'type' => 'text'],
            ['group' => 'general', 'key' => 'business_logo', 'value' => '', 'type' => 'image'],

            // Social media
            ['group' => 'social', 'key' => 'facebook_url', 'value' => '', 'type' => 'text'],
            ['group' => 'social', 'key' => 'instagram_url', 'value' => '', 'type' => 'text'],
            ['group' => 'social', 'key' => 'youtube_url', 'value' => '', 'type' => 'text'],
            ['group' => 'social', 'key' => 'tiktok_url', 'value' => '', 'type' => 'text'],
            ['group' => 'social', 'key' => 'whatsapp_number', 'value' => '', 'type' => 'text'],

            // Branding
            ['group' => 'branding', 'key' => 'primary_color', 'value' => '#1e40af', 'type' => 'text'],
            ['group' => 'branding', 'key' => 'secondary_color', 'value' => '#f59e0b', 'type' => 'text'],

            // Quotation
            ['group' => 'quotation', 'key' => 'quotation_validity_days', 'value' => '15', 'type' => 'text'],
            ['group' => 'quotation', 'key' => 'deposit_percentage', 'value' => '30', 'type' => 'text'],

            // Email
            ['group' => 'email', 'key' => 'resend_api_key', 'value' => '', 'type' => 'secret'],
            ['group' => 'email', 'key' => 'mail_from_address', 'value' => '', 'type' => 'text'],
            ['group' => 'email', 'key' => 'mail_from_name', 'value' => 'Banda Alta Vox', 'type' => 'text'],
            ['group' => 'email', 'key' => 'mail_reply_to', 'value' => '', 'type' => 'text'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
