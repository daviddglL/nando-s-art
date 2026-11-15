<?php

use App\Notifications\MensajeContacto;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\RateLimiter;

// El formulario de /contacto manda un email al artista y no guarda nada

beforeEach(function () {
    RateLimiter::clear('contacto');
    config(['contacto.email' => 'artista@example.com']);
});

function mensajeValido(array $cambios = []): array
{
    return array_merge([
        'nombre' => 'Ana',
        'email' => 'ana@example.com',
        'mensaje' => 'Hola, me gustaría encargar un retrato de mi perro.',
    ], $cambios);
}

test('el mensaje llega por email a CONTACTO_EMAIL, con «responder» a quien escribe', function () {
    Notification::fake();

    $this->post('/contacto', mensajeValido())
        ->assertRedirect(route('paginas.contacto').'#formulario-contacto')
        ->assertSessionHas('success');

    Notification::assertSentOnDemand(MensajeContacto::class, function (MensajeContacto $aviso, array $canales, AnonymousNotifiable $destino) {
        $correo = $aviso->toMail($destino);

        return $destino->routes['mail'] === 'artista@example.com'
            && $correo->replyTo === [['ana@example.com', 'Ana']]
            && str_contains(implode(' ', $correo->introLines), 'retrato de mi perro');
    });
});

test('sin CONTACTO_EMAIL llega al email de la página Contacto', function () {
    Notification::fake();
    config(['contacto.email' => null]);

    $this->post('/contacto', mensajeValido())->assertSessionHas('success');

    Notification::assertSentOnDemand(MensajeContacto::class, fn ($aviso, $canales, AnonymousNotifiable $destino) => $destino->routes['mail'] === 'info@fernandodelgadoart.com');
});

test('el correo se envía de verdad con el mailer configurado (sin colas)', function () {
    config(['mail.default' => 'array']);

    $this->post('/contacto', mensajeValido())->assertSessionHas('success');

    $enviados = app('mailer')->getSymfonyTransport()->messages();
    expect($enviados)->toHaveCount(1)
        ->and($enviados->first()->getOriginalMessage()->getSubject())->toBe('Mensaje de Ana desde la web');
});

test('si el correo falla, avisa y no pierde lo escrito', function () {
    // Un servidor SMTP que no existe: la conexión falla al momento
    config(['mail.default' => 'smtp', 'mail.mailers.smtp.host' => '127.0.0.1', 'mail.mailers.smtp.port' => 1]);

    $this->from('/contacto')->post('/contacto', mensajeValido())
        ->assertSessionHas('error', fn (string $error) => str_contains($error, 'info@fernandodelgadoart.com'))
        ->assertSessionHasInput('mensaje');
});

test('valida nombre, email y mensaje', function (array $datos, string $campo) {
    Notification::fake();

    $this->post('/contacto', mensajeValido($datos))->assertSessionHasErrors($campo);

    Notification::assertNothingSent();
})->with([
    'sin nombre' => [['nombre' => ''], 'nombre'],
    'email mal escrito' => [['email' => 'no-es-un-email'], 'email'],
    'mensaje muy corto' => [['mensaje' => 'hola'], 'mensaje'],
]);

test('el campo trampa frena a los bots', function () {
    Notification::fake();

    $this->post('/contacto', mensajeValido(['web' => 'http://spam.example']))->assertSessionHasErrors('web');

    Notification::assertNothingSent();
});

test('como mucho 3 mensajes por minuto desde la misma IP', function () {
    Notification::fake();

    foreach (range(1, 3) as $i) {
        $this->post('/contacto', mensajeValido())->assertRedirect();
    }
    $this->post('/contacto', mensajeValido())->assertTooManyRequests();
});

test('desde la ficha de una obra el mensaje sale ya empezado', function () {
    $this->get('/contacto?obra=casa-embrujada')
        ->assertOk()
        ->assertSee('me interesa la obra «Casa Embrujada»', false);

    $this->get('/contacto?obra=no-existe')->assertOk()->assertDontSee('me interesa la obra');
});
