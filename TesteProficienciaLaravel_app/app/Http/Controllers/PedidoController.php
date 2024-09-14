<?php

namespace App\Http\Controllers;

use App\Jobs\Pedidos\VerificarExistenciaProdutos;
use App\Models\Pedido;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PedidoController extends Controller
{

    public function index(Request $request)
    {

          // Cache para os pedidos dos últimos 7 dias
          if (!$request->has('cliente') && !$request->has('status') && !$request->has('data_inicial') && !$request->has('data_final')) {
            $pedidos = Cache::remember('pedidos_ultimos_7_dias', 60 * 60, function () {
                return Pedido::with('cliente')
                    ->where('created_at', '>=', now()->subDays(7))
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
            });
        } else {
            //Consulta otimizada com filtros aplicados
            $query = Pedido::with('cliente'); //Eager Loading do relacionamento 'cliente'

            if ($request->filled('cliente')) {
                $query->whereHas('cliente', function($q) use ($request) {
                    $q->where('nome', 'like', '%' . $request->cliente . '%');
                });
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('data_inicial')) {
                $query->whereDate('created_at', '>=', $request->data_inicial);
            }

            if ($request->filled('data_final')) {
                $query->whereDate('created_at', '<=', $request->data_final);
            }

            // Ordenação por data de criação e paginação
            $pedidos = $query->orderBy('created_at', 'desc')->paginate(10);

        }

        return view('admin.pages.pedidos', ['pedidos' => $pedidos]);
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

            Log::info("Dispando job: VerificarExistenciaProdutos");
            VerificarExistenciaProdutos::dispatch($pedido, $produtos)->onQueue('pedidos');

            return response()->json(['mensagem' => 'Pedido criado. Aguarde enquanto está sendo processado.'], 201);

        }catch (ValidationException $e) {
            Log::error('Houve um erro ao tentar criar o pedido: ' . $e->getMessage());
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
