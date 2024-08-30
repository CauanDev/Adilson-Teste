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
        // Recupera todas as marcas, incluindo o nome do fornecedor e os produtos associados, ordenadas por ID em ordem decrescente
        $marcas = Marcas::with(['fornecedor:id,name', 'produtos']) // Carrega o relacionamento com o fornecedor e produtos
            ->orderBy('id', 'DESC')
            ->get();

        // Retorna a lista de marcas com os nomes dos fornecedores e produtos associados em formato JSON
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
                            // Adicione outros campos do produto que desejar incluir
                        ];
                    }),
                ];
            }),
        ], 200);
    }
    public function destroy(Request $request)
    {
        DB::beginTransaction();  // Inicia a transação
        try {
            // Encontra a marca pelo ID
            $marca = Marcas::findOrFail($request->id);

            // Deleta a marca
            $marca->delete();

            DB::commit();  // Comita a transação

            // Retorna uma mensagem de sucesso
            return response()->json([
                'status' => true,
                'message' => "Marca de produto deletada com sucesso!",
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();  // Reverte a transação em caso de erro
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
            // Cria uma nova marca de produto com os dados fornecidos na requisição
            $marcaProduto = Marcas::create([
                'fornecedor_id' => $request->fornecedor_id,
                'nome' => $request->nome,
                'segmento' => $request->segmento,
                'status' => "Ativo"
            ]);

            DB::commit();  // Comita a transação

            // Retorna a marca de produto criada com uma mensagem de sucesso
            return response()->json([
                'status' => true,
                'marca_produto' => $marcaProduto,
                'message' => "Marca de produto cadastrada com sucesso!",
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();  // Reverte a transação em caso de erro
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function update(AuthRequest $request)
    {
        DB::beginTransaction();  // Inicia a transação
        try {
            // Encontra a marca pelo ID
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


            DB::commit();  // Comita a transação

            // Retorna a marca atualizada com uma mensagem de sucesso
            return response()->json([
                'status' => true,
                'marca_produto' => $marca,
                'message' => "Marca de produto atualizada com sucesso!",
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();  // Reverte a transação em caso de erro
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
