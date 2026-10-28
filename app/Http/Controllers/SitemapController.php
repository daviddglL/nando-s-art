<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $obras = Product::ordenadas()->get(['slug', 'updated_at']);

        return response()
            ->view('sitemap', compact('obras'))
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    // robots.txt dinámico para que apunte al sitemap con el dominio real
    public function robots(): Response
    {
        $texto = "User-agent: *\nDisallow: /admin\n\nSitemap: ".route('sitemap')."\n";

        return response($texto)->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
