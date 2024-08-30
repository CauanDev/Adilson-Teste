<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    
    
        protected $fillable = [
            'nome',
            'email',
            'data_nasc',  // Adicione este campo
            'sexo',
            'tipo',
            'status',
        ];

    
    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'cliente_id');
    }

}

