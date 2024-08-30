<?php

namespace App\Http\Controllers;

use App\Models\Pedido;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Http\Request;

class PedidoController extends Controller
{

    public function index()
    {
        //
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
                'status' => 'pendente'
            ]);

            /** Logging */
            return response()->json(['mensagem' => 'Pedido criado com sucesso. Está sendo processado...'], 201);

            /* Log => evento disparado* */

        }catch (ValidationException $e) {
            
            /** Gera Loggin */
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
