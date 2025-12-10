<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Gère la tentative de connexion de l'utilisateur.
     */
    public function authenticate(Request $request) // <--- C'est cette ligne qui doit être exacte !
    {
        // 1. Validation des champs entrants
        $credentials = $request->validate([
            'profil' => ['required', 'email'], 
            'password' => ['required'],
        ]);

        // 2. Tentative de connexion
        if (Auth::attempt([
            'email' => $credentials['profil'], 
            'password' => $credentials['password']
        ])) {
            
            // Connexion réussie : Régénérer la session et rediriger
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard')); 
        }

        // 3. Échec de la connexion
        throw ValidationException::withMessages([
            'profil' => ['Les identifiants fournis ne correspondent pas à nos enregistrements.'],
        ]);
    }
}