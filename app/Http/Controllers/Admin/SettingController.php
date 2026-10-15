<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Settings are edited through a grouped key/value form.
     *
     * @var array<string, array<string, array{label: string, type: string}>
     */
    private const GROUPS = [
        'general' => [
            'site_name' => ['label' => 'Site name', 'type' => 'text'],
            'site_tagline' => ['label' => 'Tagline', 'type' => 'text'],
            'site_email' => ['label' => 'Contact e-mail', 'type' => 'text'],
            'site_phone' => ['label' => 'Contact phone', 'type' => 'text'],
            'site_address' => ['label' => 'Address', 'type' => 'textarea'],
            'currency' => ['label' => 'Currency symbol', 'type' => 'text'],
            'default_country' => ['label' => 'Default country', 'type' => 'text'],
        ],
        'seo' => [
            'meta_title' => ['label' => 'Default meta title', 'type' => 'text'],
            'meta_description' => ['label' => 'Default meta description', 'type' => 'textarea'],
            'google_analytics_id' => ['label' => 'Google Analytics ID', 'type' => 'text'],
        ],
        'listings' => [
            'allow_agent_registration' => ['label' => 'Allow agent sign-ups', 'type' => 'boolean'],
            'require_approval' => ['label' => 'Admin must approve listings', 'type' => 'boolean'],
            'featured_listing_price' => ['label' => 'Featured listing price', 'type' => 'text'],
            'results_per_page' => ['label' => 'Results per page', 'type' => 'text'],
        ],
        'social' => [
            'facebook_url' => ['label' => 'Facebook URL', 'type' => 'text'],
            'twitter_url' => ['label' => 'X / Twitter URL', 'type' => 'text'],
            'instagram_url' => ['label' => 'Instagram URL', 'type' => 'text'],
            'linkedin_url' => ['label' => 'LinkedIn URL', 'type' => 'text'],
            'youtube_url' => ['label' => 'YouTube URL', 'type' => 'text'],
            'whatsapp' => ['label' => 'WhatsApp number', 'type' => 'text'],
        ],
    ];

    public function edit(): View
    {
        return view('admin.settings', [
            'groups' => self::GROUPS,
            'values' => Setting::query()->pluck('value', 'key'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $rules = [];
        $attributes = [];

        foreach (self::GROUPS as $group) {
            foreach ($group as $key => $field) {
                $rules[$key] = ['nullable', 'string', 'max:1000'];
                $attributes[$key] = $field['label'];
            }
        }

        $validated = $request->validate($rules, [], $attributes);

        foreach (self::GROUPS as $group => $fields) {
            foreach ($fields as $key => $field) {
                if (! array_key_exists($key, $validated)) {
                    continue;
                }

                Setting::set($key, $validated[$key], $group, $field['type']);
            }
        }

        return redirect()
            ->route('admin.settings')
            ->with('status', 'Settings saved.');
    }
}
