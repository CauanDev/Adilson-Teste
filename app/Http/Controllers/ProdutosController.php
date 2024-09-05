<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Models\Marcas;
use App\Models\Pedido;
use App\Models\Produto;
use App\Models\Compra;
use App\Models\Fornecedor;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class ProdutosController extends Controller
{
    // Método para listar todos os produtos  public function index(AuthRequest $request)
    public function index(AuthRequest $request)
    {
        // Recupera todos os produtos com informações da marca associada
        $produtos = Produto::with('marca') // Carrega o relacionamento com a marca
            ->orderBy('id', 'DESC')
            ->get();
    
        // Inicializa um array para armazenar a contagem de vendas por produto e a média de tempo
        $produtoVendas = [];
        $produtoTempoMedio = [];
    
        // Recupera todos os pedidos
        $pedidos = Pedido::all();
    
        // Itera sobre os pedidos e contabiliza as vendas dos produtos
        foreach ($pedidos as $pedido) {
            // Decodifica o JSON dos produtos do pedido
            $produtosPedido = json_decode($pedido->produtos, true);
    
            // Verifica se o campo produtos foi decodificado corretamente
            if (is_array($produtosPedido)) {
                foreach ($produtosPedido as $produto) {
                    // Busca o produto pelo nome
                    $produtoData = Produto::where('name', $produto['name'])->first();
    
                    if ($produtoData) {
                        // Obtém o ID do produto e a quantidade vendida
                        $produtoId = $produtoData->id;
                        $quantidade = $produto['quantidade'] ?? 1; // Assume 1 se não especificado
    
                        // Adiciona a quantidade vendida ao total do produto
                        if (isset($produtoVendas[$produtoId])) {
                            $produtoVendas[$produtoId]['quantidade'] += $quantidade;
                            $produtoVendas[$produtoId]['datas'][] = $pedido->created_at; // Adiciona data da compra
                        } else {
                            $produtoVendas[$produtoId] = [
                                'quantidade' => $quantidade,
                                'datas' => [$pedido->created_at],
                            ];
                        }
                    }
                }
            }
        }
    
        // Calcula a média de tempo entre compras para cada produto
        foreach ($produtoVendas as $produtoId => $dados) {
            $datas = $dados['datas'];
            $totalCompras = count($datas);
    
            if ($totalCompras > 1) {
                // Ordena as datas e calcula as diferenças entre compras
                sort($datas);
                $diferencas = [];
                for ($i = 1; $i < $totalCompras; $i++) {
                    $dataAnterior = new \DateTime($datas[$i - 1]);
                    $dataAtual = new \DateTime($datas[$i]);
                    $diferencas[] = $dataAtual->diff($dataAnterior)->days;
                }
                $tempoMedioDias = array_sum($diferencas) / count($diferencas);
            } else {
                $tempoMedioDias = 0; // Não há comparação suficiente para calcular a média
            }
    
            $produtoTempoMedio[$produtoId] = $tempoMedioDias;
        }
    
        // Retorna a lista de produtos com a quantidade total de vendas e a média de tempo entre compras
        return response()->json([
            'status' => true,
            'produtos' => $produtos->map(function ($produto) use ($produtoVendas, $produtoTempoMedio) {
                $totalVendas = $produtoVendas[$produto->id]['quantidade'] ?? 0;
                $tempoMedioDias = $produtoTempoMedio[$produto->id] ?? 0;
                return [
                    'id' => $produto->id,
                    'name' => $produto->name,
                    'marca' => $produto->marca ? $produto->marca->nome : null,
                    'marca_id' => $produto->marca ? $produto->marca->id : null,
                    'quantidade' => $produto->quantidade,
                    'segmento' => $produto->marca ? $produto->marca->segmento : null,
                    'preco' => $produto->preco,
                    'status' => $produto->status,
                    'created_at' => $produto->created_at,
                    'total_vendas' => $totalVendas, // Adiciona a quantidade total de vendas
                    'tempo_medio_dias' => $tempoMedioDias, // Adiciona o tempo médio de compra
                ];
            }),
        ], 200);
    }
    


    // Método para excluir um produto
    public function destroy(AuthRequest $request, $id)
    {
        DB::beginTransaction();  // Inicia a transação
        try {
            // Busca o produto pelo ID fornecido e o exclui
            $produto = Produto::findOrFail($id);
            $produto->delete();

            DB::commit();  // Comita a transação

            // Retorna uma mensagem de sucesso após a exclusão
            return response()->json([
                'status' => true,
                'message' => "Produto excluído com sucesso!",
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();  // Reverte a transação em caso de erro
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    // Método para atualizar um produto
    public function update(AuthRequest $request)
    {
        DB::beginTransaction();  // Inicia a transação
        try {
            // Busca o produto pelo ID fornecido
            $produto = Produto::findOrFail($request->id);

            if (isset($request->status)) {
                // Alterna o status do produto
                $produto->status = $produto->status === "Ativo" ? "Suspenso" : "Ativo";
            } else {
                // Atualiza os detalhes do produto com os dados fornecidos
                $produto->name = $request->name;
                $produto->quantidade = $request->quantidade;
                $produto->preco = $request->preco;
                $produto->marca_id = $request->marcaID;
            }

            $produto->save();
            DB::commit();  // Comita a transação

            // Retorna o produto atualizado com uma mensagem de sucesso
            return response()->json([
                'status' => true,
                'produto' => $produto,
                'message' => "Nome do Produto atualizado com sucesso!",
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();  // Reverte a transação em caso de erro
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    // Método para criar um novo produto
    public function store(AuthRequest $request)
    {
        DB::beginTransaction();  // Inicia a transação
        try {
            if (isset($request->produtoID)) {

                $produto = Produto::findOrFail($request->produtoID);
                $produto->quantidade = $produto->quantidade + $request->quantidade;
                $produto->preco = $request->valor;

            } else {
                $marcaID = isset($request->marcaName)
                    ? Marcas::where('nome', $request->marcaName)->pluck('id')->first()
                    : $request->marcaID;



                $produto = Produto::create([
                    'name' => $request->name,
                    'marca_id' => $marcaID,  // Chave estrangeira para a tabela de marcas
                    'quantidade' => $request->quantidade,
                    'preco' => $request->valor,
                    'status' => 'Ativo'  // Define o status inicial como "Ativo"
                ]);
            }
            $produto->save();

            Compra::create([
                'produto_id' => $produto->id,
                'total' => $request->preco * $request->quantidade,
                'valor' => $request->preco,
                'quantidade' => $request->quantidade
            ]);
            DB::commit();  // Comita a transação

            // Retorna o produto criado com uma mensagem de sucesso
            return response()->json([
                'status' => true,
                'produto' => $produto,
                'message' => 'Produto cadastrado com sucesso!',
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();  // Reverte a transação em caso de erro
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }


    // Método para filtrar produtos com base em parâmetros fornecidos
    public function filter(AuthRequest $request)
    {
        $query = Produto::query();

        // Aplica filtros conforme os parâmetros fornecidos na requisição
        if (isset($request->name)) {
            $query->where('name', $request->name);
        }
        if (isset($request->marca)) {
            $query->whereHas('marca', function ($q) use ($request) {
                $q->where('nome', $request->marca);
            });
        }
        if (isset($request->status)) {
            $query->where('status', $request->status);
        }
        if (isset($request->quantidadeMax)) {
            $query->where('quantidade', '<', $request->quantidadeMax);
        }
        if (isset($request->quantidadeMin)) {
            $query->where('quantidade', '>', $request->quantidadeMin);
        }
        if (isset($request->fornecedor_id) && $request->fornecedor_id !== 'all') {
            $query->where('fornecedor_id', $request->fornecedor_id);
        }
        if (isset($request->segmento)) {
            $query->whereHas('marca', function ($q) use ($request) {
                $q->where('segmento', $request->segmento);
            });
        }

        // Aplica ordenação conforme o parâmetro fornecido
        if (isset($request->ordem)) {
            switch ($request->ordem) {
                case 'maisProdutos':
                    $query->orderBy('quantidade', 'desc');
                    break;

                case 'menosProdutos':
                    $query->orderBy('quantidade', 'asc');
                    break;

                case 'maisVendas':
                    // Ajusta a subconsulta para contar produtos vendidos
                    $subquery = "
                SELECT
                    produto_data->>'name' AS nome,
                    COUNT(*) AS vendas_count
                FROM pedidos
                CROSS JOIN jsonb_array_elements(CAST(produtos AS jsonb)) AS produto_data
                GROUP BY produto_data->>'name'
            ";

                    $query
                        ->select('produtos.*', DB::raw('COALESCE(produto_vendas.vendas_count, 0) AS vendas_count'))
                        ->leftJoin(DB::raw("({$subquery}) AS produto_vendas"), 'produtos.name', '=', 'produto_vendas.nome')
                        ->groupBy('produtos.id', 'vendas_count')
                        ->orderBy('vendas_count', 'desc');
                    break;

                case 'menosVendas':
                    // Ajusta a subconsulta para contar produtos vendidos
                    $subquery = "
                    FROM pedidos
                    CROSS JOIN jsonb_array_elements(CAST(produtos AS jsonb)) AS produto_data
                    GROUP BY produto_data->>'name'
                ";

                    $query
                        ->select('produtos.*', DB::raw('COALESCE(produto_vendas.vendas_count, 0) AS vendas_count'))
                        ->leftJoin(DB::raw("({$subquery}) AS produto_vendas"), 'produtos.name', '=', 'produto_vendas.nome')
                        ->groupBy('produtos.id', 'vendas_count')
                        ->orderBy('vendas_count', 'asc');
                    break;

                case 'nuncaVendidos':
                    // Subconsulta para identificar produtos que nunca foram vendidos
                    $subquery = "
                        SELECT DISTINCT
                            produto_data->>'name' AS nome
                        FROM pedidos
                        CROSS JOIN jsonb_array_elements(CAST(produtos AS jsonb)) AS produto_data
                    ";

                    $query
                        ->leftJoin(DB::raw("({$subquery}) AS produtos_vendidos"), 'produtos.name', '=', 'produtos_vendidos.nome')
                        ->whereNull('produtos_vendidos.nome');
                    break;
            }
        }

        // Inclui o nome da marca na resposta
        $produtos = $query->get()->map(function ($produto) {
            return [
                'id' => $produto->id,
                'name' => $produto->name,
                'marca' => $produto->marca ? $produto->marca->nome : null,
                'marca_id' => $produto->marca ? $produto->marca->id : null,
                'quantidade' => $produto->quantidade,
                'segmento' => $produto->marca ? $produto->marca->segmento : null,
                'preco' => $produto->preco,
                'status' => $produto->status,
                'created_at' => $produto->created_at,
                'total_compras' => $produto->total_compras ?? 0, // Adicione a quantidade total de compras se disponível
            ];
        });

        // Retorna os produtos filtrados como uma coleção
        return response()->json([
            'status' => true,
            'produtos' => $produtos,
        ], 200);
    }




    /**
     * Retorna o tempo médio de compra de um produto com base no nome.
     *
     * @param AuthRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function averagePurchaseTime(AuthRequest $request)
    {
        // Valida se o nome do produto foi fornecido
        if (!$request->has('name')) {
            return response()->json([
                'status' => false,
                'message' => 'Nome do produto não fornecido.',
            ], 400);
        }

        $produtoNome = $request->input('name');

        try {
            // Consulta para obter as datas das compras do produto e as informações do fornecedor
            $subquery = "
                SELECT
                    p.name AS produto_nome,
                    f.name AS fornecedor_nome,
                    MIN(pd.created_at) AS primeira_compra,
                    MAX(pd.created_at) AS ultima_compra,
                    COUNT(*) AS total_compras
                FROM pedidos AS pd
                CROSS JOIN jsonb_array_elements(CAST(pd.produtos AS jsonb)) AS produto_data
                JOIN produtos AS p ON p.name = produto_data->>'name'
                JOIN marcas_produtos AS mp ON p.marca_id = mp.id
                JOIN fornecedores AS f ON mp.fornecedor_id = f.id
                WHERE produto_data->>'name' = ?
                GROUP BY p.name, f.name
            ";

            // Executa a consulta para obter os dados
            $result = DB::select($subquery, [$produtoNome]);

            if (empty($result)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Produto não encontrado ou não foi comprado ainda.',
                ], 404);
            }

            $data = $result[0];

            // Calcula o tempo médio de compra
            $primeiraCompra = new \DateTime($data->primeira_compra);
            $ultimaCompra = new \DateTime($data->ultima_compra);
            $diferencaTotal = $primeiraCompra->diff($ultimaCompra);

            if ($data->total_compras > 1) {
                $diferencaMedia = $diferencaTotal->days / ($data->total_compras - 1);
            } else {
                $diferencaMedia = 0; // Produto não foi comprado mais de uma vez
            }

            return response()->json([
                'status' => true,
                'produto_nome' => $data->produto_nome,
                'fornecedor_nome' => $data->fornecedor_nome,
                'tempo_medio_dias' => $diferencaMedia,
                'message' => 'Tempo médio de compra calculado com sucesso!',
            ], 200);
        } catch (QueryException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erro na consulta ao banco de dados: ' . $e->getMessage(),
            ], 400);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erro inesperado: ' . $e->getMessage(),
            ], 400);
        }
    }


}
