@extends('layouts.app')
@section('title', "Nando's Art · Acuarelas y retratos de Fernando Delgado")
@section('description', 'Acuarelas, retratos y paisajes de Fernando Delgado. Descubre sus obras originales y pide un encargo personalizado.')

@push('head')
    {{-- Datos estructurados: Google entiende que la web es de un artista y quién es --}}
    @php
        $personaJsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'Person',
        'name' => 'Fernando Delgado',
        'alternateName' => "Nando's Art",
        'jobTitle' => 'Artista',
        'url' => url('/'),
        'image' => $sobreMi->fotoUrl() ?? asset('images/nandofoto-352.webp'),
        'sameAs' => array_values(array_filter([$contacto->instagram])),
    ];
    @endphp
    <script type="application/ld+json">@json($personaJsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP)</script>
@endpush

@section('content')
{{-- Portada: el nombre del artista y un pequeño collage de tres de sus obras --}}
<section class="relative overflow-hidden grano">
    <div class="contenedor grid lg:grid-cols-[1.05fr_1fr] gap-14 lg:gap-10 items-center pt-14 pb-20 lg:pt-20 lg:pb-28">
        <div class="motion-safe:animate-aparecer">
            <p class="antetitulo">Acuarela · Retrato · Paisaje</p>
            <h1 class="mt-5 font-display text-[clamp(3rem,8vw,6.5rem)] leading-[0.95] font-medium tracking-tight">
                Fernando<br><span class="italic text-acento">Delgado</span>
            </h1>
            <p class="mt-7 max-w-md texto-largo">Obras originales en acuarela, óleo y rotulador: retratos, paisajes y encargos hechos a mano.</p>
            <div class="mt-9 flex flex-wrap gap-3">
                <a href="{{ route('galeria') }}" class="btn btn-primario">Ver la galería <span aria-hidden="true">→</span></a>
                <a href="{{ route('encargos.create') }}" class="btn btn-secundario">Pedir un encargo</a>
            </div>
        </div>

        <div class="relative h-[26rem] sm:h-[32rem] lg:h-[36rem]" aria-label="Algunas obras de Fernando Delgado" role="group">
            <figure class="absolute left-0 top-6 w-[58%] marco -rotate-3 motion-safe:animate-aparecer [animation-delay:120ms]">
                <img src="{{ asset('images/mujer-960.webp') }}"
                     srcset="{{ asset('images/mujer-480.webp') }} 480w, {{ asset('images/mujer-960.webp') }} 960w"
                     sizes="(min-width: 1024px) 330px, 55vw" width="960" height="1200"
                     alt="Retrato en acuarela de un hombre mayor de perfil, con turbante rojo y barba blanca sobre un fondo gris oscuro"
                     class="w-full h-auto aspect-[4/5] object-cover">
            </figure>
            <figure class="absolute right-0 top-0 w-[46%] marco rotate-2 motion-safe:animate-aparecer [animation-delay:240ms]">
                <img src="{{ asset('images/zig2-960.webp') }}"
                     srcset="{{ asset('images/zig2-480.webp') }} 480w, {{ asset('images/zig2-960.webp') }} 960w"
                     sizes="(min-width: 1024px) 260px, 45vw" width="960" height="960"
                     alt="Retrato en acuarela de un hombre con gorra gris y barba canosa, mirando hacia la derecha"
                     class="w-full h-auto aspect-square object-cover">
            </figure>
            <figure class="absolute right-[6%] bottom-0 w-[52%] marco -rotate-1 motion-safe:animate-aparecer [animation-delay:360ms]">
                <img src="{{ asset('images/zig3-960.webp') }}"
                     srcset="{{ asset('images/zig3-480.webp') }} 480w, {{ asset('images/zig3-960.webp') }} 960w"
                     sizes="(min-width: 1024px) 300px, 50vw" width="960" height="720" loading="lazy"
                     alt="Paisaje en acuarela de unas casas blancas junto a un río en calma, con hierba alta y cielo gris"
                     class="w-full h-auto aspect-[4/3] object-cover">
            </figure>
        </div>
    </div>
</section>

{{-- Obras destacadas: las marca el admin en /admin/obras --}}
@if ($destacadas->isNotEmpty())
    <section class="contenedor py-20" aria-labelledby="titulo-destacadas">
        <div class="flex flex-wrap items-end justify-between gap-6 mb-10">
            <div>
                <p class="antetitulo">Selección del artista</p>
                <h2 id="titulo-destacadas" class="titulo-seccion mt-3">Obras destacadas</h2>
            </div>
            <a href="{{ route('galeria') }}" class="enlace">Ver toda la galería →</a>
        </div>
        <ul class="grid grid-cols-2 lg:grid-cols-3 gap-x-5 gap-y-12 md:gap-x-8">
            @foreach ($destacadas as $obra)
                <li @class(['lg:translate-y-10' => $loop->iteration % 3 === 2])>
                    <a href="{{ route('obras.show', $obra->slug) }}" class="group block">
                        <div class="marco transition duration-500 group-hover:-translate-y-1 group-hover:shadow-flotante">
                            <img src="{{ $obra->imagenUrl() }}" @if ($obra->srcset()) srcset="{{ $obra->srcset() }}" sizes="(min-width: 1024px) 340px, 45vw" @endif alt="{{ $obra->name }}" loading="lazy" class="w-full aspect-[4/5] object-cover">
                        </div>
                        {{-- Cartela, como en una exposición --}}
                        <span class="mt-4 block font-display text-xl leading-snug group-hover:text-acento transition">{{ $obra->name }}</span>
                        <span class="mt-1 block text-sm text-tinta-tenue">{{ $obra->style->label() }} · {{ $obra->category->label() }}</span>
                        <span class="mt-2 block text-sm font-semibold">{{ $obra->available ? $obra->precioTexto() : 'Vendida' }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </section>
@endif

{{-- El artista: foto y comienzo del texto de «Sobre mí», que edita en el panel --}}
<section class="bg-papel-oscuro/60 border-y border-linea">
    <div class="contenedor py-20 grid md:grid-cols-[minmax(0,20rem)_1fr] gap-12 items-center">
        <img src="{{ $sobreMi->fotoUrl() ?? asset('images/nandofoto-352.webp') }}" alt="Fernando Delgado, el artista"
             width="352" height="352" loading="lazy"
             class="w-56 md:w-full aspect-square object-cover rounded-full mx-auto shadow-cuadro ring-8 ring-papel-claro">
        <div>
            <p class="antetitulo">El artista</p>
            <h2 class="titulo-seccion mt-3">{{ $sobreMi->titulo }}</h2>
            <p class="mt-6 texto-largo max-w-2xl whitespace-pre-line">{{ $presentacion }}</p>
            <a href="{{ route('paginas.sobre-mi') }}" class="enlace inline-block mt-6">Seguir leyendo →</a>
        </div>
    </div>
</section>

{{-- Frase del día (resources/js/frase.js) --}}
<section class="contenedor py-20 text-center" aria-labelledby="titulo-frase">
    <h2 id="titulo-frase" class="antetitulo">Frase del día</h2>
    <div id="frase-del-dia" class="mt-6 max-w-3xl mx-auto min-h-24" aria-live="polite"></div>
</section>

{{-- Cómo funciona un encargo --}}
<section class="contenedor pb-8" aria-labelledby="titulo-encargos">
    <div class="lamina grid lg:grid-cols-[1fr_1.6fr] gap-10 items-center">
        <div>
            <p class="antetitulo">Por encargo</p>
            <h2 id="titulo-encargos" class="titulo-seccion mt-3">Una obra pensada para ti</h2>
            <p class="mt-4 text-tinta-suave">Un retrato, tu mascota, el paisaje de tu pueblo… Cuéntame la idea y te digo cuánto costaría, sin compromiso.</p>
        </div>
        <ol class="grid sm:grid-cols-3 gap-6">
            @foreach ([
                ['Cuéntame la idea', 'Medidas, técnica y, si quieres, una foto de referencia.'],
                ['Recibe el presupuesto', 'Te respondo con el precio y los detalles en tu cuenta y por email.'],
                ['Manos a la obra', 'Si te encaja, me pongo a pintar tu cuadro.'],
            ] as [$paso, $texto])
                <li class="border-t border-tinta/15 pt-4">
                    <span class="font-display text-3xl italic text-acento">{{ $loop->iteration }}</span>
                    <h3 class="mt-2 font-sans text-base font-semibold">{{ $paso }}</h3>
                    <p class="mt-1 text-sm text-tinta-suave">{{ $texto }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>
@endsection
