<div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Mis Libros</h1>
            <p class="mt-1 text-gray-500 dark:text-gray-400">Historial de tus lecturas y devoluciones.</p>
        </div>

        <!-- Mensajes Flash -->
        @if (session()->has('message'))
            <div class="mb-6 p-4 rounded-lg bg-blue-100 text-blue-800 border border-blue-200 shadow-sm">
                {{ session('message') }}
            </div>
        @endif

        <!-- Navegación de Filtros -->
        <div class="flex space-x-1 bg-gray-200 dark:bg-gray-800 p-1 rounded-xl mb-6 w-full md:w-auto inline-flex">
            @foreach(['active' => '📖 Leyendo Ahora', 'reserved' => '⏳ Reservas', 'history' => '📚 Historial'] as $key => $label)
                <button wire:click="$set('filter', '{{ $key }}')"
                        class="px-4 py-2 text-sm font-medium rounded-lg transition-all {{ $filter === $key ? 'bg-white dark:bg-gray-700 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <!-- Lista de Préstamos -->
        <div class="space-y-4">
            @forelse($loans as $loan)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 p-4 sm:p-6 transition hover:shadow-md flex flex-col sm:flex-row gap-6">
                    
                    <!-- Portada Pequeña -->
                    <div class="flex-shrink-0 mx-auto sm:mx-0">
                        <img src="{{ asset('storage/' . $loan->book->image_path) }}" 
                             class="w-24 h-32 object-cover rounded shadow-sm"
                             onerror="this.src='[https://ui-avatars.com/api/?name=](https://ui-avatars.com/api/?name=){{ urlencode($loan->book->title) }}&background=random'">
                    </div>

                    <!-- Información -->
                    <div class="flex-1 text-center sm:text-left">
                        <div class="flex flex-col sm:flex-row justify-between items-start">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $loan->book->title }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $loan->book->author }}</p>
                            </div>
                            
                            <!-- Badge de Estado -->
                            <div class="mt-2 sm:mt-0">
                                @if($loan->status === 'active')
                                    @php
                                        $daysLeft = now()->diffInDays($loan->due_date, false);
                                    @endphp
                                    @if($daysLeft < 0)
                                        <span class="bg-red-100 text-red-800 text-xs font-bold px-3 py-1 rounded-full border border-red-200">
                                            ⚠️ Vencido hace {{ abs(intval($daysLeft)) }} días
                                        </span>
                                    @else
                                        <span class="bg-green-100 text-green-800 text-xs font-bold px-3 py-1 rounded-full border border-green-200">
                                            Quedan {{ intval($daysLeft) }} días
                                        </span>
                                    @endif
                                @elseif($loan->status === 'reserved')
                                    <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-3 py-1 rounded-full border border-yellow-200">
                                        Pendiente de recoger
                                    </span>
                                @elseif($loan->status === 'returned')
                                    <span class="bg-gray-100 text-gray-600 text-xs font-bold px-3 py-1 rounded-full border border-gray-200">
                                        Devuelto
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Fechas Importantes -->
                        <div class="mt-4 grid grid-cols-2 gap-4 text-sm bg-gray-50 dark:bg-gray-700/30 p-3 rounded-lg">
                            @if($loan->status === 'reserved')
                                <div>
                                    <span class="block text-gray-400 text-xs">Fecha Solicitud</span>
                                    <span class="font-medium dark:text-gray-200">{{ $loan->request_date->format('d/m/Y') }}</span>
                                </div>
                                <div class="text-right sm:text-left">
                                    <span class="block text-gray-400 text-xs">Expira reserva</span>
                                    <span class="font-medium dark:text-gray-200">24 horas</span>
                                </div>
                            @elseif($loan->status === 'active')
                                <div>
                                    <span class="block text-gray-400 text-xs">Fecha Préstamo</span>
                                    <span class="font-medium dark:text-gray-200">{{ $loan->pickup_date->format('d/m/Y') }}</span>
                                </div>
                                <div class="text-right sm:text-left">
                                    <span class="block text-gray-400 text-xs">Fecha Devolución</span>
                                    <span class="font-medium {{ now() > $loan->due_date ? 'text-red-600 font-bold' : 'text-indigo-600' }}">
                                        {{ $loan->due_date->format('d/m/Y') }}
                                    </span>
                                </div>
                            @else
                                <div>
                                    <span class="block text-gray-400 text-xs">Devuelto el</span>
                                    <span class="font-medium dark:text-gray-200">{{ $loan->return_date ? $loan->return_date->format('d/m/Y') : '-' }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Alerta de Multa -->
                        @if($loan->penalty)
                            <div class="mt-3 p-3 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800 rounded-lg flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span class="text-sm text-red-700 dark:text-red-400 font-medium">Multa generada: ${{ $loan->penalty->amount }}</span>
                                </div>
                                @if($loan->penalty->is_paid)
                                    <span class="text-xs font-bold text-green-600 bg-green-100 px-2 py-1 rounded uppercase">Pagada</span>
                                @else
                                    <span class="text-xs font-bold text-red-600 bg-white px-2 py-1 rounded uppercase border border-red-200">Pendiente de Pago</span>
                                @endif
                            </div>
                        @endif

                        <!-- Botón Cancelar (Solo para reservas) -->
                        @if($loan->status === 'reserved')
                            <div class="mt-4 flex justify-end">
                                <button wire:click="cancelReservation({{ $loan->id }})" 
                                        wire:confirm="¿Ya no quieres este libro?"
                                        class="text-sm text-red-500 hover:text-red-700 underline decoration-dotted">
                                    Cancelar reserva
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <div class="bg-gray-100 dark:bg-gray-800 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">No hay libros aquí</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">Parece que no tienes actividad en esta sección.</p>
                    <a href="{{ route('student.catalog') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Ir al Catálogo
                    </a>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $loans->links() }}
        </div>
    </div>
</div>
