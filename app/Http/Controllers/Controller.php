<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Http; // Importa o cliente HTTP do Laravel

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
