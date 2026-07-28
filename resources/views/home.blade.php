@extends('layouts.public-layout')

@section('content')
<div>
    {{-- HERO --}}
    <section class="py-20 text-center">
        <p class="text-[10px] font-black tracking-[0.3em] text-stone-400 uppercase mb-4">Sistema de Gestión Bibliotecaria</p>
        <h1 class="text-5xl lg:text-7xl font-black text-stone-900 tracking-tighter leading-none mb-6">
            ALEJANDRIA<br><span class="text-stone-400">CLOUD</span>
        </h1>
        <p class="text-stone-500 text-sm max-w-xl mx-auto leading-relaxed">
            Plataforma digital de la Biblioteca de la Escuela de Informática — UNT. Gestión integral de préstamos, catálogo bibliográfico y circulación de ejemplares.
        </p>
    </section>

    {{-- DIVISOR --}}
    <div class="h-px bg-stone-200 max-w-2xl mx-auto mb-16"></div>

    {{-- QUÉ HACE --}}
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-20">
        <div class="bg-white rounded-2xl p-6 border border-stone-100 shadow-sm">
            <div class="h-10 w-10 bg-stone-100 rounded-xl flex items-center justify-center mb-4">
                <svg class="h-5 w-5 text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h3 class="text-sm font-black text-stone-800 uppercase tracking-tight mb-2">Catálogo Digital</h3>
            <p class="text-xs text-stone-400 leading-relaxed">Consulta el acervo bibliográfico completo, verifica disponibilidad de ejemplares y realiza reservas desde cualquier dispositivo.</p>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-stone-100 shadow-sm">
            <div class="h-10 w-10 bg-stone-100 rounded-xl flex items-center justify-center mb-4">
                <svg class="h-5 w-5 text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M5 12h14"/><path d="m12 5 7 7-7 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h3 class="text-sm font-black text-stone-800 uppercase tracking-tight mb-2">Flujo de Préstamos</h3>
            <p class="text-xs text-stone-400 leading-relaxed">Proceso estructurado en tres etapas: registro de solicitud, verificación de catálogo y entrega física del ejemplar por el personal asignado.</p>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-stone-100 shadow-sm">
            <div class="h-10 w-10 bg-stone-100 rounded-xl flex items-center justify-center mb-4">
                <svg class="h-5 w-5 text-stone-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h3 class="text-sm font-black text-stone-800 uppercase tracking-tight mb-2">Control de Multas</h3>
            <p class="text-xs text-stone-400 leading-relaxed">Cálculo automático de mora por retraso (S/ 0.50 por día). Bloqueo de usuarios con deuda mayor a S/ 10.00 conforme al reglamento.</p>
        </div>
    </section>

    {{-- FLUJO VISUAL --}}
    <section class="bg-white rounded-3xl border border-stone-100 shadow-sm p-10 mb-20">
        <p class="text-[10px] font-black tracking-[0.25em] text-stone-400 uppercase mb-8 text-center">Cómo funciona</p>
        <div class="flex flex-col md:flex-row items-center justify-center gap-4">

            @php
                $steps = [
                    ['num' => '01', 'role' => 'Encargado de Usuarios', 'action' => 'Registra la solicitud del alumno o docente'],
                    ['num' => '02', 'role' => 'Encargado de Catálogo', 'action' => 'Verifica disponibilidad y asigna el ejemplar físico'],
                    ['num' => '03', 'role' => 'Encargado de Préstamos', 'action' => 'Entrega el ejemplar y activa el préstamo'],
                    ['num' => '04', 'role' => 'Devolución', 'action' => 'Se registra la entrega y se calculan multas si aplica'],
                ];
            @endphp

            @foreach($steps as $i => $step)
                <div class="flex flex-col items-center text-center max-w-[160px]">
                    <div class="h-12 w-12 rounded-2xl bg-stone-900 text-white flex items-center justify-center text-xs font-black mb-3">
                        {{ $step['num'] }}
                    </div>
                    <p class="text-[9px] font-black text-stone-400 uppercase tracking-widest mb-1">{{ $step['role'] }}</p>
                    <p class="text-xs text-stone-600 leading-snug">{{ $step['action'] }}</p>
                </div>
                @if($i < count($steps) - 1)
                    <div class="hidden md:block h-px w-8 bg-stone-200 flex-shrink-0"></div>
                @endif
            @endforeach
        </div>
    </section>

    {{-- ROLES --}}
    <section class="mb-20">
        <p class="text-[10px] font-black tracking-[0.25em] text-stone-400 uppercase mb-8 text-center">Acceso por perfil</p>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            @php
                $roles = [
                    ['label' => 'Bibliotecario', 'desc' => 'Acceso total'],
                    ['label' => 'Enc. Usuarios', 'desc' => 'Solicitudes'],
                    ['label' => 'Enc. Catálogo', 'desc' => 'Disponibilidad'],
                    ['label' => 'Enc. Préstamos', 'desc' => 'Entrega y devolución'],
                    ['label' => 'Docente', 'desc' => 'Reserva + préstamos'],
                    ['label' => 'Alumno', 'desc' => 'Consulta + préstamos'],
                ];
            @endphp
            @foreach($roles as $role)
                <div class="bg-white rounded-2xl p-4 border border-stone-100 shadow-sm text-center">
                    <p class="text-xs font-black text-stone-800 mb-1">{{ $role['label'] }}</p>
                    <p class="text-[10px] text-stone-400">{{ $role['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- CTA --}}
    @guest
    <section class="text-center pb-10">
        <p class="text-xs text-stone-400 mb-6 uppercase tracking-widest font-bold">¿Eres parte de la Escuela de Informática?</p>
        <div class="flex items-center justify-center gap-4">
            <a href="{{ route('login') }}" class="bg-stone-800 text-white text-xs font-black px-8 py-3 rounded-full hover:bg-black transition-all uppercase tracking-widest">
                Iniciar Sesión
            </a>
            <a href="{{ route('catalogo') }}" class="text-xs font-black text-stone-500 hover:text-stone-800 uppercase tracking-widest transition-colors">
                Ver Catálogo →
            </a>
        </div>
    </section>
    @endguest
</div>
@endsection