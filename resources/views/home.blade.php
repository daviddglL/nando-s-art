

@extends('layouts.app')
@if (Auth::check())
    <p>Hola, {{ Auth::user()->name }} ({{ Auth::user()->role }})</p>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Cerrar sesión</button>
    </form>
@else
    <p>No estás autenticado</p>
@endif
