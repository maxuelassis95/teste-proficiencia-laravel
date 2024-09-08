<?php

namespace App\Jobs\Pedidos;

use App\Jobs\Promocoes\EnviarEmailPromocional;
use App\Mail\Pedidos\PedidoSucessoMail;
use App\Models\Pedido;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnviarEmailSucesso implements ShouldQueue
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

        Log::alert('Iniciando envio de email para confirmar sucesso no pedido');
        try {
            Mail::to($this->pedido->cliente->email)->send(new PedidoSucessoMail($this->pedido));
            Log::alert('Email enviado');

            EnviarEmailPromocional::dispatch($this->pedido->cliente->email)
                ->delay(now()->addMinutes(2))
                ->onQueue('emails_promocionais');
                
        } catch (\Exception $e) {
            Log::error('Erro no sistema ao tentar enviar email para confirmar sucesso no pedido, '
                . $e->getMessage());
        }
    }
}
