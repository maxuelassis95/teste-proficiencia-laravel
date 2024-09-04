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
            Log::info("Inicjando o sleep verifica estoque produtos");
            sleep(8);
            Log::info('Inicjando o sleep verifica estoque produtos');

            $this->verificaEstoque();

        } catch(\Exception $e) {
            //log erro
        }

    }

    protected function verificaEstoque() {
        
        foreach($this->produtos as $produto) {

            $prod = Produto::find($produto['id']);

            if ($prod && ($prod->quantidade < $produto['quantidade'])) {

                /** log error, produto com quantidade insuficiente */
                PedidoHelper::atualizaPedido($this->pedido, 'erro', 
                'Erro: estoque insuficiente para o produto #' . $produto['id'], []);

                return;
            }

           PedidoHelper::atualizaPedido($this->pedido, 'processando', 'Todos produtos em estoque', []);

        }

    }

}
