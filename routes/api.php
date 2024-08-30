<?php

use App\Http\Controllers\ClientesController;
use App\Http\Controllers\ComprasController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\FornecedorController;
use App\Http\Controllers\FuncionarioController;
use App\Http\Controllers\MarcasController;
use App\Http\Controllers\PedidosController;
use App\Http\Controllers\ProdutosController;
use App\Http\Controllers\VendasController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rotas de Pedidos
Route::get('/pedidos', [PedidosController::class, 'index']); // Retorna todos os pedidos
Route::post('/filter-pedidos', [PedidosController::class, 'filter']); // Filtra pedidos com base nos parâmetros fornecidos
Route::post('/store-pedido', [PedidosController::class, 'store']); // Cria um novo pedido
Route::post('/update-pedido', [PedidosController::class, 'update']); // Atualiza um pedido existente
Route::delete('/pedido/{id}', [PedidosController::class, 'destroy']); // Exclui um pedido pelo ID
Route::get('/clientes/por-sexo', [PedidosController::class, 'countClientesBySexo']); // Contagem de clientes por sexo

// Rotas de Clientes
Route::get('/clientes', [ClientesController::class, 'index']); // Retorna todos os clientes
Route::post('/filter-cliente', [ClientesController::class, 'filter']); // Filtra clientes com base nos parâmetros fornecidos
Route::post('/store-cliente', [ClientesController::class, 'store']); // Cria um novo cliente
Route::post('/update-cliente', [ClientesController::class, 'update']); // Atualiza um cliente existente
Route::delete('/cliente/{id}', [ClientesController::class, 'destroy']); // Exclui um cliente pelo ID
Route::get('/cliente/{id}', [ClientesController::class, 'details']); // Retorna detalhes de um cliente pelo ID

// Rotas de Funcionários
Route::get('/funcionarios', [FuncionarioController::class, 'index']); // Retorna todos os funcionários
Route::post('/filter-funcionario', [FuncionarioController::class, 'filter']); // Filtra funcionários com base nos parâmetros fornecidos
Route::post('/store-funcionario', [FuncionarioController::class, 'store']); // Cria um novo funcionário
Route::post('/update-funcionario', [FuncionarioController::class, 'update']); // Atualiza um funcionário existente
Route::delete('/funcionario/{id}', [FuncionarioController::class, 'destroy']); // Exclui um funcionário pelo ID
Route::get('/funcionario/{id}', [FuncionarioController::class, 'details']); // Retorna detalhes de um funcionário pelo ID
Route::get('/funcionarios/por-sexo', [FuncionarioController::class, 'countBySexoInPedidos']); // Contagem de funcionários por sexo

// Rotas de Produtos
Route::get('/produtos', [ProdutosController::class, 'index']); // Retorna todos os produtos
Route::post('/filter-produto', [ProdutosController::class, 'filter']); // Filtra produtos com base nos parâmetros fornecidos
Route::post('/store-produto', [ProdutosController::class, 'store']); // Cria um novo produto
Route::post('/update-produto', [ProdutosController::class, 'update']); // Atualiza um produto existente
Route::delete('/produto/{id}', [ProdutosController::class, 'destroy']); // Exclui um produto pelo ID
Route::post('/media-pedidos', [ProdutosController::class, 'averagePurchaseTime']); // Retorna o tempo médio de compra de um produto

// Rotas de Fornecedores
Route::get('/fornecedores', [FornecedorController::class, 'index']); // Retorna todos os fornecedores
Route::post('/filter-fornecedor', [FornecedorController::class, 'filter']); // Filtra fornecedores com base nos parâmetros fornecidos
Route::post('/store-fornecedor', [FornecedorController::class, 'store']); // Cria um novo fornecedor
Route::post('/update-fornecedor', [FornecedorController::class, 'update']); // Atualiza um fornecedor existente
Route::get('/status-fornecedor/{id}', [FornecedorController::class, 'updateStatus']); // Atualiza o status de um fornecedor
Route::delete('/fornecedor/{id}', [FornecedorController::class, 'destroy']); // Exclui um fornecedor pelo ID
Route::get('/fornecedor/{id}', [FornecedorController::class, 'details']); // Retorna detalhes de um fornecedor pelo ID



Route::post('/store-marca', [MarcasController::class, 'store']);
Route::get('/marcas', [MarcasController::class, 'index']);
Route::post('/update-marca', [MarcasController::class, 'update']);
Route::post('/filter-marca', [MarcasController::class, 'filter']);
Route::delete('/marca/{id}', [MarcasController::class, 'destroy']);

Route::get('/compras',[ComprasController::class,'index']);


Route::post('/verify-email',[EmailController::class,'verify']);