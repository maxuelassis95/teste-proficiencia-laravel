<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'descricao',
        'preco',
        'quantidade'
    ];

    public function pedidos()
    {
        return $this->belongsToMany(Pedido::class, 'pedido_produto')
               ->withPivot('quantidade')
               ->withTimeStamps();
    }

}
