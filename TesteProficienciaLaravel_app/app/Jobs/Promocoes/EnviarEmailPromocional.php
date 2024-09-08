<?php

namespace App\Jobs\Promocoes;

use App\Mail\Promocionais\EnviaPromocoesMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnviarEmailPromocional implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $destinatario;

    public function __construct(string $destinatario)
    {
        $this->destinatario = $destinatario;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        Log::info('Enviando email promocional');
        
        try{
            $assunto = 'Parabéns, você recebeu 20% de desconto em toda loja ';
            Mail::to($this->destinatario)->send(new EnviaPromocoesMail($assunto));

            Log::info('Email promocional enviado');

        } catch(\Exception $e) {
            Log::error('Erro no sistema ao tentar enviar email para confirmar sucesso no pedido, '
            . $e->getMessage());
        }

    }
}
