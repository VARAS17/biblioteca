@if($bloqueado ?? false)

    <div class="p-6">
        <div class="max-w-5xl mx-auto">

            <div class="p-6 bg-red-50 border border-red-200 rounded-2xl text-center shadow-sm">

                <div class="flex justify-center mb-4">
                    <div class="h-16 w-16 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path 
                                stroke-linecap="round" 
                                stroke-linejoin="round" 
                                stroke-width="2"
                                d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                    </div>
                </div>


                <p class="font-black text-red-700 uppercase tracking-widest text-sm">
                    Acceso Bloqueado
                </p>


                <p class="text-red-600 text-xs mt-3">
                    Tienes una deuda pendiente de 
                    <strong>
                        S/ {{ number_format($deuda, 2) }}
                    </strong>.
                </p>


                <p class="text-red-500 text-xs mt-2">
                    Regulariza tu situación en biblioteca para poder realizar reservas.
                </p>

            </div>

        </div>
    </div>


@else


<div class="p-6">
    <div class="max-w-5xl mx-auto">
        
        <!-- ENCABEZADO -->
        <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">

            <div>
                <h2 class="text-3xl font-black text-blue-900 uppercase">
                    Gestión de Reservas
                </h2>

                <p class="text-gray-500 text-sm italic">
                    Apartado formal de material bibliográfico (Exclusivo Docentes)
                </p>
            </div>


            <div class="flex bg-gray-200 p-1 rounded-xl">

                <button 
                    wire:click="$set('tab', 'pendientes')"
                    class="px-6 py-2 text-[10px] font-bold rounded-lg transition 
                    {{ $tab === 'pendientes' ? 'bg-white text-blue-900 shadow-sm' : 'text-gray-500' }}">

                    SOLICITUDES ACTIVAS

                </button>


                <button 
                    wire:click="$set('tab', 'completadas')"
                    class="px-6 py-2 text-[10px] font-bold rounded-lg transition 
                    {{ $tab === 'completadas' ? 'bg-white text-blue-900 shadow-sm' : 'text-gray-500' }}">

                    HISTORIAL

                </button>

            </div>

        </div>



        <!-- LISTADO DE RESERVAS -->

        <div class="grid grid-cols-1 gap-4">


        @forelse($reservations as $res)


            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col md:flex-row justify-between items-center hover:border-blue-300 transition">


                <div class="flex items-center space-x-4 flex-1">


                    <div class="h-12 w-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">

                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path 
                            d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"/>

                        </svg>

                    </div>



                    <div>

                        <span class="text-[9px] font-bold text-blue-600 uppercase tracking-widest">

                            {{ $res->book->category->name }}

                        </span>


                        <h4 class="text-lg font-bold text-gray-800">

                            {{ $res->book->title }}

                        </h4>


                        <p class="text-xs text-gray-400">

                            Solicitado el:
                            {{ $res->reservation_date->format('d/m/Y H:i') }}

                        </p>


                    </div>


                </div>





                <div class="flex items-center space-x-6 mt-4 md:mt-0">


                    <div class="text-center">

                        <p class="text-[9px] font-bold text-gray-400 uppercase mb-1">
                            Estado
                        </p>



                        @if($res->status === 'pending')

                            <span class="px-3 py-1 rounded-full bg-orange-100 text-orange-700 text-[10px] font-bold uppercase">

                                Esperando Devolución

                            </span>



                        @elseif($res->status === 'completed')


                            <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-[10px] font-bold uppercase">

                                Completada

                            </span>


                        @else


                            <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-500 text-[10px] font-bold uppercase">

                                {{ $res->status }}

                            </span>


                        @endif


                    </div>





                    @if($res->status === 'pending')


                    <button

                        wire:click="cancelReservation({{ $res->id }})"

                        wire:confirm="¿Deseas anular esta solicitud de reserva?"

                        class="bg-red-50 text-red-600 p-2 rounded-lg hover:bg-red-100 transition shadow-sm">


                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path 
                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"/>

                        </svg>

                    </button>


                    @endif


                </div>


            </div>



        @empty


            <div class="text-center py-20 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200">

                <p class="text-gray-400 font-medium">
                    No tienes reservas en esta sección.
                </p>


                <a href="/catalogo"
                class="text-blue-600 text-xs font-bold mt-2 inline-block hover:underline">

                    Ir al catálogo a reservar

                </a>

            </div>


        @endforelse


        </div>




        <div class="mt-6">

            {{ $reservations->links() }}

        </div>





        <!-- INFO -->

        <div class="mt-12 p-6 bg-blue-900 rounded-3xl text-white">

            <div class="flex items-start">


                <div class="p-3 bg-blue-800 rounded-2xl mr-4 text-blue-300">


                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                    <path
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"/>

                    </svg>


                </div>



                <div>


                    <h4 class="font-bold mb-1">
                        Información sobre Reservas
                    </h4>


                    <p class="text-xs text-blue-200 leading-relaxed">

                        Recuerda que según la 
                        <strong>Regla de Negocio RN-11</strong>,
                        las reservas son un beneficio exclusivo para docentes.

                    </p>


                </div>


            </div>

        </div>



    </div>
</div>


@endif