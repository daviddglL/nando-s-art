<?php

use App\Galeria\Catalogo;
use App\Galeria\Obra;
use App\Galeria\Pagina;
use Tests\TestCase;

uses(TestCase::class);

// Si al editar resources/contenido/ algo queda mal escrito, estos tests dicen qué hay que corregir

test('todas las obras del fichero se leen bien y sus slugs no se repiten', function () {
    $obras = app(Catalogo::class)->todas();

    expect($obras)->not->toBeEmpty()
        ->and($obras->pluck('slug')->duplicates())->toBeEmpty();

    foreach ($obras as $obra) {
        expect($obra->slug)->toMatch('/^[a-z0-9]+(-[a-z0-9]+)*$/', "El slug «{$obra->slug}» solo puede tener minúsculas, números y guiones");
    }
});

test('todas las fotos de las obras existen en public/obras', function () {
    foreach (app(Catalogo::class)->todas() as $obra) {
        foreach (collect([$obra])->concat($obra->imagenes) as $foto) {
            expect(is_file($foto->rutaImagen()))->toBeTrue("Falta la foto public/obras/{$foto->imagen} de «{$obra->name}»");
        }
    }
});

test('como mucho salen 6 destacadas en la portada', function () {
    expect(app(Catalogo::class)->destacadas()->count())->toBeLessThanOrEqual(Catalogo::MAX_DESTACADAS);
});

test('una técnica mal escrita da un error que dice qué obra corregir', function () {
    Obra::desdeArray(1, [
        'slug' => 'prueba', 'titulo' => 'Prueba', 'descripcion' => 'x', 'tecnica' => 'acuarelas',
        'tema' => 'paisaje', 'alto' => 1, 'ancho' => 1, 'imagen' => 'x.webp',
    ]);
})->throws(InvalidArgumentException::class, 'La técnica «acuarelas» de la obra «Prueba» no existe');

test('si falta un campo, el error dice cuál', function () {
    Obra::desdeArray(1, ['slug' => 'prueba', 'titulo' => 'Prueba']);
})->throws(InvalidArgumentException::class, 'le falta el campo «descripcion»');

test('los slugs repetidos se detectan', function () {
    $fichero = tempnam(sys_get_temp_dir(), 'obras');
    $obra = ['slug' => 'igual', 'titulo' => 'A', 'descripcion' => 'x', 'tecnica' => 'oleo', 'tema' => 'otro', 'alto' => 1, 'ancho' => 1, 'imagen' => 'a.webp'];
    file_put_contents($fichero, '<?php return '.var_export([$obra, $obra], true).';');

    try {
        (new Catalogo($fichero))->todas();
    } finally {
        unlink($fichero);
    }
})->throws(InvalidArgumentException::class, 'Hay obras con el mismo slug: igual');

test('precio, vendida y opcionales', function () {
    $obra = Obra::desdeArray(1, [
        'slug' => 'p', 'titulo' => 'P', 'descripcion' => 'x', 'tecnica' => 'oleo', 'tema' => 'otro',
        'alto' => 29.7, 'ancho' => 42, 'imagen' => 'p.webp', 'precio' => 1250, 'vendida' => true, 'año' => 2024,
        'imagenes' => ['p-detalle.webp'],
    ]);

    expect($obra->precioTexto())->toBe('1.250,00 €')
        ->and($obra->available)->toBeFalse()
        ->and($obra->year)->toBe(2024)
        ->and($obra->imagenes)->toHaveCount(1);
});

test('las páginas Sobre mí y Contacto existen', function () {
    expect(Pagina::leer(Pagina::SOBRE_MI)->titulo)->toBe('Sobre mí')
        ->and(Pagina::leer(Pagina::CONTACTO)->usuarioInstagram())->toBe('@nandomtgalters');
});
