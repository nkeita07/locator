<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Collaborateur;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class IamAuthController extends Controller
{
    // URLs de Production IAM Decathlon
    protected $authUrl = 'https://idpdecathlon.oxylane.com/as/authorization.oauth2';
    protected $tokenUrl = 'https://idpdecathlon.oxylane.com/as/token.oauth2';
    protected $userinfoUrl = 'https://idpdecathlon.oxylane.com/idp/userinfo.openid';

    /**
     * Étape 1 : Redirige l'utilisateur vers la page de connexion IAM.
     * Route GET /login
     */
    public function redirectToProvider(Request $request)
    {
        $clientId = trim(config('services.dkt_iam.client_id'));
        $redirectUri = trim(config('services.dkt_iam.redirect'));

        if (empty($clientId)) {
             abort(500, 'DKT_IAM_CLIENT_ID est manquant.');
        }

        $state = Str::random(40);
        $request->session()->put('oauth.state', $state); 

        $parameters = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid profile email', 
            'state' => $state,
        ];
        
        return redirect()->away($this->authUrl . '?' . http_build_query($parameters));
    }

    /**
     * Étape 2 : Gère le retour après la connexion IAM (Callback).
     * Route GET /login/iam/callback
     */
    public function handleProviderCallback(Request $request)
    {
        // Supprime le jeton de la session généré avant la redirection (DÉSACTIVATION TEMPORAIRE DU STATE CHECK)
        $request->session()->forget('oauth.state'); 

        // Vérification de base : Le code d'autorisation doit être présent
        if (!$request->has('code')) {
            return redirect()->route('login.error')->withErrors([
                'error' => 'Code OAuth manquant.'
            ]);
        }

        $clientId = trim(config('services.dkt_iam.client_id'));
        $clientSecret = trim(config('services.dkt_iam.client_secret'));
        $redirectUri = trim(config('services.dkt_iam.redirect'));

        // 1. Échange du Code d'autorisation contre un Access Token
        // Utilisation du Basic Auth Header pour l'authentification du client (comme l'application Symfony)
        $responseToken = Http::withHeaders([
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Authorization' => 'Basic ' . base64_encode($clientId . ':' . $clientSecret),
        ])->asForm()->post($this->tokenUrl, [
            'grant_type' => 'authorization_code',
            'code' => $request->code,
            'redirect_uri' => $redirectUri,
        ]);

        if ($responseToken->failed() || !isset($responseToken->json()['access_token'])) {
            return redirect()->route('login.error')->withErrors([
                'error' => $responseToken->json()['error_description'] ?? 'Échec de l\'obtention du jeton.'
            ]);
        }

        $accessToken = $responseToken->json()['access_token'];

        // 2. Récupération des Informations Utilisateur (userinfo.openid)
        $responseUser = Http::withToken($accessToken)->get($this->userinfoUrl);

        if ($responseUser->failed()) {
            return redirect()->route('login.error')->withErrors([
                'error' => 'Impossible de récupérer les informations utilisateur.'
            ]);
        }

        $userData = $responseUser->json();
        
        // 3. Identification et Nettoyage des données IAM
        $identifier = $userData['feid'] ?? $userData['sub'] ?? null;
        $name = trim($userData['name'] ?? $userData['preferred_username'] ?? 'Utilisateur IAM');
        $email = $userData['email'] ?? null;
        $feid = $userData['feid'] ?? null;

        if (!$identifier) {
            return redirect()->route('login.error')->withErrors([
                'error' => 'Aucun identifiant IAM disponible.'
            ]);
        }

        // 4. Création ou mise à jour du collaborateur
        $collaborateur = Collaborateur::updateOrCreate(
            ['feid' => $identifier],
            [
                'name' => $name ?: 'Utilisateur IAM', // Utiliser le nom nettoyé
                'email' => $email, // Doit être null ou non-null, selon la DB
                'password' => Hash::make(Str::random(16)),
            ]
        );
        
        // 5. Connecter le Collaborateur et rompre la boucle
        Auth::login($collaborateur, true);

        // 6. Rediriger vers le tableau de bord (sortie de la boucle)
        return redirect()->route('dashboard'); 
    }
}