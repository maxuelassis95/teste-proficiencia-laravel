<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PedidoProduto extends Pivot
{
    use HasFactory;

    protected $table = 'pedido_produto';

    protected $fillable = [
        'pedido_id',
        'produto_id',
        'quantidade'
    ];
}
