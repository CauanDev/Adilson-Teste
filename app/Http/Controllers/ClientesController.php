<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Models\Cliente;
use App\Models\Venda;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ClientesController extends Controller
{
    // Método para listar todos os clientes
    public function index(AuthRequest $request)
    {
        // Recupera todos os clientes com seus pedidos e calcula a idade
        $clientes = Cliente::with('pedidos')  // Inclui pedidos relacionados
            ->get()
            ->map(function ($cliente) {
                // Adiciona a idade calculada ao cliente
                $cliente->idade = Carbon::parse($cliente->data_nasc)->age;
                return $cliente;
            });

        // Retorna a lista de clientes em formato JSON
        return response()->json([
            'status' => true,
            'clientes' => $clientes,
        ], 200);
    }

    // Método para obter detalhes de um cliente específico pelo ID
    public function details(AuthRequest $request, $id)
    {
        // Recupera o cliente com base no ID fornecido
        $cliente = Cliente::where('id', $id)->get();

        // Retorna os detalhes do cliente em formato JSON
        return response()->json([
            'status' => true,
            'cliente' => $cliente,
        ], 200);
    }

    // Método para filtrar clientes com base em parâmetros fornecidos
    public function filter(Request $request)
    {
        // Inicializa a query base para clientes
        $clientes = Cliente::query();

        // Aplica filtros conforme os parâmetros fornecidos na requisição
        if (isset($request->nome)) {
            $clientes->where('nome', 'LIKE', '%' . $request->nome . '%');
        }
        if (isset($request->status)) {
            $clientes->where('status', $request->status);
        }
        if (isset($request->tipo) && $request->tipo !== 'all') {
            $clientes->where('tipo', $request->tipo);

        }
        $clientes = $clientes->selectRaw('*, DATE_PART(\'year\', AGE(data_nasc)) as idade');

        if (isset($request->idadeMin)) {
            $clientes->whereRaw('DATE_PART(\'year\', AGE(data_nasc)) > ?', [$request->idadeMin]);
        }

        if (isset($request->idadeMax)) {
            $clientes->whereRaw('DATE_PART(\'year\', AGE(data_nasc)) < ?', [$request->idadeMax]);
        }
        if (isset($request->dataMinima)) {
            $clientes->where('created_at', '>', $request->dataMinima);
        }
        if (isset($request->dataMaxima)) {
            $clientes->where('created_at', '<', $request->dataMaxima);
        }
        // Ajusta filtro por sexo
        if (isset($request->sexo) && $request->sexo !== 'all') {
            $clientes->where('sexo', $request->sexo);
        }

        // Aplica ordenação conforme o parâmetro fornecido
        if (isset($request->ordem)) {
            switch ($request->ordem) {
                case 'maisVendas':
                    // Ordena os clientes por número de pedidos em ordem decrescente
                    $clientes
                        ->select('clientes.*', DB::raw('COUNT(pedidos.id) as pedidos_count'))
                        ->join('pedidos', 'clientes.id', '=', 'pedidos.cliente_id')  // INNER JOIN para garantir que apenas clientes com pedidos sejam incluídos
                        ->groupBy('clientes.id')
                        ->orderBy('pedidos_count', 'desc');  // Ordena pela contagem de pedidos em ordem decrescente
                    break;

                case 'menosVendas':
                    // Ordena os clientes por número de pedidos em ordem crescente
                    $clientes
                        ->select('clientes.*', DB::raw('COUNT(pedidos.id) as pedidos_count'))
                        ->leftJoin('pedidos', 'clientes.id', '=', 'pedidos.cliente_id')
                        ->groupBy('clientes.id')
                        ->orderBy('pedidos_count', 'asc');  // Ordena pela contagem de pedidos em ordem crescente
                    break;

                case 'OrdemAlfabetica':
                    // Ordena os clientes por nome em ordem alfabética
                    $clientes
                        ->orderBy('clientes.nome', 'asc');  // Ordena por nome em ordem alfabética
                    break;

                case 'OrdemIdade':
                    // Ordena os clientes por idade em ordem crescente
                    $clientes
                        ->orderBy('clientes.idade', 'asc');  // Ordena por idade em ordem crescente
                    break;

            }
        }

        // Retorna os resultados filtrados e ordenados como uma coleção
        return response()->json([
            'status' => true,
            'clientes' => $clientes->get(),
        ], 200);
    }

    // Método para criar um novo cliente
    public function store(AuthRequest $request)
    {
        DB::beginTransaction();  // Inicia a transação
        try {


            // Converte a data de nascimento para o formato Y-m-d
            $data_nasc = \Carbon\Carbon::createFromFormat('d/m/Y', $request->data_nasc)->format('Y-m-d');
            // Cria um novo cliente com os dados fornecidos na requisição
            $cliente = Cliente::create([
                'nome' => $request->nome,
                'email' => $request->email,
                'data_nasc' => $data_nasc,
                'sexo' => $request->sexo,
                'tipo' => $request->tipo,
                'status' => "Ativo"  // Define o status inicial como "Ativo"
            ]);

            DB::commit();  // Comita a transação

            // Retorna o cliente criado com uma mensagem de sucesso
            return response()->json([
                'status' => true,
                'cliente' => $cliente,
                'message' => "Cliente cadastrado com sucesso!"
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();  // Reverte a transação em caso de erro
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }


    // Método para atualizar um cliente existente
    public function update(AuthRequest $request)
    {
        DB::beginTransaction();  // Inicia a transação
        try {
            // Recupera o cliente pelo ID fornecido
            $cliente = Cliente::findOrFail($request->id);

            // Atualiza os dados do cliente com base na requisição
            if ($request->status) {
                // Alterna o status entre "Ativo" e "Suspenso"
                $cliente->status = ($cliente->status === "Ativo") ? "Suspenso" : "Ativo";
            } else {
                // Atualiza outras informações do cliente
                $cliente->nome = $request->nome;
                $cliente->tipo = $request->tipo;
                $cliente->email = $request->email;
                $cliente->sexo = $request->sexo;

                // Verifica se a data de nascimento está no formato 'DD/MM/YYYY'
                if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $request->idade)) {
                    // Converte para o formato 'YYYY-MM-DD'
                    $date = \DateTime::createFromFormat('d/m/Y', $request->idade);
                    $cliente->data_nasc = $date->format('Y-m-d');
                } else {
                    // Se a data já estiver em outro formato ou correta, usa-a diretamente
                    $cliente->data_nasc = $request->idade;
                }
            }


            $cliente->save();  // Salva as alterações

            DB::commit();  // Comita a transação

            // Retorna o cliente atualizado com uma mensagem de sucesso
            return response()->json([
                'status' => true,
                'fornecedor' => $cliente,
                'message' => "Cliente atualizado com sucesso!",
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();  // Reverte a transação em caso de erro
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    // Método para excluir um cliente
    public function destroy(AuthRequest $request, $id)
    {
        DB::beginTransaction();  // Inicia a transação
        try {
            // Recupera o cliente pelo ID fornecido
            $cliente = Cliente::findOrFail($id);
            $cliente->delete();  // Exclui o cliente

            DB::commit();  // Comita a transação

            // Retorna uma mensagem de sucesso após a exclusão
            return response()->json([
                'status' => true,
                'message' => "Cliente excluído com sucesso!",
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();  // Reverte a transação em caso de erro
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
