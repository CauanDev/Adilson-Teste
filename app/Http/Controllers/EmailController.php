<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use Http;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    
    public function verify(AuthRequest $request)
    {
        // Obtém o email do request
        $email = $request->input('email');
        
        // Obtém a chave da API do arquivo .env
        $apiKey = env('EMAILABLE_API_KEY');
        // Faz a requisição para a API Emailable
        $response = Http::get('https://api.emailable.com/v1/verify', [
            'email' => $email,
            'api_key' => $apiKey,
        ]);

        // Retorna a resposta da API para o frontend
        return $response->json();
    }
}
