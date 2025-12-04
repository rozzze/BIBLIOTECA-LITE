<div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Control de Préstamos</h1>
            <p class="mt-1 text-gray-500 dark:text-gray-400">Administra las entregas y devoluciones de la biblioteca.</p>
        </div>

        <!-- Mensajes Flash -->
        @if (session()->has('message'))
            <div class="mb-4 p-4 rounded-lg bg-green-100 text-green-700 border border-green-200">
                {{ session('message') }}
            </div>
        @endif
        @if (session()->has('warning'))
            <div class="mb-4 p-4 rounded-lg bg-yellow-100 text-yellow-800 border border-yellow-200 font-bold">
                ⚠️ {{ session('warning') }}
            </div>
        @endif

        <!-- Card Principal -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden">
            
            <!-- Tabs de Filtros -->
            <div class="flex border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-900/50 p-1">
                @foreach(['all' => 'Todos', 'reserved' => 'Reservas Pendientes', 'active' => 'En Préstamo', 'returned' => 'Historial'] as $key => $label)
                    <button wire:click="$set('filter', '{{ $key }}')"
                            class="flex-1 py-2 text-sm font-medium rounded-lg transition-all {{ $filter === $key ? 'bg-white dark:bg-gray-700 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            <!-- Tabla -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Libro</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Estudiante</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Fechas</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Estado</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                        @forelse($loans as $loan)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $loan->book->title }}</div>
                                    <div class="text-xs text-gray-500">{{ $loan->book->isbn ?? 'S/N' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $loan->user->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $loan->user->email }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-300">
                                    @if($loan->status === 'reserved')
                                        Solicitado: {{ $loan->request_date->format('d/m H:i') }}
                                    @elseif($loan->status === 'active')
                                        <div class="text-indigo-600">Entrega: {{ $loan->pickup_date->format('d/m') }}</div>
                                        <div class="font-bold {{ now() > $loan->due_date ? 'text-red-500' : 'text-green-600' }}">
                                            Vence: {{ $loan->due_date->format('d/m') }}
                                        </div>
                                    @else
                                        Devuelto: {{ $loan->return_date ? $loan->return_date->format('d/m') : '-' }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $colors = [
                                            'reserved' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                            'active' => 'bg-green-100 text-green-800 border-green-200',
                                            'returned' => 'bg-gray-100 text-gray-800 border-gray-200',
                                            'cancelled' => 'bg-red-100 text-red-800 border-red-200',
                                        ];
                                        $labels = [
                                            'reserved' => 'Reserva',
                                            'active' => 'Activo',
                                            'returned' => 'Finalizado',
                                            'cancelled' => 'Cancelado',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full border {{ $colors[$loan->status] ?? '' }}">
                                        {{ $labels[$loan->status] ?? $loan->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if($loan->status === 'reserved')
                                        <button wire:click="approve({{ $loan->id }})" class="text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-1 rounded-md text-xs mr-2 transition">
                                            Entregar Libro
                                        </button>
                                        <button wire:click="cancel({{ $loan->id }})" wire:confirm="¿Cancelar esta reserva?" class="text-red-600 hover:text-red-900 text-xs">
                                            Cancelar
                                        </button>
                                    @elseif($loan->status === 'active')
                                        <button wire:click="conclude({{ $loan->id }})" wire:confirm="¿Confirmar devolución del libro?" class="text-white bg-green-600 hover:bg-green-700 px-3 py-1 rounded-md text-xs transition shadow-sm">
                                            Recibir Devolución
                                        </button>
                                    @else
                                        <span class="text-gray-400 text-xs">Sin acciones</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    No hay préstamos en esta categoría.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $loans->links() }}
            </div>
        </div>
    </div>
</div>