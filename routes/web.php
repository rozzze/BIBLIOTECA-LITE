<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Livewire\Admin\BookManager;
use App\Livewire\Admin\LoanManager;
use App\Livewire\Student\BookCatalog;
use App\Livewire\Admin\PenaltyManager;
use App\Livewire\Student\MyLoans;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    Route::get('/catalogo', BookCatalog::class)->name('student.catalog');
    Route::get('/mis-libros', MyLoans::class)->name('student.loans');
});

// Grupo protegido: Solo autenticados Y con rol de bibliotecario
Route::middleware(['auth', 'role:librarian'])->prefix('admin')->group(function () {
    
    // Ruta para gestionar libros
    Route::get('/books', BookManager::class)->name('admin.books');
    Route::get('/loans', LoanManager::class)->name('admin.loans');
    Route::get('/penalties', PenaltyManager::class)->name('admin.penalties');

});