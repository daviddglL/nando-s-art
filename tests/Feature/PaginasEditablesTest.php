<?php

use App\Models\Pagina;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

// --- Páginas públicas ---

test('las páginas sobre mí y contacto se ven con su texto por defecto y salen en el menú', function () {
    $this->get('/sobre-mi')
        ->assertOk()
        ->assertSee('<title>Sobre mí · Nando&#039;s Art</title>', false)
        ->assertSee('Soy Fernando Delgado')
        ->assertSee(asset('images/nandofoto-352.webp'));

    $this->get('/contacto')
        ->assertOk()
        ->assertSee('<title>Contacto · Nando&#039;s Art</title>', false)
        ->assertSee('href="https://www.instagram.com/nandomtgalters/"', false)
        ->assertSee(route('encargos.create'));

    $this->get('/')
        ->assertSee('href="'.route('paginas.sobre-mi').'"', false)
        ->assertSee('href="'.route('paginas.contacto').'"', false);

    expect(Pagina::count())->toBe(2);
});

test('el texto de las páginas no interpreta HTML y respeta los saltos de línea', function () {
    Pagina::obtener(Pagina::SOBRE_MI)->update(['contenido' => "Primer párrafo\n\n<script>alert(1)</script>"]);

    $this->get('/sobre-mi')
        ->assertOk()
        ->assertDontSee('<script>alert(1)</script>', false)
        ->assertSee('&lt;script&gt;', false)
        ->assertSee('whitespace-pre-line', false);
});

test('contacto solo enseña los datos que están rellenos', function () {
    Pagina::obtener(Pagina::CONTACTO)->update([
        'email' => 'nando@example.com', 'telefono' => '+34 600 11 22 33', 'instagram' => null, 'ubicacion' => null,
    ]);

    $this->get('/contacto')
        ->assertSee('href="mailto:nando@example.com"', false)
        ->assertSee('href="tel:+34600112233"', false)
        ->assertDontSee('Instagram</dt>', false)
        ->assertDontSee('Dónde estoy');
});

test('el sitemap incluye las páginas', function () {
    $this->get('/sitemap.xml')
        ->assertSee(route('paginas.sobre-mi'))
        ->assertSee(route('paginas.contacto'));
});

// --- Panel del admin ---

test('solo el admin puede ver y editar las páginas', function () {
    $cliente = User::factory()->create();

    $this->get('/admin/paginas')->assertRedirect('/login');
    $this->actingAs($cliente)->get('/admin/paginas')->assertForbidden();
    $this->actingAs($cliente)->get('/admin/paginas/contacto/editar')->assertForbidden();
    $this->actingAs($cliente)->put('/admin/paginas/contacto', ['titulo' => 'Hackeado', 'contenido' => 'x'])->assertForbidden();

    expect(Pagina::obtener(Pagina::CONTACTO)->titulo)->toBe('Contacto');

    $this->actingAs(admin())->get('/admin/paginas')
        ->assertOk()
        ->assertSee('Sobre mí')
        ->assertSee(route('admin.paginas.edit', 'contacto'));
});

test('una página que no existe da 404 en el panel', function () {
    $this->actingAs(admin())->get('/admin/paginas/inventada/editar')->assertNotFound();
});

test('el admin puede editar contacto y se ve en la página pública', function () {
    $this->actingAs(admin())->get('/admin/paginas/contacto/editar')
        ->assertOk()
        ->assertSee('name="email"', false)
        ->assertDontSee('name="foto"', false);

    $this->actingAs(admin())
        ->put('/admin/paginas/contacto', [
            'titulo' => 'Hablemos',
            'contenido' => "Escríbeme cuando quieras.\nRespondo en 24 horas.",
            'email' => 'hola@nandosart.es',
            'telefono' => '600 123 456',
            'instagram' => 'https://instagram.com/nando',
            'ubicacion' => 'Cádiz',
        ])
        ->assertRedirect(route('admin.paginas.index'))
        ->assertSessionHas('success');

    $this->get('/contacto')
        ->assertSee('<title>Hablemos · Nando&#039;s Art</title>', false)
        ->assertSee('Respondo en 24 horas.')
        ->assertSee('hola@nandosart.es')
        ->assertSee('Cádiz')
        ->assertSee('href="https://instagram.com/nando"', false)
        ->assertSee('@nando');
});

test('los datos de contacto se validan', function () {
    $this->actingAs(admin())
        ->put('/admin/paginas/contacto', [
            'titulo' => '',
            'contenido' => 'Texto',
            'email' => 'no-es-un-email',
            'telefono' => 'llámame',
            'instagram' => 'javascript:alert(1)',
        ])
        ->assertSessionHasErrors(['titulo', 'email', 'telefono', 'instagram']);

    $this->actingAs(admin())
        ->put('/admin/paginas/contacto', ['titulo' => 'Contacto', 'contenido' => 'x', 'instagram' => 'https://evil.example/instagram.com/'])
        ->assertSessionHasErrors('instagram');
});

test('el admin puede cambiar y quitar la foto de sobre mí', function () {
    Storage::fake('public');

    $this->actingAs(admin())
        ->put('/admin/paginas/sobre-mi', [
            'titulo' => 'Quién soy',
            'contenido' => 'Pinto desde pequeño.',
            'foto' => UploadedFile::fake()->image('yo.jpg'),
        ])
        ->assertRedirect(route('admin.paginas.index'));

    $pagina = Pagina::obtener(Pagina::SOBRE_MI);
    $primera = $pagina->foto;
    Storage::disk('public')->assertExists('paginas/'.$primera);
    expect($primera)->not->toBe('yo.jpg');

    $this->get('/sobre-mi')->assertSee('Pinto desde pequeño.')->assertSee($pagina->fotoUrl());

    // Una foto nueva borra la anterior
    $this->actingAs(admin())->put('/admin/paginas/sobre-mi', [
        'titulo' => 'Quién soy', 'contenido' => 'Pinto desde pequeño.', 'foto' => UploadedFile::fake()->image('otra.png'),
    ]);
    $segunda = $pagina->fresh()->foto;
    Storage::disk('public')->assertMissing('paginas/'.$primera);
    Storage::disk('public')->assertExists('paginas/'.$segunda);

    // Quitar la foto vuelve a la de siempre
    $this->actingAs(admin())->put('/admin/paginas/sobre-mi', [
        'titulo' => 'Quién soy', 'contenido' => 'Pinto desde pequeño.', 'quitar_foto' => '1',
    ]);
    expect($pagina->fresh()->foto)->toBeNull();
    Storage::disk('public')->assertMissing('paginas/'.$segunda);
    $this->get('/sobre-mi')->assertSee(asset('images/nandofoto-352.webp'));
});

test('la foto de sobre mí tiene que ser una imagen', function () {
    Storage::fake('public');

    $this->actingAs(admin())
        ->put('/admin/paginas/sobre-mi', [
            'titulo' => 'Sobre mí', 'contenido' => 'Texto', 'foto' => UploadedFile::fake()->create('virus.php', 10),
        ])
        ->assertSessionHasErrors('foto');

    expect(Pagina::obtener(Pagina::SOBRE_MI)->foto)->toBeNull();
});
