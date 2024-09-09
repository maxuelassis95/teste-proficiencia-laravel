<?php

namespace App\Http\Controllers;

use App\Jobs\Pedidos\VerificarExistenciaProdutos;
use App\Models\Pedido;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Http\Request;

class PedidoController extends Controller
{

    public function index()
    {
        return response()->view(['admin.pages.pedidos', 200]);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {

        try {

            $validaDados = $request->validate([
                'cliente_id' => 'required|exists:clientes,id',
            ]);

            $pedido = Pedido::create([
                'cliente_id' => $request->input('cliente_id'),
                'total' => 0.00,
                'status' => 'pendente',
                'nota' => 'pedido iniciado'
            ]);

            $produtos = $request->produtos;

            /* Log => evento disparado* */
            VerificarExistenciaProdutos::dispatch($pedido, $produtos)->onQueue('pedidos');

            /** Log */
            return response()->json(['mensagem' => 'Pedido criado. Aguarde enquanto está sendo processado.'], 201);

        }catch (ValidationException $e) {
            
            /** Gera Log */
            return response()->json(['mensagem' => 'Dados inválidos'], 422);
        
        }

    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
