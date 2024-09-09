<?php

use App\Http\Controllers\PedidoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
