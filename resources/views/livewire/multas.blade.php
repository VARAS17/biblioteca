<div class="p-6">

    <!-- RESUMEN -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">

        <!-- Total pendiente -->
        <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-red-500">
            <p class="text-xs font-bold text-gray-500 uppercase">
                Total Pendiente
            </p>

            <p class="text-2xl font-black text-red-600">
                S/ {{ number_format($total_pending, 2) }}
            </p>
        </div>


        <!-- Total recaudado -->
        <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-green-500">
            
            <p class="text-xs font-bold text-gray-500 uppercase">
                Total Recaudado (Caja)
            </p>

            <p class="text-2xl font-black text-green-600">
                S/ {{ number_format($total_collected, 2) }}
            </p>

        </div>


        <!-- Usuarios deuda -->
        <div class="bg-white p-6 rounded-xl shadow-sm border-l-4 border-blue-900">

            <p class="text-xs font-bold text-gray-500 uppercase">
                Usuarios con Deuda
            </p>

            <p class="text-2xl font-black text-blue-900">
                {{ \App\Models\User::where('total_debt', '>', 0)->count() }}
            </p>

        </div>

    </div>





    <div class="bg-white rounded-xl shadow-lg overflow-hidden">


        <!-- FILTROS -->
        <div class="p-4 bg-gray-50 border-b md:flex md:justify-between md:items-center space-y-4 md:space-y-0">


            <div class="flex bg-white border rounded-lg p-1">


                <button 
                    wire:click="$set('filter_status','pending')"

                    class="px-4 py-1.5 text-xs font-bold rounded-md transition
                    {{ $filter_status == 'pending' 
                        ? 'bg-red-600 text-white shadow' 
                        : 'text-gray-500 hover:bg-gray-100' }}">

                    PENDIENTES

                </button>



                <button 
                    wire:click="$set('filter_status','paid')"

                    class="px-4 py-1.5 text-xs font-bold rounded-md transition
                    {{ $filter_status == 'paid' 
                        ? 'bg-green-600 text-white shadow' 
                        : 'text-gray-500 hover:bg-gray-100' }}">

                    PAGADAS

                </button>


            </div>




            <div class="relative w-full md:w-64">


                <input 
                    wire:model.live="search"

                    type="text"

                    placeholder="Buscar usuario o código..."

                    class="w-full pl-10 pr-4 py-2 border-gray-300 rounded-lg text-sm focus:ring-blue-900">



                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">

                    <svg 
                    class="h-4 w-4 text-gray-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                    <path 
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                    stroke-width="2"
                    stroke-linecap="round"
                    stroke-linejoin="round"/>

                    </svg>

                </div>

            </div>


        </div>






        <!-- ============================= -->
        <!-- MULTAS EN CURSO -->
        <!-- ============================= -->

        @if($filter_status == 'pending')

            @if(isset($overdueLoans) && $overdueLoans->count() > 0)


            <div class="bg-orange-50 border border-orange-200 rounded-xl overflow-hidden mb-6 mx-6 mt-6">


                <div class="px-6 py-3 bg-orange-100 border-b border-orange-200">


                    <p class="text-xs font-black text-orange-800 uppercase tracking-widest">

                        ⚠ Multas en curso (libro aún no devuelto)

                    </p>


                </div>




                <table class="min-w-full divide-y divide-orange-100">


                    <thead class="uppercase text-[10px] font-bold text-orange-600">


                        <tr>

                            <th class="px-6 py-3 text-left">
                                Usuario
                            </th>

                            <th class="px-6 py-3 text-left">
                                Libro
                            </th>

                            <th class="px-6 py-3 text-left">
                                Días vencido
                            </th>

                            <th class="px-6 py-3 text-left">
                                Multa acumulada
                            </th>

                        </tr>


                    </thead>




                    <tbody class="divide-y divide-orange-50">


                    @foreach($overdueLoans as $loan)


                        <tr class="hover:bg-orange-50">


                            <td class="px-6 py-4">


                                <div class="text-sm font-bold text-gray-800">

                                    {{ $loan->user->name }}

                                </div>


                                <div class="text-[10px] text-blue-700 font-mono">

                                    {{ $loan->user->institutional_code }}

                                </div>


                            </td>




                            <td class="px-6 py-4 text-xs text-gray-600">

                                {{ $loan->copy->book->title }}

                            </td>





                            <td class="px-6 py-4 text-sm font-bold text-orange-600">

                                {{ $loan->days_late }} d

                            </td>





                            <td class="px-6 py-4 text-sm font-black text-red-600">


                                S/ {{ number_format($loan->accrued_fine,2) }}


                                <span class="text-[10px] font-normal text-gray-400 block">

                                    Se registrará al devolver

                                </span>


                            </td>


                        </tr>


                    @endforeach


                    </tbody>


                </table>


            </div>


            @endif

        @endif









        <!-- TABLA PRINCIPAL -->


        <table class="min-w-full divide-y divide-gray-200">


            <thead class="bg-gray-100 uppercase text-[10px] font-bold text-gray-500">


                <tr>

                    <th class="px-6 py-3 text-left">
                        Usuario UNT
                    </th>


                    <th class="px-6 py-3 text-left">
                        Libro Retrasado
                    </th>


                    <th class="px-6 py-3 text-left">
                        Días
                    </th>


                    <th class="px-6 py-3 text-left">
                        Monto
                    </th>


                    <th class="px-6 py-3 text-right">
                        Acción
                    </th>


                </tr>


            </thead>





            <tbody class="divide-y divide-gray-100">


            @forelse($fines as $fine)



                <tr class="hover:bg-gray-50 transition">


                    <td class="px-6 py-4">


                        <div class="text-sm font-bold text-gray-800">

                            {{ $fine->user->name }}

                        </div>


                        <div class="text-[10px] text-blue-700 font-mono">

                            {{ $fine->user->institutional_code }}

                        </div>


                    </td>





                    <td class="px-6 py-4">


                        <div class="text-xs text-gray-600">

                            {{ $fine->loan->copy->book->title }}

                        </div>


                        <div class="text-[10px] text-gray-400">

                            Préstamo #{{ $fine->loan->id }}

                        </div>


                    </td>





                    <td class="px-6 py-4 text-sm font-bold text-gray-500">

                        {{ $fine->days_late }} d

                    </td>





                    <td class="px-6 py-4">


                        <span class="text-sm font-black 
                        {{ $fine->status == 'pending' 
                            ? 'text-red-600' 
                            : 'text-green-600' }}">


                            S/ {{ number_format($fine->amount,2) }}


                        </span>


                    </td>





                    <td class="px-6 py-4 text-right">


                    @if($fine->status == 'pending')


                        <button

                            wire:click="payFine({{ $fine->id }})"

                            wire:confirm="¿Confirmas que has recibido el pago de S/ {{ $fine->amount }}?"

                            class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded text-xs font-bold shadow-sm">


                            REGISTRAR PAGO


                        </button>


                    @else


                        <div class="text-[10px] text-gray-400 uppercase italic">

                            Pagado el {{ $fine->paid_at->format('d/m/Y') }}

                        </div>


                    @endif


                    </td>



                </tr>



            @empty


                <tr>

                    <td colspan="5" class="px-6 py-10 text-center text-gray-400 italic">

                        No hay registros para mostrar.

                    </td>

                </tr>


            @endforelse


            </tbody>


        </table>





        <div class="p-4 border-t bg-gray-50">

            {{ $fines->links() }}

        </div>


    </div>


</div>