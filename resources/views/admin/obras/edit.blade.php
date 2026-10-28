@extends('layouts.app')
@section('title', "Editar {$obra->name} · Nando's Art")

@section('content')
    <div class="max-w-2xl mx-auto my-10 bg-white p-6 sm:p-8 rounded-lg shadow-md">
        <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Editar obra</h1>

        <form method="POST" action="{{ route('admin.obras.update', $obra) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            @include('admin.obras.campos', ['obra' => $obra])

            <div class="flex justify-center gap-4">
                <a href="{{ route('admin.obras.index') }}" class="py-2 px-6 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">Cancelar</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg shadow-sm">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
@endsection
