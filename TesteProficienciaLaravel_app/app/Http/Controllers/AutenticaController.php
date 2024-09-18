<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AutenticaController extends Controller
{
    

    public function index() {
        return view('autentica.index');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // Autenticação foi bem-sucedida
            return redirect()->intended('/pedidos');
        }

        // Redireciona de volta com uma mensagem de erro
        return redirect()->back()->withErrors(['email' => 'Credenciais inválidas']);
    }

    public function logout() {
        Auth::logout();
        return redirect('/login');
    }

}
