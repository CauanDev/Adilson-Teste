<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Models\Cliente;
use App\Models\Funcionario;
use App\Models\Marcas;
use Exception;
use Illuminate\Database\QueryException;
use App\Models\Pedido;
use App\Models\Produto;
use Illuminate\Support\Facades\DB;

class PedidosController extends Controller
{


    // Funcao nao utilizada mais
    // public static function countClientesBySexo()
    // {
    //     // Chama o método estático do modelo Pedido para contar clientes por sexo
    //     $clientesPorSexo = Pedido::countClientesBySexo();

    //     // Retorna a contagem em formato JSON
    //     return response()->json($clientesPorSexo);
    // }


    // Método para listar todos os pedidos
    public function index(AuthRequest $request)
    {
        $pedidos = Pedido::orderBy('id', 'DESC')->get();

        foreach ($pedidos as $pedido) {
            $produtos = json_decode($pedido->produtos, true);

            foreach ($produtos as &$produto) {
                $produtoData = Produto::where('name', $produto['name'])->first(['id', 'marca_id']);

                if ($produtoData) {
                    $produto['id'] = $produtoData->id;
                    $marca = Marcas::find($produtoData->marca_id);
                    $produto['segmento'] = $marca->segmento;

                } else {
                    $produto['id'] = null;
                    $produto['segmento_id'] = null;
                }
            }

            $cliente = Cliente::find($pedido->cliente_id);
            $pedido->cliente_nome = $cliente ? $cliente->nome : null;
            $pedido->cliente_tipo = $cliente ? $cliente->tipo : null;
            $pedido->cliente_sexo = $cliente ? $cliente->sexo : null;
            $funcionario = Funcionario::find($pedido->funcionario_id);
            $pedido->funcionario_nome = $funcionario ? $funcionario->nome : null;
            $pedido->funcionario_sexo = $funcionario ? $funcionario->sexo : null;

            $pedido->produtos = $produtos;
        }

        return response()->json([
            'status' => true,
            'pedidos' => $pedidos,
        ], 200);
    }


    // Método para criar um novo pedido
    public function store(AuthRequest $request)
    {
        DB::beginTransaction();
        try {
            $pedido = Pedido::create([
                'cliente_id' => $request->clienteId,
                'funcionario_id' => $request->funcionarioId,
                'produtos' => json_encode($request->produtos),
                'total' => $request->total,
                'status' => "Não Processado"
            ]);

            foreach ($request->produtos as $produto) {
                $produtoModel = Produto::where('name', $produto['name'])->firstOrFail();
                $produtoModel->quantidade -= $produto['quantidade'];
                if ($produtoModel->quantidade == 0)
                    $produtoModel->status = "Suspenso";
                $produtoModel->save();
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'pedido' => $pedido,
                'message' => "Pedido cadastrado com sucesso!",
            ], 201);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Erro na consulta ao banco de dados: ' . $e->getMessage(),
            ], 400);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Erro inesperado: ' . $e->getMessage(),
            ], 400);
        }
    }

    // Método para atualizar um pedido existente
    public function update(AuthRequest $request)
    {
        DB::beginTransaction();
        try {
            $pedido = Pedido::findOrFail($request->id);

            if (isset($request->status)) {
                $pedido->status = $pedido->status === "Entregue" ? "Não Processado" : "Entregue";
            } else {
                $produtosAntigos = json_decode($pedido->produtos, true);
                $produtosNovos = $request->produtos;

                foreach ($produtosAntigos as $produtoAntigo) {
                    $produtoModel = Produto::where('name', $produtoAntigo['name'])->firstOrFail();
                    $produtoModel->quantidade += $produtoAntigo['quantidade'];
                    $produtoModel->status = ($produtoModel->quantidade == 0) ? "Suspenso" : "Ativo";

                    $produtoModel->save();
                }

                foreach ($produtosNovos as $produtoNovo) {
                    $produtoModel = Produto::where('name', $produtoNovo['name'])->firstOrFail();
                    $produtoModel->quantidade -= $produtoNovo['quantidade'];
                    $produtoModel->status = ($produtoModel->quantidade == 0) ? "Suspenso" : "Ativo";
                    $produtoModel->save();
                }

                $pedido->update([
                    'cliente_id' => $request->clienteId,
                    'funcionario_id' => $request->funcionarioId,
                    'produtos' => json_encode($request->produtos),
                    'total' => $request->total,
                ]);
            }

            $pedido->save();
            DB::commit();

            return response()->json([
                'status' => true,
                'pedido' => $pedido,
                'message' => "Pedido atualizado com sucesso!",
            ], 200);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Erro na consulta ao banco de dados: ' . $e->getMessage(),
            ], 400);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Erro inesperado: ' . $e->getMessage(),
            ], 400);
        }
    }

    // Método para filtrar pedidos com base em parâmetros fornecidos
    public function filter(AuthRequest $request)
    {
        $query = Pedido::query();

        if (isset($request->name)) {
            $cliente = Cliente::where('nome', 'like', '%' . $request->name . '%')->first();
            if ($cliente) {
                $query->where('cliente_id', $cliente->id);
            } else {
                return response()->json([
                    'status' => true,
                    'pedidos' => [],
                ], 200);
            }
        }

        if (isset($request->nameFuncionario)) {
            $funcionario = Funcionario::where('nome', 'like', '%' . $request->nameFuncionario . '%')->first();
            if ($funcionario) {
                $query->where('funcionario_id', $funcionario->id);
            } else {
                return response()->json([
                    'status' => true,
                    'pedidos' => [],
                ], 200);
            }
        }

        if (isset($request->status)) {
            $query->where('status', $request->status);
        }

        if (isset($request->dataMinima)) {
            $query->whereDate('data', '>=', $request->dataMinima);
        }

        if (isset($request->dataMaxima)) {
            $query->whereDate('data', '<=', $request->dataMaxima);
        }

        if (isset($request->valorMin)) {
            $query->where('total', '>=', $request->valorMin);
        }

        if (isset($request->valorMax)) {
            $query->where('total', '<=', $request->valorMax);
        }

        if (isset($request->segmento)) {
            $pedidos = $query->get();

            // Filtra os pedidos que contêm produtos com o segmento especificado
            $filteredPedidos = [];
            foreach ($pedidos as $pedido) {
                $produtos = json_decode($pedido->produtos, true);
                foreach ($produtos as $produto) {
                    $produtoData = Produto::where('name', $produto['name'])->first(['id', 'marca_id']);
                    if ($produtoData) {
                        $marca = Marcas::find($produtoData->marca_id);
                        if ($marca && $marca->segmento == $request->segmento) {
                            $filteredPedidos[] = $pedido;
                            break;
                        }
                    }
                }
            }

            return response()->json([
                'status' => true,
                'pedidos' => $filteredPedidos,
            ], 200);
        }

        $pedidos = $query->get();

        return response()->json([
            'status' => true,
            'pedidos' => $pedidos,
        ], 200);
    }


    // Método para excluir um pedido
    public function destroy(AuthRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $pedido = Pedido::findOrFail($id);

            // Atualiza a quantidade dos produtos no estoque
            $produtos = json_decode($pedido->produtos, true);
            foreach ($produtos as $produto) {
                $produtoModel = Produto::where('name', $produto['name'])->first();
                if ($produtoModel) {
                    $produtoModel->quantidade += $produto['quantidade'];
                    $produtoModel->save();
                }
            }


            // Exclui o pedido
            $pedido->delete();

            DB::commit(); 

            return response()->json([
                'status' => true,
                'message' => "Pedido excluído com sucesso!",
            ], 200);
        } catch (QueryException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Erro na consulta ao banco de dados: ' . $e->getMessage(),
            ], 400);
        } catch (Exception $e) {
            DB::rollBack(); 
            return response()->json([
                'status' => false,
                'message' => 'Erro inesperado: ' . $e->getMessage(),
            ], 400);
        }
    }
}
