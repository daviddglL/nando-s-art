<?php

use App\Http\Middleware\CabecerasSeguridad;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

$obraValida = fn (array $extra = []) => array_merge([
    'name' => 'Atardecer en Cádiz', 'description' => 'Acuarela sobre papel', 'height' => 30, 'width' => 40,
    'style' => 'acuarela', 'category' => 'paisaje', 'available' => '1',
], $extra);

// --- Miniaturas WebP con srcset ---

test('al crear una obra se generan sus miniaturas WebP y la API las da en srcset', function () use ($obraValida) {
    Storage::fake('public');

    $this->actingAs(admin())
        ->post('/galeria', $obraValida(['imagen' => UploadedFile::fake()->image('grande.jpg', 2000, 1500)]))
        ->assertRedirect('/galeria');

    $obra = Product::sole();
    $carpeta = $obra->carpetaMiniaturas();
    foreach ([400, 800, 1600] as $ancho) {
        Storage::disk('public')->assertExists("{$carpeta}/{$ancho}.webp");
        expect(getimagesizefromstring(Storage::disk('public')->get("{$carpeta}/{$ancho}.webp")))
            ->toMatchArray([0 => $ancho, 'mime' => 'image/webp']);
    }

    $srcset = $this->getJson('/api/products?style=acuarela')->json('data.0.srcset');
    expect($srcset)->toContain("/storage/{$carpeta}/400.webp 400w")
        ->and($srcset)->toContain("/storage/{$carpeta}/1600.webp 1600w");
});

test('una imagen estrecha no se amplía: la miniatura mayor tiene su ancho real', function () use ($obraValida) {
    Storage::fake('public');

    $this->actingAs(admin())
        ->post('/galeria', $obraValida(['imagen' => UploadedFile::fake()->image('pequena.png', 600, 400)]))
        ->assertRedirect();

    expect(Product::sole()->anchosMiniaturas())->toBe([400, 600]);
});

test('la ficha y el listado del admin usan las miniaturas', function () use ($obraValida) {
    Storage::fake('public');
    $admin = admin();
    $this->actingAs($admin)->post('/galeria', $obraValida(['imagen' => UploadedFile::fake()->image('a.jpg', 1000, 800)]));
    $obra = Product::sole();

    $this->get(route('obras.show', $obra->slug))
        ->assertOk()
        ->assertSee('srcset="'.$obra->srcset().'"', false);

    $this->actingAs($admin)->get('/admin/obras')
        ->assertSee(asset('storage/'.$obra->carpetaMiniaturas().'/400.webp'), false);
});

test('al cambiar la imagen de una obra se borran las miniaturas viejas y se crean las nuevas', function () use ($obraValida) {
    Storage::fake('public');
    $admin = admin();
    $this->actingAs($admin)->post('/galeria', $obraValida(['imagen' => UploadedFile::fake()->image('a.jpg', 900, 600)]));
    $obra = Product::sole();
    $carpetaVieja = $obra->carpetaMiniaturas();

    $this->actingAs($admin)
        ->put("/admin/obras/{$obra->id}", $obraValida(['imagen' => UploadedFile::fake()->image('b.jpg', 900, 600)]))
        ->assertRedirect();

    $obra->refresh();
    Storage::disk('public')->assertMissing($carpetaVieja);
    expect($obra->carpetaMiniaturas())->not->toBe($carpetaVieja)
        ->and($obra->anchosMiniaturas())->toBe([400, 800, 900]);
});

test('una obra sin miniaturas sigue funcionando con la imagen original', function () {
    Storage::fake('public');
    $obra = Product::factory()->create();

    expect($obra->srcset())->toBeNull()
        ->and($obra->miniaturaUrl())->toBe($obra->imagenUrl());
    $this->get(route('obras.show', $obra->slug))->assertOk()->assertDontSee('srcset=', false);
});

test('obras:miniaturas genera las miniaturas de las obras que ya existían', function () {
    Storage::fake('public');
    $obra = Product::factory()->create(['imagen' => 'vieja.jpg']);
    Storage::disk('public')->put('obras/vieja.jpg', UploadedFile::fake()->image('vieja.jpg', 1200, 800)->getContent());
    $sinImagen = Product::factory()->create(['name' => 'Perdida']);

    $this->artisan('obras:miniaturas')
        ->expectsOutputToContain("{$obra->name}: 400, 800, 1200 px")
        ->expectsOutputToContain('Perdida: no se encuentra la imagen')
        ->assertSuccessful();

    expect($obra->anchosMiniaturas())->toBe([400, 800, 1200])
        ->and($sinImagen->anchosMiniaturas())->toBe([]);
});

// --- Imágenes de las obras de ejemplo ---

test('el seeder copia las imágenes de ejemplo al disco public y crea sus miniaturas', function () {
    Storage::fake('public');

    $this->seed(ProductSeeder::class);

    expect(Product::count())->toBe(6);
    Product::all()->each(function (Product $obra) {
        expect(file_exists(ProductSeeder::CARPETA_IMAGENES.'/'.$obra->imagen))->toBeTrue();
        Storage::disk('public')->assertExists('obras/'.$obra->imagen);
        expect($obra->srcset())->not->toBeNull();
    });
});

test('en producción el seeder no crea obras ni presupuestos de ejemplo', function () {
    app()['env'] = 'production';
    config(['admin.password' => 'una-contraseña-segura']);

    // Sin pasar por `db:seed`, que en producción pide confirmación
    app(DatabaseSeeder::class)->setContainer(app())->__invoke();

    expect(Product::count())->toBe(0)
        ->and(User::sole()->isAdmin())->toBeTrue()
        ->and(User::sole()->hasVerifiedEmail())->toBeTrue();
});

// --- SEO y accesibilidad de la portada y la galería ---

test('la portada y la galería tienen título y descripción propios, un h1 y un solo main', function (string $url, string $titulo) {
    $html = $this->get($url)->assertOk()->getContent();

    expect($html)->toContain("<title>{$titulo}")
        ->and(substr_count($html, '<main'))->toBe(1)
        ->and(substr_count($html, '<h1'))->toBe(1)
        ->and($html)->not->toContain('Galería de Nando\'s Art: acuarela, óleo, rotulador y más. Obras originales y encargos personalizados.');
})->with([
    'portada' => ['/', 'Nando&#039;s Art · Acuarelas y retratos'],
    'galería' => ['/galeria', 'Galería de obras'],
]);

test('las imágenes de la portada tienen textos alt descriptivos y versiones WebP', function () {
    $html = $this->get('/')->getContent();

    expect($html)->not->toMatch('/alt="(Autor|Obra \d)"/')
        ->and($html)->toContain('alt="Retrato en acuarela de un hombre mayor de perfil')
        ->and($html)->toContain('images/zig2-480.webp 480w');

    foreach (['mujer-480', 'mujer-960', 'zig2-480', 'zig2-960', 'zig3-480', 'zig3-960', 'nandofoto-352'] as $imagen) {
        expect(public_path("images/{$imagen}.webp"))->toBeFile();
    }
});

// --- Registro: límite de intentos, honeypot y verificación de email ---

$registro = fn (int $i, array $extra = []) => array_merge([
    'name' => "Ana {$i}", 'email' => "ana{$i}@example.com",
    'password' => 'password123', 'password_confirmation' => 'password123',
], $extra);

test('al registrarse se envía el email de verificación', function () use ($registro) {
    Notification::fake();

    $this->post('/register', $registro(1))->assertRedirect('/dashboard');

    $user = User::sole();
    expect($user->hasVerifiedEmail())->toBeFalse();
    Notification::assertSentTo($user, VerifyEmail::class);
});

test('sin verificar el email no se pueden pedir presupuestos', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)->get('/presupuesto')->assertRedirect(route('verification.notice'));
    $this->actingAs($user)->post('/presupuesto', [])->assertRedirect(route('verification.notice'));
    $this->actingAs($user)->get('/dashboard')->assertRedirect(route('verification.notice'));
    $this->actingAs($user)->get(route('verification.notice'))->assertOk()->assertSee('Verifica tu email');
    // El perfil sí, para poder corregir un email mal escrito
    $this->actingAs($user)->get('/profile')->assertOk();
});

test('el enlace del email verifica la cuenta y se puede reenviar', function () {
    Notification::fake();
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)->post(route('verification.resend'))->assertSessionHas('resent');
    Notification::assertSentTo($user, VerifyEmail::class, function (VerifyEmail $aviso) use ($user) {
        $enlace = $aviso->toMail($user)->actionUrl;
        $this->actingAs($user)->get($enlace)->assertRedirect('/dashboard');

        return true;
    });

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
    $this->actingAs($user->fresh())->get('/presupuesto')->assertOk();
});

test('al cambiar el email en el perfil hay que volver a verificarlo', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->actingAs($user)->patch('/profile', ['name' => $user->name, 'email' => 'nuevo@example.com'])->assertRedirect();

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
    Notification::assertSentTo($user, VerifyEmail::class);
});

test('el registro tiene un límite de cuentas por hora desde la misma IP', function () use ($registro) {
    Notification::fake();

    foreach (range(1, 5) as $i) {
        $this->post('/register', $registro($i))->assertRedirect('/dashboard');
        auth()->logout();
    }

    $this->post('/register', $registro(6))->assertTooManyRequests();
    expect(User::count())->toBe(5);
});

test('el campo trampa del registro frena a los bots', function () use ($registro) {
    $this->get('/register')->assertSee('name="web"', false);

    $this->post('/register', $registro(1, ['web' => 'https://spam.example']))->assertSessionHasErrors('web');

    $this->assertGuest();
    expect(User::count())->toBe(0);
});

// --- Cabeceras de seguridad ---

test('todas las respuestas llevan las cabeceras de seguridad', function (string $url) {
    $this->get($url)
        ->assertHeader('Content-Security-Policy', CabecerasSeguridad::CSP)
        ->assertHeader('X-Frame-Options', 'DENY')
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
        ->assertHeaderMissing('Strict-Transport-Security');
})->with(['/', '/galeria', '/login', '/api/products', '/no-existe']);

test('HSTS solo se envía por HTTPS', function () {
    $this->get('https://localhost/')
        ->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
});

test('la CSP no permite scripts en línea ni de otros dominios, y las vistas no los usan', function () {
    expect(CabecerasSeguridad::CSP)->toContain("script-src 'self';")
        ->and(CabecerasSeguridad::CSP)->toContain("frame-ancestors 'none'");

    $vistas = collect(File::allFiles(resource_path('views')))
        ->filter(fn ($fichero) => preg_match('/\son[a-z]+="|<script(?![^>]*application\/json)[^>]*>\s*\S/', $fichero->getContents()))
        ->map->getRelativePathname();

    expect($vistas->all())->toBe([]);
});
