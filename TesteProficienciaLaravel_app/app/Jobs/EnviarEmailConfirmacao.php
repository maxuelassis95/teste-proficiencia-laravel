<?php

namespace App\Jobs;

use App\Models\Pedido;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EnviarEmailConfirmacao implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $pedido;

    public function __construct(Pedido $pedido)
    {
       $this->pedido = $pedido;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
    }
}
