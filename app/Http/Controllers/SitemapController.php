<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $urls = [
            ['loc' => route('home'),        'priority' => '1.0'],
            ['loc' => route('services'),    'priority' => '0.9'],
            ['loc' => route('doctors'),     'priority' => '0.9'],
            ['loc' => route('booking'),     'priority' => '0.9'],
            ['loc' => route('gallery'),     'priority' => '0.7'],
            ['loc' => route('testimonials'),'priority' => '0.6'],
            ['loc' => route('offers'),      'priority' => '0.8'],
            ['loc' => route('faq'),         'priority' => '0.5'],
            ['loc' => route('contact'),     'priority' => '0.6'],
            ['loc' => route('privacy'),     'priority' => '0.3'],
            ['loc' => route('terms'),       'priority' => '0.3'],
        ];

        try {
            foreach (Service::active()->get(['slug']) as $service) {
                $urls[] = [
                    'loc'      => route('services.show', $service->slug),
                    'priority' => '0.7',
                ];
            }
        } catch (\Throwable) {
            // Table not migrated yet — skip dynamic entries.
        }

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $u) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($u['loc'], ENT_XML1) . "</loc>\n";
            $xml .= "    <priority>{$u['priority']}</priority>\n";
            $xml .= "  </url>\n";
        }
        $xml .= '</urlset>' . "\n";

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    public function robots(): Response
    {
        $body  = "User-agent: *\n";
        $body .= "Disallow: /admin/\n";
        $body .= "Disallow: /api/\n";
        $body .= "Allow: /\n\n";
        $body .= "Sitemap: " . route('sitemap') . "\n";

        return response($body, 200, ['Content-Type' => 'text/plain']);
    }
}
