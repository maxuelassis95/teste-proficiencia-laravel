<?php

namespace App\Services;

use Exception;

class PagamentoService{

    public function processarPagamento()
    {

        // Simulação de falha aleatória (30% de chance de falha)
        if (rand(1, 100) <= 30) {
            throw new Exception('Ocorreu uma falha no pagamento.');
        }

        return true;
    }
    

}