<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fornecedor extends Model
{
    use HasFactory;
    /**
     * @var string
     */
    protected $table = 'fornecedores';
    protected $fillable = [
        'name',
        'status',
        'email'
    ];

    public function produtos()
    {
        return $this->hasMany(Produto::class, 'fornecedor_id');
    }
    public function marcas()
    {
        return $this->hasMany(Marcas::class, 'fornecedor_id');
    }
}
