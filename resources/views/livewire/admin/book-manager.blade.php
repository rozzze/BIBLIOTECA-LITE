<div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen">
    
    <!-- Header para el Layout de Breeze (Opcional pero recomendado) -->
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Gestión de Libros') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Encabezado de la Sección -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                    Biblioteca
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Administra el catálogo, existencias y portadas de los libros.
                </p>
            </div>

            <!-- Mensaje Flash Flotante -->
            @if (session()->has('message'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                     class="flex items-center gap-3 px-4 py-3 text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg shadow-sm dark:bg-green-900/30 dark:border-green-800 dark:text-green-400 animate-fade-in-down">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ session('message') }}
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- COLUMNA IZQUIERDA: FORMULARIO (4 columnas de ancho) -->
            <div class="lg:col-span-4">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden sticky top-6">
                    <!-- Título de la Card -->
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex justify-between items-center">
                        <h2 class="text-base font-bold text-gray-800 dark:text-gray-100">
                            {{ $isEditing ? '✏️ Editar Libro' : '➕ Nuevo Libro' }}
                        </h2>
                        @if($isEditing)
                            <span class="text-xs font-mono text-indigo-500 bg-indigo-50 dark:bg-indigo-900/30 px-2 py-1 rounded">ID: {{ $bookIdToEdit }}</span>
                        @endif
                    </div>
                    
                    <div class="p-6">
                        <form wire:submit.prevent="save" class="space-y-5">
                            
                            <!-- Título -->
                            <div class="space-y-1">
                                <label for="title" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Título del Libro</label>
                                <input wire:model="title" type="text" id="title" 
                                       class="w-full rounded-lg border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white dark:focus:border-indigo-500 transition-all placeholder-gray-400" 
                                       placeholder="Ej. Cien Años de Soledad">
                                @error('title') <span class="text-xs text-red-500 font-medium flex items-center gap-1 mt-1">⚠️ {{ $message }}</span> @enderror
                            </div>

                            <!-- Grid Autor e ISBN -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label for="author" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Autor</label>
                                    <input wire:model="author" type="text" id="author" 
                                           class="w-full rounded-lg border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white transition-all placeholder-gray-400" 
                                           placeholder="G.G. Márquez">
                                    @error('author') <span class="text-xs text-red-500 font-medium block mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div class="space-y-1">
                                    <label for="isbn" class="text-sm font-semibold text-gray-700 dark:text-gray-300">ISBN <span class="text-gray-400 font-normal text-xs">(Opcional)</span></label>
                                    <input wire:model="isbn" type="text" id="isbn" 
                                           class="w-full rounded-lg border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white transition-all placeholder-gray-400"
                                           placeholder="978-...">
                                </div>
                            </div>

                            <!-- Categoría y Editorial -->
                            <div class="space-y-4">
                                <div class="space-y-1">
                                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Categoría</label>
                                    <select wire:model="category_id" class="w-full rounded-lg border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white cursor-pointer">
                                        <option value="">Selecciona una categoría...</option>
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id') <span class="text-xs text-red-500 font-medium block mt-1">{{ $message }}</span> @enderror
                                </div>

                                <div class="space-y-1">
                                    <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Editorial</label>
                                    <select wire:model="publisher_id" class="w-full rounded-lg border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white cursor-pointer">
                                        <option value="">Selecciona una editorial...</option>
                                        @foreach($publishers as $pub)
                                            <option value="{{ $pub->id }}">{{ $pub->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('publisher_id') <span class="text-xs text-red-500 font-medium block mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Stock -->
                            <div class="space-y-1">
                                <label for="stock" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Stock Total</label>
                                <div class="relative">
                                    <input wire:model="stock_total" type="number" min="1" id="stock" 
                                           class="w-full rounded-lg border-gray-300 bg-white pl-4 pr-12 py-2.5 text-sm text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 dark:border-gray-600 dark:bg-gray-900 dark:text-white transition-all" >
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 text-xs">Uds.</span>
                                    </div>
                                </div>
                                @error('stock_total') <span class="text-xs text-red-500 font-medium block mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Imagen Upload Mejorada -->
                            <div class="space-y-2">
                                <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">Portada del Libro</label>
                                
                                <div x-data="{ isHovering: false }" 
                                     @dragover.prevent="isHovering = true" 
                                     @dragleave.prevent="isHovering = false"
                                     @drop="isHovering = false"
                                     class="relative group cursor-pointer">
                                    
                                    <div class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-xl transition-all duration-200"
                                         :class="isHovering ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800'">
                                        
                                        @if($image)
                                            <div class="flex items-center space-x-2 text-indigo-600">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                <span class="text-sm font-medium">Imagen cargada</span>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1">Click para cambiar</p>
                                        @else
                                            <svg class="w-8 h-8 text-gray-400 group-hover:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <p class="text-xs text-gray-500 mt-2 font-medium group-hover:text-indigo-600">
                                                Arrastra o haz click aquí
                                            </p>
                                        @endif
                                        
                                        <input wire:model="image" type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                    </div>
                                </div>
                                <div wire:loading wire:target="image" class="text-xs text-indigo-600 font-medium flex items-center gap-1">
                                    <svg class="animate-spin h-3 w-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Procesando imagen...
                                </div>
                                @error('image') <span class="text-xs text-red-500 font-medium">{{ $message }}</span> @enderror
                            </div>

                            <!-- Botones -->
                            <div class="pt-2 flex gap-3">
                                <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold py-3 px-4 rounded-xl shadow-md shadow-indigo-200 dark:shadow-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 transition-all transform hover:scale-[1.02]">
                                    {{ $isEditing ? 'Actualizar Libro' : 'Guardar Libro' }}
                                </button>
                                
                                @if($isEditing)
                                    <button type="button" wire:click="resetInput" class="flex-none bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-sm font-bold py-3 px-4 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-all">
                                        ❌
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA: TABLA (8 columnas de ancho) -->
            <div class="lg:col-span-8">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col h-full">
                    
                    <!-- Header Tabla -->
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-800/50">
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Inventario Actual</h3>
                            <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-700">
                                {{ $books->total() }}
                            </span>
                        </div>
                        
                        <!-- Buscador Simple (Decorativo por ahora) -->
                        <div class="relative hidden sm:block">
                            <input type="text" placeholder="Buscar libro..." class="w-48 pl-8 pr-3 py-1.5 text-xs rounded-full border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 focus:ring-indigo-500 focus:border-indigo-500">
                            <svg class="w-3.5 h-3.5 text-gray-400 absolute left-3 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>

                    <!-- Tabla -->
                    <div class="overflow-x-auto flex-1">
                        <table class="min-w-full divide-y divide-gray-100 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Portada</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Información</th>
                                    <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                                @forelse($books as $book)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                                        <td class="px-6 py-4 whitespace-nowrap w-24">
                                            <div class="h-20 w-14 rounded-lg bg-gray-200 dark:bg-gray-700 overflow-hidden shadow-sm border border-gray-200 dark:border-gray-600 relative group-hover:scale-105 transition-transform">
                                                <img src="{{ asset('storage/' . $book->image_path) }}" 
                                                     alt="Cover" 
                                                     class="h-full w-full object-cover"
                                                     onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($book->title) }}&background=random'">
                                            </div>
                                        </td>
                                        
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-bold text-gray-900 dark:text-white line-clamp-1 group-hover:text-indigo-600 transition-colors">
                                                    {{ $book->title }}
                                                </span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                                    {{ $book->author }}
                                                </span>
                                                <div class="flex gap-2">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 border border-blue-100 dark:border-blue-800">
                                                        {{ $book->category->name }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm">
                                                <div class="font-medium text-gray-700 dark:text-gray-200 mb-1">
                                                    Stock: {{ $book->stock_total }}
                                                </div>
                                                @if($book->is_available)
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                        Disponible
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800">
                                                        <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                                        Agotado
                                                    </span>
                                                @endif
                                            </div>
                                        </td>

                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end gap-2">
                                                <button wire:click="edit({{ $book->id }})" class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-lg transition-all" title="Editar">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </button>
                                                <button wire:click="delete({{ $book->id }})" wire:confirm="¿Seguro que deseas eliminar este libro?" class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-all" title="Eliminar">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-24 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-4">
                                                    <svg class="w-10 h-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                                </div>
                                                <h3 class="text-gray-500 font-medium text-lg">No hay libros registrados</h3>
                                                <p class="text-gray-400 text-sm mt-1">Utiliza el formulario de la izquierda para agregar uno nuevo.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($books->hasPages())
                        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                            {{ $books->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>