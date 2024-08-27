<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;

class ProdutoController extends Controller
{

    public function index()
    {
        $arr = [
            'Olá mundo!',
        ];

        return response()->json($arr, 200);
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
        
        $produto = Produto::find($id);

        $response = '';

        if($produto) {
        
            $response = response()->json($produto, 200);
        
        } else {
            
            $response = response()->json([
                'msg' => 'Produto não encontrado',
                'cod' => 500, 
            ]);

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
