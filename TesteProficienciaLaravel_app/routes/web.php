<?php

use App\Http\Controllers\AutenticaController;
use App\Http\Controllers\PedidoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/pedidos');
});

// Grupo de rotas protegidas
Route::middleware('auth')->group(function () {
    Route::get('/pedidos', [PedidoController::class, 'index'])->name('pedidos.index');

    Route::get('/horizon', function() {
        return redirect('/horizon');
    })->name('horizon');

    Route::get('/telescope', function() {
        return redirect('/telescope');
    })->name('telescope');
});

Route::get('/login', [AutenticaController::class, 'index'])->name('login');
Route::post('/login', [AutenticaController::class, 'login']);
Route::get('/logout', [AutenticaController::class, 'logout'])->name('logout');
