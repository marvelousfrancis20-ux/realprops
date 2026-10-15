<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            'about' => [
                'title' => 'About Real Props',
                'content' => '<p>Real Props is a modern real-estate marketplace connecting buyers, renters and verified agents across Nigeria.</p>
<p>Our mission is to make property search transparent, fast and delightful — with accurate listings, powerful filters and a genuinely useful agent directory.</p>
<p>Every agent on the platform is reviewed by our team before their listings go live, so you can enquire with confidence.</p>',
                'meta_description' => 'Learn about Real Props, the modern real-estate marketplace for buyers, renters and agents.',
            ],
            'faq' => [
                'title' => 'Frequently Asked Questions',
                'content' => '<h3>How do I contact an agent?</h3>
<p>Open any listing and use the enquiry form. Your message goes straight to the agent\'s inbox and you will receive replies by e-mail.</p>
<h3>Can I schedule a viewing?</h3>
<p>Yes — every listing has a “Book a tour” form. Pick a date and time and the agent will confirm.</p>
<h3>How do agents get approved?</h3>
<p>Agents create a profile with their agency details and documents. Our team verifies them, after which their listings can go live.</p>
<h3>Is Real Props free to use?</h3>
<p>Searching, enquiring and saving favourites is free for everyone. Agents choose from several affordable plans to publish listings.</p>',
                'meta_description' => 'Answers to the most common questions about searching, enquiring and listing on Real Props.',
            ],
            'terms' => [
                'title' => 'Terms of Service',
                'content' => '<p>By using Real Props you agree to these terms. Listings are provided by independent agents and we do not guarantee any transaction between users and agents.</p>
<p>You agree not to misuse the platform, post fraudulent listings or contact users for unsolicited marketing.</p>
<p>Agents are responsible for the accuracy of their listings and for responding to enquiries in a timely manner.</p>',
                'meta_description' => 'The terms and conditions that govern your use of Real Props.',
            ],
            'privacy' => [
                'title' => 'Privacy Policy',
                'content' => '<p>We collect only the information needed to operate the marketplace: account details, listings you publish and enquiries you send.</p>
<p>We never sell your data. Message content is shared with the listing agent so they can respond to you.</p>
<p>You can request a copy or deletion of your personal data at any time by contacting support@realprops.test.</p>',
                'meta_description' => 'How Real Props collects, uses and protects your personal information.',
            ],
        ];

        foreach ($pages as $slug => $data) {
            Page::firstOrCreate(['slug' => $slug], $data + ['is_active' => true]);
        }
    }
}
