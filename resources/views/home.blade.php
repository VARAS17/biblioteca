@extends('layouts.public-layout')

@section('content')
    <!-- Aquí va el contenido de tu página de inicio (Hero, libros destacados, etc.) -->
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h1 class="text-3xl font-bold text-blue-900 mb-4">Bienvenido a la Biblioteca de Informática</h1>
                <p class="text-gray-600">Explora nuestro catálogo y gestiona tus préstamos de forma eficiente.</p>
                
                <!-- Aquí podrías incluir tu componente Livewire de buscador -->
                <div class="mt-8">
                </div>
            </div>
        </div>
    </div>
@endsection