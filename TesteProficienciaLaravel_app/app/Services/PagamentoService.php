<?php

namespace App\Services;

use Exception;

class PagamentoService{

    public function processarPagamento()
    {

        // Simulação de falha aleatória (30% de chance de falha)
        if (rand(1, 100) <= 90) {
            throw new Exception('Simulação de falha no pagamento.');
        }

        // Retorna sucesso se não falhar
        return true;
        
        /*
        $tentativas = 5;
        $atrasoInicial = 100; // 100ms de atraso inicial

        for ($i = 0; $i < $tentativas; $i++) {
            try {
                return $this->realizarRequisicaoPagamento();
            } catch (Exception $e) {
                Log::alert('Tentativa ' . ($i + 1) . ' falhou: ' . $e->getMessage());

                if ($i === $tentativas - 1) {
                    Log::error('Tentativas de pagamento esgotadas: ' . $e->getMessage());
                    throw $e;  // Todas as tentativas falharam, lança exceção
                }

                // Atraso com backoff exponencial
                usleep($atrasoInicial * 1000);
                $atrasoInicial *= 2;
            }
        }
            
        */
    }
    

}