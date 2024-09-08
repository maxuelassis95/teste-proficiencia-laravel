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

    public $tries = 5;

    public function __construct(Pedido $pedido, array $produtos)
    {
        $this->pedido = $pedido;
        $this->produtos = $produtos;
    }

    public function handle(): void
    {

        if (empty($this->pedido)) {
            Log::error("Erro: Pedido não encontrado");
        }
        
        try {
            sleep(5); // retirar na limpeza de código
            Log::info('Iniciando a verificação de produtos...');
            $this->verificarProdutos();

        } catch (\Exception $e) {
            Log::error("Erro: Houve um erro ao verificar a disponibilidade dos produtos no sistema. " . 
            $e->getMessage());
        }

    }

    protected function verificarProdutos() {

        $produtoIds = array_column($this->produtos, 'id');
            $produtosEncontrados = Produto::whereIn('id', $produtoIds)
                                   ->pluck('id')
                                   ->toArray();

            $verificaProdutosNaoEcontrados = array_diff($produtoIds, $produtosEncontrados);

            if(!empty($verificaProdutosNaoEcontrados)) {
                
                Log::error('Erro: alguns produtos do pedido não estão mais disponiveis em nosso sistema');
                PedidoHelper::atualizaPedido($this->pedido, 'Erro no pedido', 
                'Erro: alguns produtos do pedido não estão mais disponiveis em nosso sistema', []);

                Log::info('Disparando job: EnviarEmailErros');
                EnviarEmailErro::dispatch($this->pedido)->onQueue('emails_pedidos');

            } else {
                
                Log::info('Sucesso: Todos os produtos estão disponiveis em nosso sistema, atualizando status do pedido');
                PedidoHelper::atualizaPedido($this->pedido, 'processando...', 'Todos produtos disponiveis', []);                

                Log::info('Disparando Job: VerificarEstoqueProdutos');
                VerificarEstoqueProdutos::dispatch($this->pedido, $this->produtos)->onQueue('pedidos');
            }
    }
}
