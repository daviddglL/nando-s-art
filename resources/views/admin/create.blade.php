@extends('layouts.app')
@section('title', "Nueva obra · Nando's Art")

@section('content')
    <div class="max-w-2xl mx-auto my-14 lamina">
        <h1 class="font-display text-4xl text-center mb-8">Formulario de Nueva Obra</h1>

        <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @include('admin.obras.campos', ['obra' => null])

            <div class="text-center">
                <button type="submit" class="btn btn-primario">
                    Guardar
                </button>
            </div>
        </form>
    </div>
@endsection
