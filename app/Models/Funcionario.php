<?php

namespace App\Models;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Funcionario extends Model
{
    use HasFactory;
    protected $fillable = [
        'nome',
        'status',
        'data_nasc',
        'sexo',
        'email',
        'salario'
    ];
    /**
     * Retorna a quantidade de funcionários por sexo, considerando apenas aqueles que têm pedidos associados.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function countFuncionariosBySexoInPedidos()
    {
        return self::join('pedidos', 'funcionarios.id', '=', 'pedidos.funcionario_id')
            ->select('funcionarios.sexo', DB::raw('count(funcionarios.id) as total'))
            ->groupBy('funcionarios.sexo')
            ->get();
    }
    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'funcionario_id');
    }
    /**
     * Obtém a contagem de pedidos associados ao funcionário.
     *
     * @return int
     */
    public function getPedidosCountAttribute()
    {
        return $this->pedidos()->count();
    }
}


