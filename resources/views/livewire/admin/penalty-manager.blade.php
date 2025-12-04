<div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="flex justify-between items-end mb-8">
            <div>
                <h1 class="text-3xl font-bold text-red-600 dark:text-red-500">Multas Pendientes</h1>
                <p class="mt-1 text-gray-500 dark:text-gray-400">Gestiona los pagos y desbloquea estudiantes.</p>
            </div>
            
            <!-- Buscador de morosos -->
            <div class="w-1/3">
                <input wire:model.live="search" type="text" placeholder="Buscar estudiante..." 
                       class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white px-4 py-2">
            </div>
        </div>

        <!-- Mensajes -->
        @if (session()->has('message'))
            <div class="mb-6 p-4 rounded-lg bg-green-100 text-green-800 border border-green-200 shadow-sm flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                {{ session('message') }}
            </div>
        @endif
        @if (session()->has('warning'))
            <div class="mb-6 p-4 rounded-lg bg-orange-100 text-orange-800 border border-orange-200 shadow-sm flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                {{ session('warning') }}
            </div>
        @endif

        <!-- Grid de Tarjetas de Multas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($penalties as $penalty)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md border-l-4 border-red-500 overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="font-bold text-lg text-gray-900 dark:text-white">{{ $penalty->user->name }}</h3>
                                <p class="text-xs text-gray-500">{{ $penalty->user->email }}</p>
                            </div>
                            <span class="bg-red-100 text-red-800 text-xs font-bold px-2 py-1 rounded uppercase tracking-wide">
                                Pendiente
                            </span>
                        </div>

                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Monto:</span>
                                <span class="font-bold text-gray-900 dark:text-white text-lg">${{ number_format($penalty->amount, 2) }}</span>
                            </div>
                            <div class="text-sm">
                                <span class="text-gray-500 block mb-1">Motivo:</span>
                                <p class="text-gray-700 dark:text-gray-300 italic bg-gray-50 dark:bg-gray-700/50 p-2 rounded text-xs">
                                    "{{ $penalty->reason }}"
                                </p>
                            </div>
                            <div class="text-xs text-gray-400">
                                Libro: {{ $penalty->loan->book->title }} <br>
                                Fecha multa: {{ $penalty->created_at->format('d/m/Y') }}
                            </div>
                        </div>

                        <button wire:click="markAsPaid({{ $penalty->id }})" 
                                wire:confirm="¿Confirmas que el estudiante pagó ${{ $penalty->amount }}?"
                                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg transition-colors flex justify-center items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Registrar Pago
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white dark:bg-gray-800 rounded-xl p-12 text-center shadow-sm">
                    <div class="mx-auto h-16 w-16 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mb-4">
                        <svg class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">¡Todo en orden!</h3>
                    <p class="text-gray-500 dark:text-gray-400">No hay multas pendientes por cobrar.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $penalties->links() }}
        </div>
    </div>
</div>