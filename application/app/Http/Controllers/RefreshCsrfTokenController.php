<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RefreshCsrfTokenController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $request->session()->regenerateToken();
        return new Response();
    }
}
