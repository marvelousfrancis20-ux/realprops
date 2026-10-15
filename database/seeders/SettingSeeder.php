<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['key' => 'site_name', 'value' => 'Real Props', 'group' => 'general', 'type' => 'string'],
            ['key' => 'site_tagline', 'value' => 'Find a place you will love', 'group' => 'general', 'type' => 'string'],
            ['key' => 'site_email', 'value' => 'hello@realprops.test', 'group' => 'general', 'type' => 'string'],
            ['key' => 'site_phone', 'value' => '+234 913 073 2722', 'group' => 'general', 'type' => 'string'],
            ['key' => 'site_address', 'value' => '12 Admiralty Way, Lekki Phase 1, Lagos', 'group' => 'general', 'type' => 'string'],
            ['key' => 'currency', 'value' => '₦', 'group' => 'general', 'type' => 'string'],
            ['key' => 'default_country', 'value' => 'Nigeria', 'group' => 'general', 'type' => 'string'],
            ['key' => 'meta_title', 'value' => 'Real Props — Property marketplace', 'group' => 'seo', 'type' => 'string'],
            ['key' => 'meta_description', 'value' => 'Search thousands of verified houses, apartments and land for sale and rent.', 'group' => 'seo', 'type' => 'string'],
            ['key' => 'google_analytics_id', 'value' => null, 'group' => 'seo', 'type' => 'string'],
            ['key' => 'allow_agent_registration', 'value' => '1', 'group' => 'listings', 'type' => 'boolean'],
            ['key' => 'require_approval', 'value' => '1', 'group' => 'listings', 'type' => 'boolean'],
            ['key' => 'featured_listing_price', 'value' => '2500', 'group' => 'listings', 'type' => 'string'],
            ['key' => 'results_per_page', 'value' => '12', 'group' => 'listings', 'type' => 'string'],
            ['key' => 'facebook_url', 'value' => 'https://facebook.com/realprops', 'group' => 'social', 'type' => 'string'],
            ['key' => 'twitter_url', 'value' => 'https://x.com/realprops', 'group' => 'social', 'type' => 'string'],
            ['key' => 'instagram_url', 'value' => 'https://instagram.com/realprops', 'group' => 'social', 'type' => 'string'],
            ['key' => 'linkedin_url', 'value' => null, 'group' => 'social', 'type' => 'string'],
            ['key' => 'youtube_url', 'value' => null, 'group' => 'social', 'type' => 'string'],
            ['key' => 'whatsapp', 'value' => '+234 800 000 0000', 'group' => 'social', 'type' => 'string'],
        ];

        foreach ($settings as $setting) {
            Setting::set($setting['key'], $setting['value'], $setting['group'], $setting['type']);
        }
    }
}
