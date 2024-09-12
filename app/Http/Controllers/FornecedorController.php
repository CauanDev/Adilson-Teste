<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use App\Models\Fornecedor;
use App\Models\Produto;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;

class FornecedorController extends Controller
{
    // Método para listar todos os fornecedores
    public function index(AuthRequest $request)
    {
        $fornecedores = Fornecedor::with('marcas')->orderBy('id', 'DESC')->get();
    
        $fornecedoresComMarcas = $fornecedores->map(function($fornecedor) {
            return [
                'id' => $fornecedor->id,
                'nome' => $fornecedor->name,
                'email' => $fornecedor->email,
                'created_at'=>$fornecedor->created_at, 
                'status'=>$fornecedor->status, 
                'marcas' => $fornecedor->marcas->map(function($marca) {
                    return [
                        'id' => $marca->id,
                        'nome' => $marca->nome,
                        'status' => $marca->status,
                        'segmento' => $marca->segmento,
                        'produtos' => $marca->produtos,
                        'created_at' => $marca->created_at,
                    ];
                }),
            ];
        });
    
        return response()->json([
            'status' => true,
            'fornecedores' => $fornecedoresComMarcas,
        ], 200);
    }
    

    // Método para criar um novo fornecedor
    public function store(AuthRequest $request)
    {
        DB::beginTransaction();
        try
        {
            $fornecedor = Fornecedor::create([
                'name' => $request->name,
                'email' => $request->email,
                'status' => "Ativo"
            ]);

            DB::commit();  

            return response()->json([
                'status' => true,
                'fornecedor' => $fornecedor,
                'message' => "Fornecedor cadastrado com sucesso!",
            ], 201);
        }
        catch(Exception $e)
        {
            DB::rollBack();  
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    // Método para filtrar fornecedores com base em parâmetros fornecidos
    public function filter(AuthRequest $request)
    {
        if (isset($request->fornecedorID)) {
            $produtos = Produto::where('fornecedor_id', $request->fornecedorID)->get();
            return response()->json([
                'status' => true,
                'produtos' => $produtos,
            ], 201);
        }
        
        $query = Fornecedor::query();
        
        if (isset($request->nome)) {
            $query->where('name', $request->nome);
        }
        if (isset($request->dataMinima)) {
            $query->where('created_at', '>', $request->dataMinima);
        }
        if (isset($request->dataMaxima)) {
            $query->where('created_at', '<', $request->dataMaxima);
        }
        if (isset($request->status)) {
            $query->where('status', $request->status);
        }
        if (isset($request->produto)) {
            // Filtra fornecedores com base nos produtos associados
            $produtoIds = Produto::where('name', 'like', '%' . $request->produto . '%')->pluck('fornecedor_id');
            $query->whereIn('id', $produtoIds);
        }

        return response()->json([
            'status' => true,
            'fornecedores' => $query->get(),
        ], 201);
    }

    // Método para atualizar o status de um fornecedor
    public function updateStatus(AuthRequest $request, $id)
    {
        DB::beginTransaction();  // Inicia a transação
        try
        {
            $fornecedor = Fornecedor::findOrFail($id);    
            $fornecedor->status = ($fornecedor->status === 'Ativo') ? 'Suspenso' : 'Ativo';    
            $fornecedor->save();
    
            DB::commit();  
    
            return response()->json([
                'status' => true,
                'fornecedor' => $fornecedor,
                'message' => "Status do fornecedor atualizado com sucesso!",
            ], 200);
        }
        catch(Exception $e)
        {
            DB::rollBack();  
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
     
    // Método para atualizar o nome de um fornecedor
    public function update(AuthRequest $request)
    {
        DB::beginTransaction();  // Inicia a transação
        try
        {
            $fornecedor = Fornecedor::findOrFail($request->id);
            $fornecedor->name = $request->name; 
            $fornecedor->email = $request->email; 
            $fornecedor->save();
    
            DB::commit();
    
            return response()->json([
                'status' => true,
                'fornecedor' => $fornecedor,
                'message' => "Nome do fornecedor atualizado com sucesso!",
            ], 200);
        }
        catch(Exception $e)
        {
            DB::rollBack(); 
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    // Método para obter detalhes de um fornecedor específico pelo ID
    public function details(AuthRequest $request, $id)
    {
        DB::beginTransaction(); 
        $fornecedor = Fornecedor::findOrFail($id);  
        DB::commit(); 

        return response()->json([
            'status' => true,
            'fornecedor' => $fornecedor,
            'message' => "Fornecedor obtido com sucesso!",
        ], 200);
    }

    // Método para excluir um fornecedor
    public function destroy(AuthRequest $request, $id)
    {
        DB::beginTransaction();  
        try
        {
            $fornecedor = Fornecedor::findOrFail($id);
            $fornecedor->delete();  

            DB::commit();  

            return response()->json([
                'status' => true,
                'message' => "Fornecedor excluído com sucesso!",
            ], 200);
        }
        catch(Exception $e)
        {
            DB::rollBack();  
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
