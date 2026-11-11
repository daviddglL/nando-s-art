<?php

use App\Models\Pagina;
use App\Models\Product;

// --- Portada y footer leen lo que el artista edita en el panel ---

test('la portada usa el primer párrafo de «Sobre mí» y ya no tiene los bloques fijos', function () {
    Pagina::obtener(Pagina::SOBRE_MI)->update(['contenido' => "Pinto desde niño.\n\nSegundo párrafo que no sale."]);

    $this->get('/')
        ->assertOk()
        ->assertSee('Pinto desde niño.')
        ->assertDontSee('Segundo párrafo que no sale.')
        ->assertDontSee('serenidad de los paisajes naturales');
});

test('el footer sale en todas las páginas con el Instagram y el email de Contacto', function () {
    Pagina::obtener(Pagina::CONTACTO)->update(['instagram' => 'https://www.instagram.com/otro_usuario/', 'email' => 'hola@example.com']);

    foreach (['/', '/galeria', '/contacto', '/login'] as $url) {
        $this->get($url)
            ->assertSee('href="https://www.instagram.com/otro_usuario/"', false)
            ->assertSee('@otro_usuario')
            ->assertSee('mailto:hola@example.com', false)
            ->assertDontSee('nandomtgalters');
    }
});

// --- Galería ---

test('la galería empieza mostrando todas las obras', function () {
    $this->get('/galeria')
        ->assertOk()
        ->assertSee('data-style="">Todas', false)
        ->assertSee('"estiloInicial":""', false);

    $this->get('/galeria?estilo=oleo')->assertSee('"estiloInicial":"oleo"', false);
});

test('la API sin estilo devuelve las obras de todos los estilos', function () {
    Product::factory()->create(['style' => 'oleo']);
    Product::factory()->create(['style' => 'acuarela']);

    $this->getJson('/api/products')->assertOk()->assertJsonCount(2, 'data');
});

// --- Accesibilidad y SEO ---

test('el layout tiene «Saltar al contenido» y marca la página actual del menú', function () {
    $html = $this->get('/galeria')->getContent();

    expect($html)->toContain('href="#contenido"')
        ->and($html)->toContain('id="contenido"')
        ->and($html)->toMatch('#href="'.preg_quote(route('galeria'), '#').'" class="nav-enlace"\s+aria-current="page"#')
        ->and($html)->toContain('<meta name="theme-color"');
});

test('las fichas llevan datos estructurados VisualArtwork con su precio', function () {
    $obra = Product::factory()->create(['name' => 'Marina', 'price' => 250, 'available' => true]);

    $html = $this->get(route('obras.show', $obra->slug))->getContent();
    preg_match('#<script type="application/ld\+json">(.*?)</script>#s', $html, $jsonLd);
    $datos = json_decode($jsonLd[1], true);

    expect($datos['@type'])->toBe('VisualArtwork')
        ->and($datos['name'])->toBe('Marina')
        ->and($datos['offers']['price'])->toBe('250.00')
        ->and($datos['offers']['priceCurrency'])->toBe('EUR');
});

test('los datos estructurados no permiten cerrar el <script> con el texto de una obra', function () {
    $obra = Product::factory()->create(['description' => '</script><script>alert(1)</script>']);

    $this->get(route('obras.show', $obra->slug))
        ->assertOk()
        ->assertDontSee('</script><script>alert(1)', false);
});

test('login, registro y presupuesto empiezan por un h1', function () {
    $this->get('/login')->assertSee('<h1', false);
    $this->get('/register')->assertSee('<h1', false);
});
