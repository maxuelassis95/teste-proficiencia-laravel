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

class ProcessarEstoque implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $pedido, $produtos;
    public $tries = 5;

    public function __construct(Pedido $pedido, array $produtos)
    {
        $this->pedido = $pedido;
        $this->produtos = $produtos;
    }

    public function handle(): void
    { 
        Log::info('Iniciando o processamento de estoque para o pedido #' . $this->pedido->id);

        try {
            $this->processaEstoque();
        } catch (\Exception $e) {
            Log::error('Erro ao processar o estoque para o pedido #' . $this->pedido->id . ': ' . $e->getMessage());
            PedidoHelper::atualizaPedido($this->pedido, 'erro', 'Erro no processamento de estoque', []);

            Log::info('Disparando job: EnviarEmailErros');
            EnviarEmailErro::dispatch($this->pedido)->onQueue('emails_pedidos');
        }
    }

    protected function processaEstoque()
    {
        foreach ($this->produtos as $produto) {
            $produtoQueVaiSofrerBaixa = Produto::find($produto['id']);

            if ($produtoQueVaiSofrerBaixa) {

                $novaQuantidade = $produtoQueVaiSofrerBaixa->quantidade - $produto['quantidade'];

                // Garantindo que a nova quantidade não será negativa (extra segurança)
                if ($novaQuantidade < 0) {
                    throw new \Exception('Estoque insuficiente para o produto #' . $produto['id']);
                }

                // Att a quantidade do produto
                $produtoQueVaiSofrerBaixa->update(['quantidade' => $novaQuantidade]);

                Log::info('Estoque atualizado para o produto #' . $produto['id']);
            } else {
                throw new \Exception('Produto não encontrado: #' . $produto['id']);
            }
        }

        PedidoHelper::atualizaPedido($this->pedido, 'estoque processado', 'Estoque processado com sucesso', []);
        Log::info('Processamento de estoque finalizado com sucesso para o pedido #' . $this->pedido->id);

        Log::info('Disparando Job: GerarFaturaPedido');
        GerarFaturaPedido::dispatch($this->pedido, $this->produtos)->onQueue('pedidos');
    }
}