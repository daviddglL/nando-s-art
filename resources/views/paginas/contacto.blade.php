@extends('layouts.app')
@section('title', "{$pagina->titulo} · Nando's Art")
@section('description', $pagina->resumen())

@section('content')
<article class="contenedor py-16 md:py-24">
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_1.15fr] gap-14 lg:gap-20 items-start">
        <div>
            <p class="antetitulo">Hablemos</p>
            <h1 class="titulo-pagina mt-3">{{ $pagina->titulo }}</h1>
            <div class="mt-8 texto-largo whitespace-pre-line">{{ $pagina->contenido }}</div>

            @if ($pagina->email || $pagina->telefono || $pagina->instagram || $pagina->ubicacion)
                <dl class="mt-10 grid grid-cols-1 sm:grid-cols-[auto_1fr] gap-x-8 border-t border-linea">
                    @if ($pagina->email)
                        <dt class="pt-4 sm:py-4 sm:border-b border-linea text-sm text-tinta-tenue">Email</dt>
                        <dd class="pb-4 sm:py-4 border-b border-linea"><a href="mailto:{{ $pagina->email }}" class="enlace break-all">{{ $pagina->email }}</a></dd>
                    @endif
                    @if ($pagina->telefono)
                        <dt class="pt-4 sm:py-4 sm:border-b border-linea text-sm text-tinta-tenue">Teléfono</dt>
                        <dd class="pb-4 sm:py-4 border-b border-linea"><a href="{{ $pagina->telefonoEnlace() }}" class="enlace">{{ $pagina->telefono }}</a></dd>
                    @endif
                    @if ($pagina->instagram)
                        <dt class="pt-4 sm:py-4 sm:border-b border-linea text-sm text-tinta-tenue">Instagram</dt>
                        <dd class="pb-4 sm:py-4 border-b border-linea"><a href="{{ $pagina->instagram }}" target="_blank" rel="noopener noreferrer" class="enlace break-all">{{ '@'.trim(Str::after($pagina->instagram, 'instagram.com/'), '/') }}</a></dd>
                    @endif
                    @if ($pagina->ubicacion)
                        <dt class="pt-4 sm:py-4 sm:border-b border-linea text-sm text-tinta-tenue">Dónde estoy</dt>
                        <dd class="pb-4 sm:py-4 border-b border-linea">{{ $pagina->ubicacion }}</dd>
                    @endif
                </dl>
            @endif

            <div class="mt-10 rounded-2xl bg-noche text-papel/80 p-7 grano">
                <h2 class="font-display text-2xl text-papel">¿Tienes una idea para un encargo?</h2>
                <p class="mt-2 text-sm">Cuéntamela con medidas, estilo y una imagen de referencia, y te respondo con un presupuesto.</p>
                <a href="{{ route('encargos.create') }}" class="btn btn-acento mt-5">Pedir presupuesto</a>
            </div>
        </div>

        {{-- Para escribir sin crear una cuenta (MensajeContactoController) --}}
        <form id="formulario-contacto" method="POST" action="{{ route('contacto.enviar') }}" class="lamina space-y-5 scroll-mt-28">
            @csrf
            <div>
                <h2 class="font-display text-3xl">Escríbeme</h2>
                <p class="mt-1 text-sm text-tinta-tenue">No hace falta crear una cuenta. Te contesto al email que pongas.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="contacto-nombre" class="etiqueta">Nombre</label>
                    <input id="contacto-nombre" name="nombre" value="{{ old('nombre') }}" required maxlength="100" autocomplete="name" class="campo">
                    @error('nombre') <p class="error-campo">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="contacto-email" class="etiqueta">Email</label>
                    <input id="contacto-email" type="email" name="email" value="{{ old('email') }}" required maxlength="255" autocomplete="email" class="campo">
                    @error('email') <p class="error-campo">{{ $message }}</p> @enderror
                </div>
            </div>
            <div>
                <label for="contacto-mensaje" class="etiqueta">Mensaje</label>
                <textarea id="contacto-mensaje" name="mensaje" rows="7" required minlength="10" maxlength="5000" class="campo">{{ old('mensaje') }}</textarea>
                @error('mensaje') <p class="error-campo">{{ $message }}</p> @enderror
            </div>
            {{-- Campo trampa: oculto para las personas, los bots lo rellenan --}}
            <div class="hidden" aria-hidden="true">
                <label for="contacto-web">Deja este campo vacío</label>
                <input id="contacto-web" type="text" name="web" value="" tabindex="-1" autocomplete="off">
            </div>
            <button type="submit" class="btn btn-primario w-full sm:w-auto">Enviar mensaje</button>
        </form>
    </div>
</article>
@endsection
