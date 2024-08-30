<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;
    protected $fillable = [
        'produtos',
        'preco',
        'desconto',
        'total',
        'status',
        'cliente_id',
        'funcionario_id'
    ];

    /**
     * Retorna a quantidade de clientes por sexo com base nos pedidos.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function countClientesBySexo()
    {
        return self::select('clientes.sexo', DB::raw('count(distinct clientes.id) as total'))
            ->join('clientes', 'pedidos.cliente_id', '=', 'clientes.id')
            ->groupBy('clientes.sexo')
            ->get();
    }
}
