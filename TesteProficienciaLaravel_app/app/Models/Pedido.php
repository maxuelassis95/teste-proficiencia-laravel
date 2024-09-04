<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'total',
        'status',
        'nota'
    ];

    public function cliente()
    {
        return $this->belongTo(Cliente::class);
    }

    public function produtos()
    {
        return $this->belongsToMany(Produto::class, 'pedido_produto')
               ->withPivot('quantidade')
               ->withTimeStamps();
    }


}
