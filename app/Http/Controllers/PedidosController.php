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
    /**
     * Retorna a quantidade de clientes por sexo com base nos pedidos.
     *
     * @return \Illuminate\Support\Collection
     */
    public static function countClientesBySexo()
    {
        // Chama o método estático do modelo Pedido para contar clientes por sexo
        $clientesPorSexo = Pedido::countClientesBySexo();

        // Retorna a contagem em formato JSON
        return response()->json($clientesPorSexo);
    }

    // Método para listar todos os pedidos
    public function index(AuthRequest $request)
    {
        // Recupera todos os pedidos ordenados por ID em ordem decrescente
        $pedidos = Pedido::orderBy('id', 'DESC')->get();

        // Processa cada pedido para adicionar o ID do produto, segmento, nome do cliente e nome do funcionário
        foreach ($pedidos as $pedido) {
            // Decodifica o JSON dos produtos
            $produtos = json_decode($pedido->produtos, true);

            // Adiciona o ID do produto e o segmento a cada produto
            foreach ($produtos as &$produto) {
                // Busca o produto pelo nome
                $produtoData = Produto::where('name', $produto['name'])->first(['id', 'marca_id']);

                if ($produtoData) {
                    // Adiciona o ID do produto
                    $produto['id'] = $produtoData->id;

                    // Busca a marca associada ao produto
                    $marca = Marcas::find($produtoData->marca_id);

                    // Adiciona o segmento da marca
                    $produto['segmento'] = $marca->segmento;

                } else {
                    $produto['id'] = null; // Ou outra lógica de fallback
                    $produto['segmento_id'] = null; // Ou outra lógica de fallback
                }
            }

            // Busca o nome do cliente associado ao pedido
            $cliente = Cliente::find($pedido->cliente_id);
            $pedido->cliente_nome = $cliente ? $cliente->nome : null;

            // Busca o nome do funcionário associado ao pedido
            $funcionario = Funcionario::find($pedido->funcionario_id);
            $pedido->funcionario_nome = $funcionario ? $funcionario->nome : null;

            // Atualiza o campo produtos do pedido com os IDs e segmentos
            $pedido->produtos = $produtos;
        }

        // Retorna a lista de pedidos com os IDs, segmentos dos produtos, nomes do cliente e funcionário
        return response()->json([
            'status' => true,
            'pedidos' => $pedidos,
        ], 200);
    }


    // Método para criar um novo pedido
    public function store(AuthRequest $request)
    {
        DB::beginTransaction();  // Inicia a transação
        try {
            // Cria um novo pedido com os dados fornecidos
            $pedido = Pedido::create([
                'cliente_id' => $request->clienteId,
                'funcionario_id' => $request->funcionarioId,
                'produtos' => json_encode($request->produtos),
                'total' => $request->total,
                'status' => "Não Processado"  // Define o status inicial como "Não Processado"
            ]);

            // Atualiza a quantidade dos produtos no estoque
            foreach ($request->produtos as $produto) {
                $produtoModel = Produto::where('name', $produto['name'])->firstOrFail();
                $produtoModel->quantidade -= $produto['quantidade'];
                $produtoModel->save();
            }

            DB::commit();  // Comita a transação

            // Retorna o pedido criado com uma mensagem de sucesso
            return response()->json([
                'status' => true,
                'pedido' => $pedido,
                'message' => "Pedido cadastrado com sucesso!",
            ], 201);
        } catch (QueryException $e) {
            DB::rollBack();  // Reverte a transação em caso de erro de consulta
            return response()->json([
                'status' => false,
                'message' => 'Erro na consulta ao banco de dados: ' . $e->getMessage(),
            ], 400);
        } catch (Exception $e) {
            DB::rollBack();  // Reverte a transação em caso de erro inesperado
            return response()->json([
                'status' => false,
                'message' => 'Erro inesperado: ' . $e->getMessage(),
            ], 400);
        }
    }

    // Método para atualizar um pedido existente
    public function update(AuthRequest $request)
    {
        DB::beginTransaction();  // Inicia a transação
        try {
            // Busca o pedido pelo ID fornecido
            $pedido = Pedido::findOrFail($request->id);

            if (isset($request->status)) {
                // Alterna o status do pedido
                $pedido->status = $pedido->status === "Entregue" ? "Não Processado" : "Entregue";
            } else {
                // Calcula a diferença de quantidade dos produtos
                $produtosAntigos = json_decode($pedido->produtos, true);
                $produtosNovos = $request->produtos;

                // Atualiza a quantidade dos produtos antigos (retorna ao estoque)
                foreach ($produtosAntigos as $produtoAntigo) {
                    $produtoModel = Produto::where('name', $produtoAntigo['name'])->firstOrFail();
                    $produtoModel->quantidade += $produtoAntigo['quantidade'];
                    $produtoModel->save();
                }

                // Atualiza a quantidade dos produtos novos (reduzindo do estoque)
                foreach ($produtosNovos as $produtoNovo) {
                    $produtoModel = Produto::where('name', $produtoNovo['name'])->firstOrFail();
                    $produtoModel->quantidade -= $produtoNovo['quantidade'];
                    $produtoModel->save();
                }

                // Atualiza os detalhes do pedido
                $pedido->update([
                    'cliente_id' => $request->clienteId,
                    'funcionario_id' => $request->funcionarioId,
                    'produtos' => json_encode($request->produtos),
                    'total' => $request->total,
                ]);
            }

            $pedido->save();
            DB::commit();  // Comita a transação

            // Retorna o pedido atualizado com uma mensagem de sucesso
            return response()->json([
                'status' => true,
                'pedido' => $pedido,
                'message' => "Pedido atualizado com sucesso!",
            ], 200);
        } catch (QueryException $e) {
            DB::rollBack();  // Reverte a transação em caso de erro de consulta
            return response()->json([
                'status' => false,
                'message' => 'Erro na consulta ao banco de dados: ' . $e->getMessage(),
            ], 400);
        } catch (Exception $e) {
            DB::rollBack();  // Reverte a transação em caso de erro inesperado
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

        // Aplica filtros conforme os parâmetros fornecidos na requisição
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

        // Se o filtro de segmento for aplicado
        if (isset($request->segmento)) {
            // Recupera todos os pedidos sem filtro de segmento
            $pedidos = $query->get();

            // Filtra os pedidos que contêm produtos com o segmento especificado
            $filteredPedidos = [];
            foreach ($pedidos as $pedido) {
                $produtos = json_decode($pedido->produtos, true);
                foreach ($produtos as $produto) {
                    // Busca o produto pelo nome
                    $produtoData = Produto::where('name', $produto['name'])->first(['id', 'marca_id']);
                    if ($produtoData) {
                        // Busca a marca associada ao produto
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

        // Recupera os pedidos filtrados sem segmentação
        $pedidos = $query->get();

        return response()->json([
            'status' => true,
            'pedidos' => $pedidos,
        ], 200);
    }


    // Método para excluir um pedido
    public function destroy(AuthRequest $request, $id)
    {
        DB::beginTransaction();  // Inicia a transação
        try {
            // Busca o pedido pelo ID fornecido
            $pedido = Pedido::findOrFail($id);

            // Atualiza a quantidade dos produtos no estoque (retorna ao estoque)
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

            DB::commit();  // Comita a transação

            // Retorna uma mensagem de sucesso após a exclusão
            return response()->json([
                'status' => true,
                'message' => "Pedido excluído com sucesso!",
            ], 200);
        } catch (QueryException $e) {
            DB::rollBack();  // Reverte a transação em caso de erro de consulta
            return response()->json([
                'status' => false,
                'message' => 'Erro na consulta ao banco de dados: ' . $e->getMessage(),
            ], 400);
        } catch (Exception $e) {
            DB::rollBack();  // Reverte a transação em caso de erro inesperado
            return response()->json([
                'status' => false,
                'message' => 'Erro inesperado: ' . $e->getMessage(),
            ], 400);
        }
    }
}
