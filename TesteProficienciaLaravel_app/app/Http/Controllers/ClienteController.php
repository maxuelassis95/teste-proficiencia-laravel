<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
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
        //
    }

    public function show(string $id)
    {
        
        $cliente = Cliente::find($id);

        $pedidos = $cliente->pedidos()->get();

        $response = '';

        if ($cliente) {
            $response = response()->json([            
                $cliente,
                $pedidos,
            ], 200);
        } else {
            $response = response()->json([
                'msg' => 'Erro'
            ], 200);
        }
        
        return $response;

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
