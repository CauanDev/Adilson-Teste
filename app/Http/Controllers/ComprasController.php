<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Models\Compra;
use Illuminate\Http\Request;

class ComprasController extends Controller
{
    public function index(AuthRequest $request)
    {
        $compras = Compra::with('produto.marca.fornecedor') 
            ->orderBy('id', 'DESC')
            ->get();

        return response()->json([
            'status' => true,
            'compras' => $compras,
        ], 200);
    }
}
