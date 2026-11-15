<?php

namespace App\Http\Controllers;

use App\Galeria\Catalogo;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(Catalogo $catalogo): Response
    {
        return response()
            ->view('sitemap', ['obras' => $catalogo->todas()])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }

    // robots.txt dinámico para que apunte al sitemap con el dominio real
    public function robots(): Response
    {
        $texto = "User-agent: *\nAllow: /\n\nSitemap: ".route('sitemap')."\n";

        return response($texto)->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
