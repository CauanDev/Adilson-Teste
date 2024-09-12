<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Models\Funcionario;
use DB;
use Exception;
use Illuminate\Http\Request;

class FuncionarioController extends Controller
{



    // Funcao nao utilizada mais
    // public function countBySexoInPedidos()
    // {
    //     // Chama o método estático do modelo Funcionario para contar funcionários por sexo em pedidos
    //     $funcionariosPorSexo = Funcionario::countFuncionariosBySexoInPedidos();

    //     // Retorna os dados em formato JSON
    //     return response()->json($funcionariosPorSexo);
    // }



    // Método para listar todos os funcionários com a quantidade de pedidos
    public function index(AuthRequest $request)
    {
        try {
            $funcionarios = Funcionario::withCount('pedidos')
                ->orderBy('id', 'DESC')
                ->get();

            return response()->json([
                'status' => true,
                'funcionarios' => $funcionarios,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }



    // Método para obter detalhes de um funcionário específico pelo ID
    public function details(AuthRequest $request, $id)
    {
        $funcionario = Funcionario::where('id', $id)->get();

        return response()->json([
            'status' => true,
            'funcionario' => $funcionario,
        ], 200);
    }

    // Método para atualizar um funcionário
    public function update(AuthRequest $request)
    {
        DB::beginTransaction();
        try {
            $funcionario = Funcionario::findOrFail($request->id);

            if (isset($request->status)) {
                $funcionario->status = $funcionario->status === "Ativo" ? "Suspenso" : "Ativo";
            } else {
                $dataNasc = $request->data_nasc;
                if ($this->isValidDateFormat($dataNasc)) {
                    $funcionario->data_nasc = $this->convertDateToDatabaseFormat($dataNasc);
                } else {
                    throw new Exception('Data de nascimento está no formato incorreto.');
                }

                $funcionario->nome = $request->nome;
                $funcionario->email = $request->email;
                $funcionario->salario = $request->salario;
                $funcionario->sexo = $request->sexo;
            }

            $funcionario->save();
            DB::commit();

            return response()->json([
                'status' => true,
                'funcionario' => $funcionario,
                'message' => "Funcionario atualizado com sucesso!",
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
    // Verifica se a data está no formato 'dd/mm/yyyy'
    private function isValidDateFormat($date)
    {
        $d = \DateTime::createFromFormat('d/m/Y', $date);
        return $d && $d->format('d/m/Y') === $date;
    }
    // Converte a data do formato 'dd/mm/yyyy' para 'yyyy-mm-dd'
    private function convertDateToDatabaseFormat($date)
    {
        $d = \DateTime::createFromFormat('d/m/Y', $date);
        return $d ? $d->format('Y-m-d') : null;
    }


    // Método para criar um novo funcionário
    public function store(AuthRequest $request)
    {
        DB::beginTransaction();
        try {
            $funcionario = Funcionario::create([
                'nome' => $request->nome,
                'email' => $request->email,
                'data_nasc' => $request->data_nasc,
                'salario' => $request->salario,
                'sexo' => $request->sexo,
                'status' => "Ativo" 
            ]);

            DB::commit();  

            return response()->json([
                'status' => true,
                'fornecedor' => $funcionario,
                'message' => "Funcionario cadastrado com sucesso!",
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    // Método para filtrar funcionários com base em parâmetros fornecidos
    public function filter(AuthRequest $request)
    {
        $funcionarios = Funcionario::query();

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

        return response()->json([
            'status' => true,
            'funcionarios' => $funcionarios->get(),
        ], 200);
    }

    // Método para excluir um funcionário
    public function destroy(AuthRequest $request, $id)
    {
        DB::beginTransaction(); 
        try {
            $funcionario = Funcionario::findOrFail($id);
            $funcionario->delete();  

            DB::commit(); 

            return response()->json([
                'status' => true,
                'message' => "Funcionario excluído com sucesso!",
            ], 200);
        } catch (Exception $e) {
            DB::rollBack(); 
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
