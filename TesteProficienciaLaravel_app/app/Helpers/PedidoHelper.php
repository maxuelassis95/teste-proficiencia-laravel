<?php 

namespace App\Helpers;
use App\Models\Pedido;
use Illuminate\Support\Facades\Log;

class PedidoHelper
{
    public static function atualizaPedido(Pedido $pedido, string $status, ?string $nota = null, ?array $dadosAdicionais = null ) {

        Log::info("Atualizando pedido", ['pedido_id' => $pedido->id, 'status' => $status, 'nota' => $nota, 'dadosAdicionais' => $dadosAdicionais]);
        
        $pedido->status = $status;

        if ($nota !== null) {

            $pedido->nota = $nota;

        }

        foreach ($dadosAdicionais as $chave => $valor) {
            $pedido->{$chave} = $valor;
        }

        $pedido->save();

    }

}