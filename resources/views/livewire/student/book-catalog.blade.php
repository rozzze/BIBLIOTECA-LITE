<div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
    
    <!-- Notificaciones Flotantes (Usando AlpineJS para escuchar eventos de Livewire) -->
    <div x-data="{ show: false, message: '', type: '' }" 
         x-on:notify-success.window="show = true; message = $event.detail[0]; type = 'success'; setTimeout(() => show = false, 3000)"
         x-on:notify-error.window="show = true; message = $event.detail[0]; type = 'error'; setTimeout(() => show = false, 3000)"
         x-show="show" 
         x-transition
         class="fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-xl text-white font-bold transform transition-all duration-300"
         :class="type === 'success' ? 'bg-green-500' : 'bg-red-500'">
        <span x-text="message"></span>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header y Buscador -->
        <div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Catálogo de Libros</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Explora y reserva tu próxima lectura.</p>
            </div>
            
            <div class="w-full md:w-1/3 relative">
                <input wire:model.live.debounce.300ms="search" 
                       type="text" 
                       placeholder="Buscar por título o autor..." 
                       class="w-full rounded-full border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-white pl-10 pr-4 py-2 focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
                <svg class="w-5 h-5 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>

        <!-- Grid de Libros -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse($books as $book)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden group flex flex-col h-full border border-gray-100 dark:border-gray-700">
                    
                    <!-- Portada -->
                    <div class="relative h-64 overflow-hidden bg-gray-200">
                        <img src="{{ asset('storage/' . $book->image_path) }}" 
                             alt="{{ $book->title }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                             onerror="this.src='[https://ui-avatars.com/api/?name=](https://ui-avatars.com/api/?name=){{ urlencode($book->title) }}&background=random&size=400'">
                        
                        <!-- Badge de Categoría -->
                        <span class="absolute top-2 right-2 bg-black/50 backdrop-blur-sm text-white text-xs px-2 py-1 rounded-md">
                            {{ $book->category->name }}
                        </span>

                        <!-- Overlay si está agotado -->
                        @if(!$book->is_available)
                            <div class="absolute inset-0 bg-black/60 flex items-center justify-center">
                                <span class="text-white font-bold border-2 border-white px-4 py-2 transform -rotate-12 rounded">AGOTADO</span>
                            </div>
                        @endif
                    </div>

                    <!-- Info -->
                    <div class="p-5 flex-1 flex flex-col">
                        <h3 class="font-bold text-gray-900 dark:text-white text-lg leading-tight mb-1 line-clamp-2">{{ $book->title }}</h3>
                        <p class="text-sm text-indigo-600 dark:text-indigo-400 font-medium mb-2">{{ $book->author }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-3 mb-4 flex-1">
                            {{ $book->synopsis ?? 'Sin sinopsis disponible.' }}
                        </p>

                        <!-- Botón de Acción -->
                        <div class="mt-auto pt-4 border-t border-gray-100 dark:border-gray-700">
                            @if($book->is_available)
                                <button wire:click="reserve({{ $book->id }})" 
                                        wire:loading.attr="disabled"
                                        class="w-full bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-bold py-2 px-4 rounded-lg hover:bg-gray-700 dark:hover:bg-gray-200 transition-colors flex justify-center items-center gap-2">
                                    <span wire:loading.remove wire:target="reserve({{ $book->id }})">Reservar</span>
                                    <span wire:loading wire:target="reserve({{ $book->id }})">Procesando...</span>
                                </button>
                            @else
                                <button disabled class="w-full bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500 font-bold py-2 px-4 rounded-lg cursor-not-allowed">
                                    No Disponible
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    <p class="text-xl font-medium">No encontramos libros con esa búsqueda.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $books->links() }}
        </div>
    </div>
</div>
