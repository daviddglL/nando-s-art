@php
    // Los datos de contacto salen de la página Contacto que edita el artista en el panel
    $contacto = \App\Models\Pagina::leer(\App\Models\Pagina::CONTACTO);
    $usuarioInstagram = $contacto->instagram ? trim(\Illuminate\Support\Str::after($contacto->instagram, 'instagram.com/'), '/') : null;
@endphp
<footer class="mt-24 bg-noche text-papel/75 grano">
    <div class="contenedor py-16 grid gap-12 md:grid-cols-[1.4fr_1fr_1fr]">
        <div>
            <p class="font-display text-3xl md:text-4xl text-papel leading-tight">¿Tienes una idea?<br><span class="italic text-acento-claro">Pintémosla juntos.</span></p>
            <a href="{{ route('encargos.create') }}" class="btn btn-acento mt-6">Pedir un presupuesto</a>
        </div>

        <div>
            <h2 class="font-sans text-xs font-semibold tracking-[0.2em] uppercase text-papel/50 mb-4">La web</h2>
            <ul class="space-y-2">
                <li><a href="{{ route('galeria') }}" class="hover:text-white">Galería</a></li>
                <li><a href="{{ route('paginas.sobre-mi') }}" class="hover:text-white">Sobre mí</a></li>
                <li><a href="{{ route('paginas.contacto') }}" class="hover:text-white">Contacto</a></li>
            </ul>
        </div>

        <div>
            <h2 class="font-sans text-xs font-semibold tracking-[0.2em] uppercase text-papel/50 mb-4">Contacto</h2>
            <ul class="space-y-2">
                @if ($contacto->email)
                    <li><a href="mailto:{{ $contacto->email }}" class="hover:text-white break-all">{{ $contacto->email }}</a></li>
                @endif
                @if ($contacto->instagram)
                    <li>
                        <a href="{{ $contacto->instagram }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 hover:text-white" aria-label="Instagram de Nando's Art ({{ '@'.$usuarioInstagram }})">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M7.75 2h8.5A5.75 5.75 0 0 1 22 7.75v8.5A5.75 5.75 0 0 1 16.25 22h-8.5A5.75 5.75 0 0 1 2 16.25v-8.5A5.75 5.75 0 0 1 7.75 2zm0 1.5A4.25 4.25 0 0 0 3.5 7.75v8.5A4.25 4.25 0 0 0 7.75 20.5h8.5A4.25 4.25 0 0 0 20.5 16.25v-8.5A4.25 4.25 0 0 0 16.25 3.5h-8.5zm4.25 3.25a5.25 5.25 0 1 1 0 10.5 5.25 5.25 0 0 1 0-10.5zm0 1.5a3.75 3.75 0 1 0 0 7.5 3.75 3.75 0 0 0 0-7.5zm6 1.25a1 1 0 1 1-2 0 1 1 0 0 1 2 0z"/></svg>
                            {{ '@'.$usuarioInstagram }}
                        </a>
                    </li>
                @endif
                @if ($contacto->ubicacion)
                    <li>{{ $contacto->ubicacion }}</li>
                @endif
            </ul>
        </div>
    </div>
    <div class="border-t border-white/10">
        <div class="contenedor py-6 flex flex-col sm:flex-row gap-2 justify-between text-xs text-papel/50">
            <p>© {{ date('Y') }} Nando's Art · Fernando Delgado. Todos los derechos reservados.</p>
            <p>Acuarela, óleo, rotulador y dibujo. Obras originales y por encargo.</p>
        </div>
    </div>
</footer>
