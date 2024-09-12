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
        $clientes = Cliente::with('pedidos')
            ->get()
            ->map(function ($cliente) {
                $cliente->idade = Carbon::parse($cliente->data_nasc)->age;
                return $cliente;
            });

        return response()->json([
            'status' => true,
            'clientes' => $clientes,
        ], 200);
    }

    // Método para obter detalhes de um cliente específico pelo ID
    public function details(AuthRequest $request, $id)
    {
        $cliente = Cliente::where('id', $id)->get();

        return response()->json([
            'status' => true,
            'cliente' => $cliente,
        ], 200);
    }

    // Método para filtrar clientes com base em parâmetros fornecidos
    public function filter(Request $request)
    {
        $clientes = Cliente::query();

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
        if (isset($request->sexo) && $request->sexo !== 'all') {
            $clientes->where('sexo', $request->sexo);
        }

        return response()->json([
            'status' => true,
            'clientes' => $clientes->get(),
        ], 200);
    }

    // Método para criar um novo cliente
    public function store(AuthRequest $request)
    {
        DB::beginTransaction();
        try {


            $data_nasc = Carbon::createFromFormat('d/m/Y', $request->data_nasc)->format('Y-m-d');

            $cliente = Cliente::create([
                'nome' => $request->nome,
                'email' => $request->email,
                'data_nasc' => $data_nasc,
                'sexo' => $request->sexo,
                'tipo' => $request->tipo,
                'status' => "Ativo"
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'cliente' => $cliente,
                'message' => "Cliente cadastrado com sucesso!"
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }


    // Método para atualizar um cliente existente
    public function update(AuthRequest $request)
    {
        DB::beginTransaction();
        try {
            $cliente = Cliente::findOrFail($request->id);

            if ($request->status) {
                $cliente->status = ($cliente->status === "Ativo") ? "Suspenso" : "Ativo";
            } else {
                $cliente->nome = $request->nome;
                $cliente->tipo = $request->tipo;
                $cliente->email = $request->email;
                $cliente->sexo = $request->sexo;

                // Verifica se a data de nascimento está no formato 'DD/MM/YYYY'
                if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $request->idade)) {
                    $date = \DateTime::createFromFormat('d/m/Y', $request->idade);
                    $cliente->data_nasc = $date->format('Y-m-d');
                } else {
                    $cliente->data_nasc = $request->idade;
                }
            }


            $cliente->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'fornecedor' => $cliente,
                'message' => "Cliente atualizado com sucesso!",
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    // Método para excluir um cliente
    public function destroy(AuthRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $cliente = Cliente::findOrFail($id);
            $cliente->delete();  // Exclui o cliente

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => "Cliente excluído com sucesso!",
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
