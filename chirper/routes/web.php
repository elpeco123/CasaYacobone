<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VentaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Rutas web para Casa Yacobone - Sistema de Control de Stock
|
*/

// Redirect root to dashboard or login
Route::get('/', function () {
    return redirect()->route('login');
});

// Guest routes (Login & Registration)
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Logout (Authenticated users)
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Protected routes (Requires authentication)
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // CRUD de Usuarios (Solo Administrador)
    Route::resource('users', UserController::class)->middleware('role:admin');

    // CRUD de Proveedores (Solo Administrador)
    Route::resource('proveedores', ProveedorController::class)
        ->except(['show'])
        ->middleware('role:admin');

    // CRUD de Categorías (Solo Administrador)
    Route::resource('categorias', CategoriaController::class)
        ->except(['show'])
        ->middleware('role:admin');

    // Productos CRUD
    Route::resource('productos', ProductoController::class);

    // Eliminar producto solo para admin
    Route::delete('/productos/{producto}', [ProductoController::class, 'destroy'])
        ->middleware('role:admin')
        ->name('productos.destroy');

    // Ventas
    Route::resource('ventas', VentaController::class)->only(['index', 'create', 'store', 'show']);

    // Reportes
    Route::prefix('reportes')->name('reportes.')->group(function () {
        Route::get('/diario', [ReporteController::class, 'diario'])->name('diario');
        Route::get('/semanal', [ReporteController::class, 'semanal'])->name('semanal');
    });
});
