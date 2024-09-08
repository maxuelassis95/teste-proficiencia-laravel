<?php

namespace App\Jobs\Pedidos;

use App\Mail\Pedidos\PedidoErroMail;
use App\Models\Pedido;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnviarEmailErro implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $pedido;
    public $tries = 3;

    public function __construct(Pedido $pedido)
    {
        $this->pedido = $pedido;
    }

    public function handle(): void
    {
        try{

            Mail::to($this->pedido->cliente->email)->send(new PedidoErroMail($this->pedido));
            
        } catch (\Exception $e) {
            Log::error('Erro no sistema ao tentar enviar email de erro: ' . $e->getMessage());
        }
    }
}
