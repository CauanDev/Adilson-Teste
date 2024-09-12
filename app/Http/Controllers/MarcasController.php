<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Models\Fornecedor;
use App\Models\Marcas;
use DB;
use Exception;
use Illuminate\Http\Request;

class MarcasController extends Controller
{
    public function index(AuthRequest $request)
    {
        $marcas = Marcas::with(['fornecedor:id,name', 'produtos']) // Carrega o relacionamento com o fornecedor e produtos
            ->orderBy('id', 'DESC')
            ->get();

        return response()->json([
            'status' => true,
            'marcas' => $marcas->map(function ($marca) {
                return [
                    'id' => $marca->id,
                    'nome' => $marca->nome,
                    'segmento' => $marca->segmento,
                    'status' => $marca->status,
                    'created_at' => $marca->created_at,
                    'fornecedor_nome' => $marca->fornecedor ? $marca->fornecedor->name : null,
                    'fornecedor_id' => $marca->fornecedor ? $marca->fornecedor->id : null,
                    'produtos' => $marca->produtos->map(function ($produto) {
                        return [
                            'id' => $produto->id,
                            'nome' => $produto->name,
                            'preco' => $produto->preco,
                        ];
                    }),
                ];
            }),
        ], 200);
    }
    public function destroy(Request $request)
    {
        DB::beginTransaction(); 
        try {
            $marca = Marcas::findOrFail($request->id);

            $marca->delete();

            DB::commit();  

            return response()->json([
                'status' => true,
                'message' => "Marca de produto deletada com sucesso!",
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();  
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }


    public function store(AuthRequest $request)
    {
        DB::beginTransaction();  // Inicia a transação
        try {
            $marcaProduto = Marcas::create([
                'fornecedor_id' => $request->fornecedor_id,
                'nome' => $request->nome,
                'segmento' => $request->segmento,
                'status' => "Ativo"
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'marca_produto' => $marcaProduto,
                'message' => "Marca de produto cadastrada com sucesso!",
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();  
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function update(AuthRequest $request)
    {
        DB::beginTransaction();
        try {
            $marca = Marcas::findOrFail($request->id);
            if ($request->status) {
                $marca->update([
                    'status' => $marca->status === "Ativo" ? "Suspenso" : "Ativo"
                ]);
            } else {
                $marca->update([
                    'fornecedor_id' => $request->fornecedor_id,
                    'nome' => $request->nome,
                    'segmento' => $request->segmento,
                ]);
            }


            DB::commit(); 

            return response()->json([
                'status' => true,
                'marca_produto' => $marca,
                'message' => "Marca de produto atualizada com sucesso!",
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();  
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function filter(AuthRequest $request)
    {
        $query = Marcas::query();
        if (isset($request->status))
            $query->where('status', $request->status);

        // Retorna as Marcas filtrados em formato JSON
        return response()->json([
            'status' => true,
            'marcas' => $query->get(),
        ], 201);

    }
}
