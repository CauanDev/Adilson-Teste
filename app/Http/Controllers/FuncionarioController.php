<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Models\Funcionario;
use DB;
use Exception;
use Illuminate\Http\Request;

class FuncionarioController extends Controller
{
    // Método para listar todos os funcionários com a quantidade de pedidos
    public function index(AuthRequest $request)
    {
        try {
            // Recupera todos os funcionários ordenados por ID em ordem decrescente
            $funcionarios = Funcionario::withCount('pedidos') // Usa o método withCount para incluir a contagem de pedidos
                ->orderBy('id', 'DESC')
                ->get();

            // Retorna a lista de funcionários com a quantidade de pedidos em formato JSON
            return response()->json([
                'status' => true,
                'funcionarios' => $funcionarios,
            ], 200);
        } catch (Exception $e) {
            // Em caso de erro, retorna uma mensagem de erro
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Mostra a quantidade de funcionários por sexo com base nos pedidos.
     *
     * @return \Illuminate\Http\Response
     */
    public function countBySexoInPedidos()
    {
        // Chama o método estático do modelo Funcionario para contar funcionários por sexo em pedidos
        $funcionariosPorSexo = Funcionario::countFuncionariosBySexoInPedidos();

        // Retorna os dados em formato JSON
        return response()->json($funcionariosPorSexo);
    }

    // Método para obter detalhes de um funcionário específico pelo ID
    public function details(AuthRequest $request, $id)
    {
        // Recupera o funcionário pelo ID fornecido
        $funcionario = Funcionario::where('id', $id)->get();

        // Retorna os detalhes do funcionário em formato JSON
        return response()->json([
            'status' => true,
            'funcionario' => $funcionario,
        ], 200);
    }

    // Método para atualizar um funcionário
    public function update(AuthRequest $request)
    {
        DB::beginTransaction();  // Inicia a transação
        try {
            // Busca o funcionário pelo ID fornecido
            $funcionario = Funcionario::findOrFail($request->id);

            // Alterna o status se o parâmetro 'status' estiver presente
            if (isset($request->status)) {
                $funcionario->status = $funcionario->status === "Ativo" ? "Suspenso" : "Ativo";
            } else {
                // Verifica e ajusta o formato da data se necessário
                $dataNasc = $request->data_nasc;
                if ($this->isValidDateFormat($dataNasc)) {
                    $funcionario->data_nasc = $this->convertDateToDatabaseFormat($dataNasc);
                } else {
                    throw new Exception('Data de nascimento está no formato incorreto.');
                }

                // Atualiza outros campos do funcionário
                $funcionario->nome = $request->nome;
                $funcionario->email = $request->email;
                $funcionario->salario = $request->salario;
                $funcionario->sexo = $request->sexo;
            }

            // Salva as mudanças
            $funcionario->save();
            DB::commit();  // Comita a transação

            // Retorna o funcionário atualizado com uma mensagem de sucesso
            return response()->json([
                'status' => true,
                'funcionario' => $funcionario,
                'message' => "Funcionario atualizado com sucesso!",
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();  // Reverte a transação em caso de erro
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    private function isValidDateFormat($date)
    {
        // Verifica se a data está no formato 'dd/mm/yyyy'
        $d = \DateTime::createFromFormat('d/m/Y', $date);
        return $d && $d->format('d/m/Y') === $date;
    }

    private function convertDateToDatabaseFormat($date)
    {
        // Converte a data do formato 'dd/mm/yyyy' para 'yyyy-mm-dd'
        $d = \DateTime::createFromFormat('d/m/Y', $date);
        return $d ? $d->format('Y-m-d') : null;
    }


    // Método para criar um novo funcionário
    public function store(AuthRequest $request)
    {
        DB::beginTransaction();  // Inicia a transação
        try {
            // Cria um novo funcionário com os dados fornecidos na requisição
            $funcionario = Funcionario::create([
                'nome' => $request->nome,
                'email' => $request->email,
                'data_nasc' => $request->data_nasc,
                'salario' => $request->salario,
                'sexo' => $request->sexo,
                'status' => "Ativo"  // Define o status inicial como "Ativo"
            ]);

            DB::commit();  // Comita a transação

            // Retorna o funcionário criado com uma mensagem de sucesso
            return response()->json([
                'status' => true,
                'fornecedor' => $funcionario,
                'message' => "Funcionario cadastrado com sucesso!",
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();  // Reverte a transação em caso de erro
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    // Método para filtrar funcionários com base em parâmetros fornecidos
    public function filter(AuthRequest $request)
    {
        // Inicializa a query base para funcionários
        $funcionarios = Funcionario::query();

        // Aplica filtros conforme os parâmetros fornecidos na requisição
        if (isset($request->nome)) {
            $funcionarios->where('nome', 'LIKE', '%' . $request->nome . '%');
        }
        if (isset($request->status)) {
            $funcionarios->where('status', $request->status);
        }
        $funcionarios = $funcionarios->selectRaw('*, DATE_PART(\'year\', AGE(data_nasc)) as idade');

        if (isset($request->idadeMin)) {
            $funcionarios->whereRaw('DATE_PART(\'year\', AGE(data_nasc)) > ?', [$request->idadeMin]);
        }
        
        if (isset($request->idadeMax)) {
            $funcionarios->whereRaw('DATE_PART(\'year\', AGE(data_nasc)) < ?', [$request->idadeMax]);
        }
        if (isset($request->dataMinima)) {
            $funcionarios->where('created_at', '>', $request->dataMinima);
        }
        if (isset($request->dataMaxima)) {
            $funcionarios->where('created_at', '<', $request->dataMaxima);
        }
        if (isset($request->sexo) && $request->sexo !== 'all') {
            $funcionarios->where('sexo', $request->sexo);
        }
        if (isset($request->salarioMinimo)) {
            $funcionarios->where('salario', '>', $request->salarioMinimo);
        }
        if (isset($request->salarioMaximo)) {
            $funcionarios->where('salario', '<', $request->salarioMaximo);
        }
        if (isset($request->ordem)) {
            switch ($request->ordem) {
                case 'maisVendas':
                    $funcionarios->select('funcionarios.*', DB::raw('COUNT(pedidos.id) as pedidos_count'))
                        ->leftJoin('pedidos', 'funcionarios.id', '=', 'pedidos.funcionario_id')  // JUNÇÃO À ESQUERDA para incluir todos os funcionários, mesmo aqueles sem pedidos
                        ->groupBy('funcionarios.id')  // Agrupa pelos IDs dos funcionários
                        ->orderBy(DB::raw('COUNT(pedidos.id)'), 'desc');  // Ordena pela contagem de pedidos em ordem decrescente
                    break;

                case 'menosVendas':
                    $funcionarios->select('funcionarios.*', DB::raw('COUNT(pedidos.id) as pedidos_count'))
                        ->leftJoin('pedidos', 'funcionarios.id', '=', 'pedidos.funcionario_id')  // JUNÇÃO À ESQUERDA para incluir todos os funcionários, mesmo aqueles sem pedidos
                        ->groupBy('funcionarios.id')  // Agrupa pelos IDs dos funcionários
                        ->orderBy(DB::raw('COUNT(pedidos.id)'), 'asc');  // Ordena pela contagem de pedidos em ordem crescente
                    break;

                case 'OrdemAlfabetica':
                    $funcionarios->orderBy('nome', 'asc');  // Ordena por nome em ordem alfabética
                    break;

                case 'OrdemIdade':
                    $funcionarios->orderBy('idade', 'asc');  // Ordena por idade em ordem crescente
                    break;

                case 'OrdemSalario':
                    $funcionarios->orderBy('salario', 'asc');  // Ordena por salário em ordem crescente
                    break;
            }
        }

        // Retorna os funcionários filtrados e ordenados como uma coleção
        return response()->json([
            'status' => true,
            'funcionarios' => $funcionarios->get(),
        ], 200);
    }

    // Método para excluir um funcionário
    public function destroy(AuthRequest $request, $id)
    {
        DB::beginTransaction();  // Inicia a transação
        try {
            // Busca o funcionário pelo ID fornecido
            $funcionario = Funcionario::findOrFail($id);
            $funcionario->delete();  // Exclui o funcionário

            DB::commit();  // Comita a transação

            // Retorna uma mensagem de sucesso após a exclusão
            return response()->json([
                'status' => true,
                'message' => "Funcionario excluído com sucesso!",
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
