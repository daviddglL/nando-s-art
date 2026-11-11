@extends('layouts.app')

@section('title', "Mi perfil · Nando's Art")

@section('content')
<div class="max-w-2xl mx-auto py-10 px-4 space-y-6">
    <h1 class="titulo-seccion">Mi perfil</h1>

    @if (session('success'))
        <div class="aviso aviso-ok" role="status">{{ session('success') }}</div>
    @endif

    {{-- Datos de la cuenta --}}
    <section class="lamina">
        <h2 class="font-display text-2xl mb-4">Datos de la cuenta</h2>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label for="name" class="etiqueta">Nombre</label>
                <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name"
                    class="campo">
                @error('name', 'updateProfile')
                    <span class="error-campo">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="email" class="etiqueta">Correo electrónico</label>
                <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                    class="campo">
                @error('email', 'updateProfile')
                    <span class="error-campo">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn btn-primario">
                Guardar
            </button>
        </form>
    </section>

    {{-- Cambiar contraseña --}}
    <section class="lamina">
        <h2 class="font-display text-2xl mb-4">Cambiar contraseña</h2>

        <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="current_password" class="etiqueta">Contraseña actual</label>
                <input id="current_password" type="password" name="current_password" required autocomplete="current-password"
                    class="campo">
                @error('current_password', 'updatePassword')
                    <span class="error-campo">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="password" class="etiqueta">Contraseña nueva</label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                    class="campo">
                @error('password', 'updatePassword')
                    <span class="error-campo">{{ $message }}</span>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="etiqueta">Repite la contraseña nueva</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                    class="campo">
            </div>

            <button type="submit" class="btn btn-primario">
                Cambiar contraseña
            </button>
        </form>
    </section>

    {{-- Borrar la cuenta --}}
    <section class="lamina">
        <h2 class="text-xl font-semibold text-tinta mb-2">Borrar la cuenta</h2>
        <p class="text-sm text-tinta-suave mb-4">Se borrarán tu cuenta y tus presupuestos. No se puede deshacer.</p>

        <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-4"
            data-confirmar="¿Seguro que quieres borrar tu cuenta?">
            @csrf
            @method('DELETE')

            <div>
                <label for="delete_password" class="etiqueta">Contraseña</label>
                <input id="delete_password" type="password" name="password" required autocomplete="current-password"
                    class="campo">
                @error('password', 'deleteUser')
                    <span class="error-campo">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="py-2 px-6 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-md transition">
                Borrar mi cuenta
            </button>
        </form>
    </section>
</div>
@endsection
