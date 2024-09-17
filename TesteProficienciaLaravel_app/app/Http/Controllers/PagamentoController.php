<?php

namespace App\Http\Controllers;

use App\Notifications\SlackNotificacao;
use App\Services\PagamentoService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class PagamentoController extends Controller
{
    
    protected PagamentoService $pagamentoService;

    public function __construct(PagamentoService $pagamentoService)
    {
        $this->pagamentoService = $pagamentoService;
    }

    public function processarPagamento() {
        $maximoTentativas = 5; 
        $tentativas = 0;
        $backoff = 100;

        while ($tentativas < $maximoTentativas) {
            try {
                $tentativas++;
                Log::info("Tentativa $tentativas de processamento de pagamento.");

                $this->pagamentoService->processarPagamento();

                // Se o pagamento for bem-sucedido
                return response()->json(['message' => 'Pagamento processado com sucesso'], 200);

            } catch (\Exception $e) {
                Log::error("Erro na tentativa $tentativas: " . $e->getMessage());

                // Aumenta o tempo de backoff exponencialmente
                usleep($backoff * 1000); // Convertendo milissegundos para microsegundos
                $backoff *= 2; 
            }
        }

        Log::critical('Falha crítica após várias tentativas de pagamento.');

        // Envia notificação via Slack
        Notification::route('slack', env('SLACK_WEBHOOK_URL'))
            ->notify(new SlackNotificacao('Erro: O cliente xxx teve falha no pagamento após ' . $tentativas . ' tentativas.'));

        return response()->json(['error' => 'Pagamento falhou após várias tentativas.'], 500);
    }

}
