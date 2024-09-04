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

class VerificarExistenciaProdutos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $pedido;
    protected $produtos;

    public function __construct(Pedido $pedido, array $produtos)
    {
        $this->pedido = $pedido;
        $this->produtos = $produtos;
    }

    public function handle()
    {

        if (empty($this->pedido)) {
            /**log erro */
        }
        
        try {
            Log::info("Iniciando o sleep verifica existencia produtos");
            sleep(5);
            Log::info('Terminando o sleep verifica existencia produtos');

            $this->verificarProdutos();

        } catch (\Exception $e) {
            //Log (['mensagem' => 'Erro ao verificar estoque'], 422);
        }

    }

    protected function verificarProdutos() {

        $produtoIds = array_column($this->produtos, 'id');
            $produtosEncontrados = Produto::whereIn('id', $produtoIds)
                                   ->pluck('id')
                                   ->toArray();

            $verificaProdutosNaoEcontrados = array_diff($produtoIds, $produtosEncontrados);

            if(!empty($verificaProdutosNaoEcontrados)) {
                
                // log erro
                PedidoHelper::atualizaPedido($this->pedido, 'Erro no pedido', 
                'Erro: alguns produtos do pedido não existem em nossa base de dados', []);


            } else {
                
                Log::info('Sucesso: Todos os produtos estão em nosso sistema');
                PedidoHelper::atualizaPedido($this->pedido, 'processando...', 'Todos produtos encontrados', []);                

                // log disparando job
                VerificarEstoqueProdutos::dispatch($this->pedido, $this->produtos)->onQueue('pedidos');
            }
    }
}
