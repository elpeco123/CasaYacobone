<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\CategoriaGastoController;
use App\Http\Controllers\CuentaCorrienteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\RetiroController;
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

// Página offline de la PWA (pública: el Service Worker la sirve sin red).
Route::view('/offline', 'offline')->name('offline');

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
        ->parameters(['proveedores' => 'proveedor'])
        ->except(['show'])
        ->middleware('role:admin');

    // CRUD de Categorías (Solo Administrador)
    Route::resource('categorias', CategoriaController::class)
        ->except(['show'])
        ->middleware('role:admin');

    // CRUD de Categorías de Gasto (Solo Administrador)
    Route::resource('categoria-gastos', CategoriaGastoController::class)
        ->parameters(['categoria-gastos' => 'categoriaGasto'])
        ->except(['show'])
        ->middleware('role:admin');

    // Productos CRUD (Solo Administrador)
    Route::resource('productos', ProductoController::class)->middleware('role:admin');

    // Ventas (Vendedores y Administradores)
    Route::resource('ventas', VentaController::class)->only(['index', 'create', 'store', 'show']);

    // Apertura y cierre de Caja (Vendedores y Administradores)
    Route::get('/caja', [CajaController::class, 'index'])->name('caja.index');
    Route::post('/caja', [CajaController::class, 'store'])->name('caja.store');
    Route::post('/caja/{caja}/cerrar', [CajaController::class, 'cerrar'])->name('caja.cerrar');

    // Retiros / gastos de la caja abierta (Vendedores y Administradores)
    Route::post('/caja/retiros', [RetiroController::class, 'store'])->name('caja.retiros.store');
    Route::delete('/caja/retiros/{retiro}', [RetiroController::class, 'destroy'])->name('caja.retiros.destroy');

    // Cuentas corrientes de clientes (Solo Administrador)
    Route::middleware('role:admin')->prefix('cuentas-corrientes')->name('cuentas-corrientes.')->group(function () {
        Route::get('/', [CuentaCorrienteController::class, 'index'])->name('index');
        Route::get('/{cliente}', [CuentaCorrienteController::class, 'show'])->name('show');
        Route::post('/{cliente}/pagos', [CuentaCorrienteController::class, 'pagar'])->name('pagar');
    });

    // Reportes (Solo Administrador)
    Route::prefix('reportes')->name('reportes.')->middleware('role:admin')->group(function () {
        Route::get('/diario', [ReporteController::class, 'diario'])->name('diario');
        Route::get('/rendimiento', [ReporteController::class, 'rendimiento'])->name('rendimiento');
        Route::get('/cajas', [ReporteController::class, 'cajas'])->name('cajas');
    });
});
