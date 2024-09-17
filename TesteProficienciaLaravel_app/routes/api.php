<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\PagamentoController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\PedidoController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Rotas da api pedidos
Route::apiResource('clientes', ClienteController::class);
Route::apiResource('produtos', ProdutoController::class);
Route::apiResource('pedidos', PedidoController::class);

// Rotas da api de pagamentos
Route::get('/pagamento', [PagamentoController::class, 'processarPagamento'])->name('pagamento.processarPagamento');