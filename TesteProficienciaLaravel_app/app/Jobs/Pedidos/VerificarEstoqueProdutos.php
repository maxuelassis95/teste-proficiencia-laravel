<?php

namespace App\Jobs\Pedidos;

use App\Helpers\PedidoHelper;
use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class VerificarEstoqueProdutos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $pedido, $produtos;

    public function __construct(Pedido $pedido, array $produtos)
    {
        $this->pedido = $pedido;
        $this->produtos = $produtos;
    }

    public function handle(): void
    {
        
        try{
            sleep(8); // retirar na limpeza de código

            Log::info('Iniciando a verificação de estoque...');
            $this->verificaEstoque();

        } catch(\Exception $e) {
            Log::error('Houve um erro ao verificar estoque dos produtos no banco de dados. ' . 
            $e->getMessage());        
        }

    }

    protected function verificaEstoque() {

        $estoqueInsuficiente = false;
        $produtoSemEstoque = null;
        
        foreach($this->produtos as $produto) {

            $prod = Produto::find($produto['id']);

            if ($prod && ($prod->quantidade < $produto['quantidade'])) {

                Log::error('Erro: estoque insuficiente par o produto: #' . $produto['id']);
                $estoqueInsuficiente = true;
                $produtoSemEstoque = $produto['id'];
                break;
            }
        }

        if($estoqueInsuficiente) {
            PedidoHelper::atualizaPedido($this->pedido, 'erro', 
                'Erro: estoque insuficiente para o produto #' . $produtoSemEstoque['id'], []);
        } else {
            PedidoHelper::atualizaPedido($this->pedido, 'processando', 'Todos produtos em estoque', []);
        }

    }

}
