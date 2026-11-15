<?php

use App\Galeria\Catalogo;

// Las páginas públicas salen con las obras de resources/contenido/obras.php

test('la portada enseña las obras destacadas y el comienzo de «Sobre mí»', function () {
    $destacadas = app(Catalogo::class)->destacadas();

    expect($destacadas)->not->toBeEmpty();

    $respuesta = $this->get('/')->assertOk();
    foreach ($destacadas as $obra) {
        $respuesta->assertSee($obra->name)->assertSee(route('obras.show', $obra->slug), false);
    }
    $respuesta->assertSee('Soy Fernando Delgado');
});

test('la galería, Sobre mí y Contacto se abren', function (string $url) {
    $this->get($url)->assertOk();
})->with(['/galeria', '/galeria?estilo=acuarela&categoria=paisaje&q=casa', '/galeria?estilo=no-existe', '/sobre-mi', '/contacto']);

test('cada obra tiene su ficha con título, técnica, medidas y precio', function () {
    foreach (app(Catalogo::class)->todas() as $obra) {
        $this->get("/obras/{$obra->slug}")
            ->assertOk()
            ->assertSee($obra->name)
            ->assertSee($obra->style->label())
            ->assertSee("{$obra->height} x {$obra->width} cm")
            ->assertSee($obra->precioTexto())
            ->assertSee(route('paginas.contacto', ['obra' => $obra->slug]), false);
    }
});

test('una obra que no existe da 404', function () {
    $this->get('/obras/no-existe')->assertNotFound()->assertSee('Esta página no existe');
});

test('la API de la galería da todas las obras en el orden del fichero', function () {
    $slugs = app(Catalogo::class)->todas()->pluck('slug')->all();

    $this->getJson('/api/products')
        ->assertOk()
        ->assertJsonCount(count($slugs), 'data')
        ->assertJsonPath('data.0.slug', $slugs[0])
        ->assertJsonStructure(['data' => [['id', 'slug', 'name', 'description', 'precio_texto', 'available', 'height', 'width',
            'style', 'style_label', 'category', 'category_label', 'imagen_url', 'srcset', 'imagenes', 'url']]]);
});

test('la API filtra por técnica, tema y texto (sin tildes ni mayúsculas)', function () {
    $this->getJson('/api/products?category=paisaje')
        ->assertOk()
        ->assertJsonCount(2, 'data');

    $this->getJson('/api/products?q=MANSION')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.slug', 'mansion-en-el-bosque');

    $this->getJson('/api/products?style=oleo')->assertOk()->assertJsonCount(0, 'data');
    $this->getJson('/api/products?style=no-existe')->assertUnprocessable();
});

test('la API da una obra por su slug', function () {
    $this->getJson('/api/products/casa-embrujada')->assertOk()->assertJsonPath('data.name', 'Casa Embrujada');
    $this->getJson('/api/products/no-existe')->assertNotFound();
});

test('el sitemap tiene las páginas y todas las obras', function () {
    $respuesta = $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

    foreach (app(Catalogo::class)->todas() as $obra) {
        $respuesta->assertSee(route('obras.show', $obra->slug), false);
    }
    $respuesta->assertSee(route('paginas.contacto'), false);
});

test('robots.txt apunta al sitemap', function () {
    $this->get('/robots.txt')->assertOk()->assertSee('Sitemap: '.route('sitemap'), false);
});

test('ya no hay login, registro, panel ni presupuestos', function (string $url) {
    $this->get($url)->assertNotFound();
})->with(['/login', '/register', '/password/reset', '/admin', '/admin/obras', '/dashboard', '/profile', '/presupuesto', '/galeria/nuevo']);

test('el menú no enseña nada de cuentas', function () {
    $this->get('/')
        ->assertOk()
        ->assertDontSee('Iniciar sesión')
        ->assertDontSee('Mis presupuestos')
        ->assertSee(route('paginas.contacto'), false);
});

test('todas las respuestas llevan las cabeceras de seguridad', function () {
    $this->get('/')
        ->assertHeader('X-Frame-Options', 'DENY')
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('Content-Security-Policy');
});
