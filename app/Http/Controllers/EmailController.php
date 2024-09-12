<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use Http;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    
    public function verify(AuthRequest $request)
    {
        $email = $request->input('email');
        
        $apiKey = env('EMAILABLE_API_KEY');
        $response = Http::get('https://api.emailable.com/v1/verify', [
            'email' => $email,
            'api_key' => $apiKey,
        ]);

        return $response->json();
    }
}
