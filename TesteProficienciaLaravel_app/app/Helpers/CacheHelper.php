<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheHelper
{

    public static function limpaCache()
    {

        Log::alert('Iniciando a limpeza de cache.');

        try {

            Cache::flush();
            Log::info("Cache limpo com sucesso.");

        } catch (\Exception $e) {
            Log::error("Erro ao excluir cache: " . $e->getMessage());
        }
    }
}
