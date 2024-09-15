<?php

namespace App\Http\Controllers;

use App\Jobs\Pedidos\VerificarExistenciaProdutos;
use App\Models\Pedido;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class PedidoController extends Controller
{

    public function index(Request $request)
    {
        // Vai ser usado para passar a página da paginação, e realizar o cache de forma correta
        $pagina = $request->input('page', 1);
        Log::info('Página de paginação solicitada: ' . $pagina);
    
        // Cache para os pedidos dos últimos 7 dias
        if (!$request->has('cliente') && !$request->has('status') && !$request->has('data_inicial') && !$request->has('data_final')) {
    
            $cacheKey = "pedidos_ultimos_7_dias_pagina_{$pagina}";
            Log::info("Chave de cache gerada para pedidos sem filtros: {$cacheKey}");
    
            $pedidos = Cache::remember($cacheKey, 60 * 60, function () use ($pagina, $cacheKey) {
                Log::info("Cache não encontrado para chave {$cacheKey}. Realizando consulta ao banco de dados...");
                return Pedido::with('cliente')
                    ->where('created_at', '>=', now()->subDays(7))
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);
            });
    
            Log::info("Pedidos sem filtro carregados para a página {$pagina}");
    
        } else {
            // Filtros aplicados
            Log::info('Filtros aplicados: ' . json_encode($request->all()));
    
            // Cria chave de cache específica para cada filtro
            $cacheKey = 'pedidos_' . md5(serialize($request->all())) . "_pagina_{$pagina}";
            Log::info("Chave de cache gerada para pedidos com filtros: {$cacheKey}");
    
            $pedidos = Cache::remember($cacheKey, 60 * 60, function () use ($request, $cacheKey) {
                Log::info("Cache não encontrado para chave {$cacheKey}. Realizando consulta ao banco de dados com filtros...");
    
                $query = Pedido::with(['cliente']); // aplicandoeager loading
    
                // Filtro por cliente
                if ($request->filled('cliente')) {
                    $query->whereHas('cliente', function ($q) use ($request) {
                        $q->where('nome', 'like', '%' . $request->cliente . '%');
                    });
                    Log::info("Filtro aplicado por cliente: " . $request->cliente);
                }
    
                // Filtro por status
                if ($request->filled('status')) {
                    $query->where('status', $request->status);
                    Log::info("Filtro aplicado por status: " . $request->status);
                }
    
                // Filtro por data inicial
                if ($request->filled('data_inicial')) {
                    $query->whereDate('created_at', '>=', $request->data_inicial);
                    Log::info("Filtro aplicado por data inicial: " . $request->data_inicial);
                }
    
                // Filtro por data final
                if ($request->filled('data_final')) {
                    $query->whereDate('created_at', '<=', $request->data_final);
                    Log::info("Filtro aplicado por data final: " . $request->data_final);
                }
    
                // Ordenação por data de criação e paginação
                return $query->orderBy('created_at', 'desc')->paginate(10);
            });
    
            Log::info("Pedidos com filtros carregados para a página {$pagina}");
        }
    
        //Retorna a view com os pedidos
        Log::info("Renderizando a view de pedidos com os dados carregados.");
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
        } catch (ValidationException $e) {
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
