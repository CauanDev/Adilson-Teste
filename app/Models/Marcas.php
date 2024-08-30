<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Marcas extends Model
{
    use HasFactory;
    protected $table = 'marcas_produtos';
    protected $fillable = [
        'fornecedor_id',
        'nome',
        'segmento',
        'status'
    ];

    // Relacionamento com o modelo Fornecedor
    public function fornecedor()
    {
        return $this->belongsTo(Fornecedor::class);
    }
    public function produtos()
    {
        return $this->hasMany(Produto::class, 'marca_id'); // Ajuste a chave estrangeira conforme sua configuração
    }
}
