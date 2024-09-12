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
    // Método para listar todos os produtos
    public function index(AuthRequest $request)
    {
        $produtos = Produto::with('marca') 
            ->orderBy('id', 'DESC')
            ->get();
    
        $produtoVendas = [];
        $produtoTempoMedio = [];
    
        $pedidos = Pedido::all();
    
        foreach ($pedidos as $pedido) {
            $produtosPedido = json_decode($pedido->produtos, true);
    
            if (is_array($produtosPedido)) {
                foreach ($produtosPedido as $produto) {
                    $produtoData = Produto::where('name', $produto['name'])->first();
    
                    if ($produtoData) {
                        $produtoId = $produtoData->id;
                        $quantidade = $produto['quantidade'] ?? 1;
    
                        if (isset($produtoVendas[$produtoId])) {
                            $produtoVendas[$produtoId]['quantidade'] += $quantidade;
                            $produtoVendas[$produtoId]['datas'][] = $pedido->created_at;
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
    
        foreach ($produtoVendas as $produtoId => $dados) {
            $datas = $dados['datas'];
            $totalCompras = count($datas);
    
            if ($totalCompras > 1) {
                sort($datas);
                $diferencas = [];
                for ($i = 1; $i < $totalCompras; $i++) {
                    $dataAnterior = new \DateTime($datas[$i - 1]);
                    $dataAtual = new \DateTime($datas[$i]);
                    $diferencas[] = $dataAtual->diff($dataAnterior)->days;
                }
                $tempoMedioDias = array_sum($diferencas) / count($diferencas);
            } else {
                $tempoMedioDias = 0; 
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
                    'total_vendas' => $totalVendas, 
                    'tempo_medio_dias' => $tempoMedioDias,
                ];
            }),
        ], 200);
    }
    


    // Método para excluir um produto
    public function destroy(AuthRequest $request, $id)
    {
        DB::beginTransaction();  
        try {
            $produto = Produto::findOrFail($id);
            $produto->delete();

            DB::commit();  

            return response()->json([
                'status' => true,
                'message' => "Produto excluído com sucesso!",
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();  
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    // Método para atualizar um produto
    public function update(AuthRequest $request)
    {
        DB::beginTransaction(); 
        try {
            $produto = Produto::findOrFail($request->id);

            if (isset($request->status)) {
                $produto->status = $produto->status === "Ativo" ? "Suspenso" : "Ativo";
            } else {
                $produto->name = $request->name;
                $produto->quantidade = $request->quantidade;
                $produto->preco = $request->preco;
                $produto->marca_id = $request->marcaID;
            }

            $produto->save();
            DB::commit(); 
            return response()->json([
                'status' => true,
                'produto' => $produto,
                'message' => "Nome do Produto atualizado com sucesso!",
            ], 200);
        } catch (Exception $e) {
            DB::rollBack(); 
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    // Método para criar um novo produto
    public function store(AuthRequest $request)
    {
        DB::beginTransaction();  
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
                    'marca_id' => $marcaID,  
                    'quantidade' => $request->quantidade,
                    'preco' => $request->valor,
                    'status' => 'Ativo'
                ]);
            }
            $produto->save();

            Compra::create([
                'produto_id' => $produto->id,
                'total' => $request->preco * $request->quantidade,
                'valor' => $request->preco,
                'quantidade' => $request->quantidade
            ]);
            DB::commit(); 

            return response()->json([
                'status' => true,
                'produto' => $produto,
                'message' => 'Produto cadastrado com sucesso!',
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();  
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
                'total_compras' => $produto->total_compras ?? 0, 
            ];
        });

        return response()->json([
            'status' => true,
            'produtos' => $produtos,
        ], 200);
    }






}
