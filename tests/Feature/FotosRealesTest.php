<?php

use App\Actions\GenerarMiniaturas;
use App\Actions\GuardarImagen;
use App\Models\Encargo;
use App\Models\Pagina;
use App\Models\Product;
use App\Models\User;
use App\Notifications\MensajeContacto;
use App\Notifications\NuevoPresupuesto;
use App\Support\Imagen;
use App\Support\Markdown;
use Database\Seeders\UserSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

beforeEach(fn () => Storage::fake('public'));

// JPEG de $ancho×$alto con la etiqueta EXIF de orientación (6 = el móvil estaba en vertical)
function jpegConOrientacion(int $ancho, int $alto, int $orientacion): string
{
    $gd = imagecreatetruecolor($ancho, $alto);
    ob_start();
    imagejpeg($gd);
    $jpeg = (string) ob_get_clean();

    $tiff = "MM\0*\0\0\0\x08".pack('n', 1).pack('nnN', 0x0112, 3, 1).pack('n', $orientacion)."\0\0".pack('N', 0);
    $app1 = "Exif\0\0".$tiff;

    return substr($jpeg, 0, 2)."\xFF\xE1".pack('n', strlen($app1) + 2).$app1.substr($jpeg, 2);
}

// --- Fotos de cámara y de móvil ---

test('el panel acepta fotos de hasta 15 MB y rechaza las más grandes', function () {
    $datos = ['name' => 'Grande', 'description' => 'Foto de cámara', 'height' => 30, 'width' => 40, 'style' => 'acuarela', 'category' => 'paisaje'];

    $this->actingAs(admin())
        ->post(route('products.store'), $datos + ['imagen' => UploadedFile::fake()->image('foto.jpg', 800, 600)->size(4000)])
        ->assertSessionHasNoErrors();

    $this->post(route('products.store'), ['name' => 'Enorme'] + $datos + ['imagen' => UploadedFile::fake()->image('foto.jpg', 800, 600)->size(16000)])
        ->assertSessionHasErrors('imagen');
});

test('una foto grande se guarda reducida a 2400 px de lado', function () {
    $foto = UploadedFile::fake()->image('camara.jpg', 4000, 3000);

    $nombre = app(GuardarImagen::class)->handle($foto, 'obras');

    [$ancho, $alto] = getimagesizefromstring(Storage::disk('public')->get('obras/'.$nombre));
    expect([$ancho, $alto])->toBe([2400, 1800])
        ->and($nombre)->toEndWith('.jpg');
});

test('una foto pequeña y derecha se guarda tal cual', function () {
    $foto = UploadedFile::fake()->image('pequena.png', 600, 400);

    $nombre = app(GuardarImagen::class)->handle($foto, 'obras');

    expect(Storage::disk('public')->get('obras/'.$nombre))->toBe($foto->getContent());
});

test('una foto de móvil en vertical se guarda derecha y sus miniaturas también', function () {
    // Guardada como 60×30 con orientación 6: el navegador la enseña de 30×60
    $bytes = jpegConOrientacion(60, 30, 6);
    expect(Imagen::orientacion($bytes))->toBe(6)
        ->and(Imagen::medidas($bytes))->toBe([30, 60]);

    $ruta = tempnam(sys_get_temp_dir(), 'exif');
    file_put_contents($ruta, $bytes);
    $nombre = app(GuardarImagen::class)->handle(new UploadedFile($ruta, 'movil.jpg', 'image/jpeg', null, true), 'obras');

    [$ancho, $alto] = getimagesizefromstring(Storage::disk('public')->get('obras/'.$nombre));
    expect([$ancho, $alto])->toBe([30, 60]);

    // Las obras que ya estaban subidas giradas: las miniaturas leen el EXIF de la original
    Storage::disk('public')->put('obras/antigua.jpg', $bytes);
    $obra = Product::factory()->create(['imagen' => 'antigua.jpg']);
    app(GenerarMiniaturas::class)->handle($obra);

    [$ancho, $alto] = getimagesizefromstring(Storage::disk('public')->get($obra->carpetaMiniaturas().'/30.webp'));
    expect([$ancho, $alto])->toBe([30, 60]);
});

test('el visor y las redes usan la miniatura de 1600 px y no la original', function () {
    Storage::disk('public')->put('obras/grande.jpg', UploadedFile::fake()->image('grande.jpg', 2000, 1500)->getContent());
    $obra = Product::factory()->create(['imagen' => 'grande.jpg']);
    app(GenerarMiniaturas::class)->handle($obra);

    $grande = asset('storage/'.$obra->carpetaMiniaturas().'/1600.webp');
    expect($obra->imagenGrandeUrl())->toBe($grande);

    $this->get(route('obras.show', $obra->slug))
        ->assertSee('<meta property="og:image" content="'.$grande.'">', false)
        ->assertSee('data-lightbox-imagen src="'.$grande.'"', false);
});

// --- Producción ---

test('el admin de pruebas solo se crea si se pide y nunca en producción', function () {
    config(['admin.pruebas' => false]);
    $this->seed(UserSeeder::class);
    expect(User::where('email', 'admin@example.com')->exists())->toBeFalse();

    User::query()->delete();
    config(['admin.pruebas' => true]);
    $this->seed(UserSeeder::class);
    expect(User::where('email', 'admin@example.com')->exists())->toBeTrue();
});

test('las páginas públicas no escriben en la base de datos', function () {
    $this->get('/sobre-mi')->assertOk()->assertSee('Soy Fernando Delgado');
    $this->get('/contacto')->assertOk();

    expect(Pagina::count())->toBe(0);
});

// --- Contacto sin cuenta ---

test('cualquiera puede escribir al artista desde contacto sin crear cuenta', function () {
    Notification::fake();
    config(['admin.email_contacto' => 'artista@example.com']);

    $this->post(route('contacto.enviar'), [
        'nombre' => 'Ana',
        'email' => 'ana@example.com',
        'mensaje' => '¿Haces retratos de mascotas?',
    ])->assertRedirect(route('paginas.contacto').'#formulario-contacto');

    Notification::assertSentOnDemand(MensajeContacto::class, function (MensajeContacto $aviso, array $canales, object $destino) {
        $correo = $aviso->toMail($destino);

        return $destino->routes['mail'] === 'artista@example.com'
            && $aviso->nombre === 'Ana'
            && $correo->replyTo[0][0] === 'ana@example.com';
    });
});

test('el formulario de contacto frena a los bots y pide los datos', function () {
    Notification::fake();

    $this->post(route('contacto.enviar'), ['nombre' => 'Bot', 'email' => 'bot@example.com', 'mensaje' => 'Compra seguidores baratos', 'web' => 'spam'])
        ->assertSessionHasErrors('web');
    $this->post(route('contacto.enviar'), ['nombre' => '', 'email' => 'no-es-email', 'mensaje' => 'corto'])
        ->assertSessionHasErrors(['nombre', 'email', 'mensaje']);

    Notification::assertNothingSent();
});

test('el formulario de contacto tiene límite de envíos por IP', function () {
    Notification::fake();
    $datos = ['nombre' => 'Ana', 'email' => 'ana@example.com', 'mensaje' => 'Un mensaje de prueba'];

    foreach (range(1, 3) as $_) {
        $this->post(route('contacto.enviar'), $datos)->assertRedirect();
    }
    $this->post(route('contacto.enviar'), $datos)->assertTooManyRequests();
});

test('la página de contacto enseña el formulario', function () {
    $this->get('/contacto')
        ->assertSee('action="'.route('contacto.enviar').'"', false)
        ->assertSee('name="mensaje"', false);
});

// --- Detalles ---

test('el texto de los visitantes no puede colar enlaces en los emails', function () {
    expect(Markdown::escapar('[Pulsa aquí](https://malo.example)'))
        ->toBe('\[Pulsa aquí\]\(https://malo\.example\)');

    expect(new NuevoPresupuesto(Encargo::factory()->make()))->deleteWhenMissingModels->toBeTrue();
});

test('el presupuesto tiene límites de texto y de medidas', function () {
    $cliente = User::factory()->create();

    $this->actingAs($cliente)->post(route('encargos.store'), [
        'description' => str_repeat('a', 5001),
        'height' => 100000,
        'width' => 0,
        'style' => 'acuarela',
        'category' => 'retrato',
    ])->assertSessionHasErrors(['description', 'height', 'width']);
});

test('buscar % o _ no devuelve todas las obras', function () {
    Product::factory()->create(['name' => 'Gato', 'description' => 'Un gato']);
    Product::factory()->create(['name' => 'Al 100% azul', 'description' => 'Marina']);

    $this->getJson('/api/products?q=%25')->assertJsonCount(1, 'data')->assertJsonPath('data.0.name', 'Al 100% azul');
    $this->getJson('/api/products?q=_')->assertJsonCount(0, 'data');
});
