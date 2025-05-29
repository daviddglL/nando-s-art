@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Bienvenido, {{ $user->name }}</h1>
    <p>Rol: <strong>{{ $user->role }}</strong></p>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-danger">Cerrar sesión</button>
    </form>
</div>
@endsection


@if (Auth::check())
    <div style="padding: 1rem; background: #f0f0f0; border-bottom: 1px solid #ccc;">
        <p><strong>Usuario:</strong> {{ Auth::user()->name }}</p>
        <p><strong>Rol:</strong> {{ Auth::user()->role }}</p>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" style="padding: 0.5rem 1rem; background-color: #c00; color: white; border: none; cursor: pointer;">
                Cerrar sesión
            </button>
        </form>
    </div>
@endif
