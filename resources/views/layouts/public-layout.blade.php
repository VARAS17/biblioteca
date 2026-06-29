<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Biblioteca UNT | Excelencia Académica</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:300,400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        [x-cloak] { display: none !important; }
        
        body {
            /* Color "Stone Mist": Calma visual absoluta, elimina el deslumbramiento del blanco */
            background-color: #f1f2ee; 
            color: #2d3436;
        }

        /* Scrollbar orgánica y sutil */
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 20px; }
        
        .glass-nav {
            background: rgba(241, 242, 238, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .nav-link-active { 
            position: relative; 
            color: #1a302b !important; 
            font-weight: 600;
        }
        .nav-link-active::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 100%;
            height: 1.5px;
            background-color: #4b635a;
            border-radius: 2px;
        }

        /* Sombras suaves de estilo Apple */
        .premium-card {
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
    </style>
</head>
<body class="font-sans antialiased">

    <!-- NAVIGATION: Glassmorphism suave sobre el nuevo color de fondo -->
    <nav x-data="{ mobileMenu: false, adminDropdown: false, circDropdown: false, userDropdown: false }" 
         class="glass-nav border-b border-stone-200/60 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 lg:px-10">
            <div class="flex justify-between h-20">
                
                <!-- IZQUIERDA: Identidad Visual -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-4 group">
                        <div class="p-1.5 rounded-xl bg-stone-100 border border-stone-200 group-hover:border-stone-300 transition-all duration-500">
                            <img src="{{ asset('logobilbio.jpg') }}" alt="UNT" class="h-9 w-auto opacity-90 mix-blend-multiply">
                        </div>
                        <div class="hidden md:block">
                            <h1 class="text-xs font-bold tracking-[0.15em] text-stone-800 uppercase">Biblioteca UNT</h1>
                            <p class="text-[9px] text-stone-500 font-medium tracking-widest uppercase">Escuela de Informática</p>
                        </div>
                    </a>

                    <div class="hidden lg:flex items-center ml-12 space-x-8">
                        <a href="{{ route('home') }}" 
                           class="text-[13px] font-medium tracking-tight transition-colors {{ request()->routeIs('home') ? 'nav-link-active' : 'text-stone-500 hover:text-stone-900' }}">
                            Inicio
                        </a>
                        <a href="{{ route('catalogo') }}" 
                           class="text-[13px] font-medium tracking-tight transition-colors {{ request()->routeIs('catalogo') ? 'nav-link-active' : 'text-stone-500 hover:text-stone-900' }}">
                            Catálogo
                        </a>

                        @auth
                            @hasanyrole('alumno|docente')
                                <a href="{{ route('mis.prestamos') }}" class="text-[13px] font-medium text-stone-500 hover:text-stone-900 transition-colors">
                                    Mis Préstamos
                                </a>
                            @endhasanyrole

                            @role('bibliotecario')
                                <!-- GESTIÓN -->
                                <div class="relative">
                                    <button @click="adminDropdown = !adminDropdown; circDropdown = false" 
                                            class="flex items-center gap-1.5 text-[13px] font-medium text-stone-500 hover:text-stone-900 transition-colors">
                                        <span>Gestión</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" :class="adminDropdown ? 'rotate-180' : ''" class="transition-transform duration-300 opacity-60"><path d="m6 9 6 6 6-6"/></svg>
                                    </button>
                                    <div x-show="adminDropdown" @click.away="adminDropdown = false" x-cloak 
                                         class="absolute top-full left-0 mt-3 w-56 bg-stone-50 border border-stone-200 rounded-2xl shadow-xl py-2 z-50">
                                        <a href="{{ route('admin.libros') }}" class="flex items-center px-4 py-2.5 text-[13px] text-stone-600 hover:bg-stone-100 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3 opacity-60"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                                            Libros e Inventario
                                        </a>
                                        <a href="{{ route('admin.usuarios') }}" class="flex items-center px-4 py-2.5 text-[13px] text-stone-600 hover:bg-stone-100 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3 opacity-60"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                            Usuarios UNT
                                        </a>
                                        <div class="h-px bg-stone-200/50 mx-4 my-1"></div>
                                        <a href="{{ route('admin.multas') }}" class="flex items-center px-4 py-2.5 text-[13px] text-rose-700 font-medium hover:bg-rose-50 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3 opacity-80"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>
                                            Control de Multas
                                        </a>
                                    </div>
                                </div>

                                <!-- CIRCULACIÓN -->
                                <div class="relative">
                                    <button @click="circDropdown = !circDropdown; adminDropdown = false" 
                                            class="flex items-center gap-1.5 text-[13px] font-medium text-stone-500 hover:text-stone-900 transition-colors">
                                        <span>Circulación</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" :class="circDropdown ? 'rotate-180' : ''" class="transition-transform duration-300 opacity-60"><path d="m6 9 6 6 6-6"/></svg>
                                    </button>
                                    <div x-show="circDropdown" @click.away="circDropdown = false" x-cloak 
                                         class="absolute top-full left-0 mt-3 w-56 bg-stone-50 border border-stone-200 rounded-2xl shadow-xl py-2 z-50">
                                        <a href="{{ route('admin.prestamos') }}" class="flex items-center px-4 py-2.5 text-[13px] text-stone-600 hover:bg-stone-100 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3 text-stone-500"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                                            Nuevo Préstamo
                                        </a>
                                        <a href="{{ route('admin.devoluciones') }}" class="flex items-center px-4 py-2.5 text-[13px] text-stone-600 hover:bg-stone-100 transition-colors">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3 text-stone-500"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
                                            Devoluciones
                                        </a>
                                    </div>
                                </div>
                            @endrole
                        @endauth
                    </div>
                </div>

                <!-- CENTRO: Buscador Integrado -->
                <div class="hidden lg:flex flex-1 items-center justify-center max-w-sm mx-auto">
                    <div class="w-full relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-stone-400 group-focus-within:text-stone-600 transition-colors"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        </div>
                        <input type="text" 
                               class="w-full bg-stone-200/30 border border-transparent rounded-full py-2 pl-10 pr-4 text-[13px] focus:bg-stone-50 focus:border-stone-300 focus:ring-0 transition-all placeholder:text-stone-400" 
                               placeholder="Buscar en el acervo... (/)">
                    </div>
                </div>

                <!-- DERECHA: Sesión -->
                <div class="flex items-center gap-6">
                    @guest
                        <a href="{{ route('login') }}" class="text-[13px] font-semibold text-stone-600 hover:text-stone-900 transition-colors">Entrar</a>
                        <a href="{{ route('register') }}" class="bg-stone-800 text-stone-50 text-[12px] font-bold px-6 py-2.5 rounded-full hover:bg-stone-950 transition-all shadow-sm">Registrarse</a>
                    @else
                        <!-- Alertas de Multa -->
                        <button class="relative p-2 text-stone-400 hover:text-stone-800 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                            @if(Auth::user()->total_debt > 0)
                                <span class="absolute top-2 right-2 h-2 w-2 bg-rose-500 rounded-full ring-2 ring-[#f1f2ee]"></span>
                            @endif
                        </button>

                        <!-- Usuario -->
                        <div class="relative" x-data="{ userMenu: false }">
                            <button @click="userMenu = !userMenu" class="flex items-center gap-3 group">
                                <div class="h-9 w-9 bg-stone-200 border border-stone-300 rounded-full flex items-center justify-center text-[11px] font-bold text-stone-700 transition-all group-hover:border-stone-400">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                            </button>
                            <div x-show="userMenu" @click.away="userMenu = false" x-cloak 
                                 class="absolute right-0 mt-4 w-56 bg-stone-50 border border-stone-200 rounded-2xl shadow-xl py-2 z-50">
                                <div class="px-5 py-3 border-b border-stone-200/50 mb-1">
                                    <p class="text-xs font-bold text-stone-800 truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-[10px] text-stone-400 font-mono mt-0.5 tracking-tight uppercase">{{ Auth::user()->institutional_code }}</p>
                                </div>
                                <a href="/perfil" class="flex items-center px-5 py-2.5 text-[13px] text-stone-600 hover:bg-stone-100 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3 opacity-60"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    Mi Perfil
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center px-5 py-2.5 text-[13px] text-rose-700 font-medium hover:bg-rose-50 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3 opacity-70"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/></svg>
                                        Cerrar Sesión
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <!-- CONTENT -->
    <main class="max-w-7xl mx-auto px-6 lg:px-10 py-12 min-h-[75vh]">
        {{ $slot ?? '' }} 
        @yield('content')
    </main>

    <!-- FOOTER: Consistente con el tono de calma -->
    <footer class="bg-stone-200/40 border-t border-stone-200 py-16">
        <div class="max-w-7xl mx-auto px-6 lg:px-10">
            <div class="flex flex-col md:flex-row justify-between items-center gap-10">
                <div class="flex items-center gap-6">
                    <img src="{{ asset('logobilbio.jpg') }}" alt="UNT" class="h-10 opacity-50 grayscale">
                    <div class="text-left border-l border-stone-300 pl-6">
                        <p class="text-[12px] font-bold text-stone-700 tracking-tight">Escuela de Informática</p>
                        <p class="text-[10px] text-stone-500 font-medium tracking-wide uppercase">Universidad Nacional de Trujillo</p>
                    </div>
                </div>
                <div class="text-center md:text-right">
                    <p class="text-[10px] text-stone-400 font-bold uppercase tracking-[0.25em]">© {{ date('Y') }} Excelencia en Gestión Bibliotecaria</p>
                    <p class="text-[9px] text-stone-400 mt-2">Facultad de Ciencias Físicas y Matemáticas</p>
                </div>
            </div>
        </div>
    </footer>

    @livewireScripts
    
    <!-- NOTIFICACIONES (Estilo Minimalista) -->
    <div id="toast-wrapper" class="fixed bottom-10 right-10 space-y-4 z-[100]"></div>

    <script>
        window.addEventListener('notify', event => {
            const wrapper = document.getElementById('toast-wrapper');
            const toast = document.createElement('div');
            toast.className = "flex items-center bg-stone-800 text-stone-50 px-6 py-4 rounded-2xl shadow-2xl transition-all duration-700 transform translate-y-10 opacity-0 border border-white/5";
            toast.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-stone-400 mr-4"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                <span class="text-[13px] font-medium tracking-tight">${event.detail}</span>
            `;
            wrapper.appendChild(toast);
            setTimeout(() => { toast.classList.remove('translate-y-10', 'opacity-0'); }, 50);
            setTimeout(() => {
                toast.classList.add('opacity-0', 'translate-y-2');
                setTimeout(() => toast.remove(), 700);
            }, 4500);
        });

        window.addEventListener('keydown', e => {
            if (e.key === '/' && !['INPUT', 'TEXTAREA'].includes(document.activeElement.tagName)) {
                e.preventDefault();
                document.querySelector('input[type="text"]').focus();
            }
        });
    </script>
</body>
</html>