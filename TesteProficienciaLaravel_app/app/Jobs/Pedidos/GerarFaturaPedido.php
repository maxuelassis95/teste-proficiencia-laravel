<?php

namespace App\Jobs\Pedidos;

use App\Helpers\PedidoHelper;
use App\Models\Pedido;
use App\Models\PedidoProduto;
use App\Models\Produto;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GerarFaturaPedido implements ShouldQueue
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
        sleep(5);

        try {
            Log::info('Iniciando o processo de geração de fatura para o pedido #' . $this->pedido->id);

            $this->criarPedidoProduto();
            $this->atualizarPedido();

            Log::info('Fatura gerada com sucesso para o pedido #' . $this->pedido->id);
        } catch (\Exception $e) {
            Log::error('Erro ao gerar a fatura do pedido #' . $this->pedido->id . '. Mensagem: ' . $e->getMessage());
        }
    }

    protected function criarPedidoProduto()
    {
        foreach ($this->produtos as $produto) {
            PedidoProduto::create([
                'pedido_id' => $this->pedido->id,
                'produto_id' => $produto['id'],
                'quantidade' => $produto['quantidade']
            ]);

            Log::info('Produto #' . $produto['id'] . ' adicionado ao pedido #' . $this->pedido->id);
        }
    }

    protected function atualizarPedido()
    {
        $total = 0;

        foreach ($this->produtos as $produto) {
            $prod = Produto::find($produto['id']);

            if ($prod) {
                $subtotal = $prod->preco * $produto['quantidade'];
                $total += $subtotal;

                Log::info('Subtotal calculado para o produto #' . $produto['id'] . ': R$ ' . $subtotal);
            } else {
                Log::error('Produto não encontrado: #' . $produto['id']);
            }
        }


        PedidoHelper::atualizaPedido(
            $this->pedido,
            'aguardando pagamento',
            'Todas etapas foram concluidas, aguardando pagamento',
            ['total' => $total]
        );

        EnviarEmailSucesso::dispatch($this->pedido)->onQueue('emails_pedidos');

        Log::info('Total do pedido atualizado para: R$ ' . $total);
    }
}
