<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'status',
        'quantidade',
        'fornecedor_id',
        'marca',
        'preco',
        'marca_id'
    ];
    public function marca()
    {
        return $this->belongsTo(Marcas::class, 'marca_id'); // Ajuste a chave estrangeira conforme sua configuração
    }

    // Define o relacionamento com o modelo Compra
    public function compras()
    {
        return $this->hasMany(Compra::class, 'produto_id');
    }
}
