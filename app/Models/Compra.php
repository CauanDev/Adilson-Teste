<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use HasFactory;
    protected $table = 'compras_produtos';
    protected $fillable = [
        'produto_id',
        'total',
        'valor',
        'quantidade',
    ];
    public function produto()
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }
}
