<?php

namespace App\Services\Socialite;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;
use Illuminate\Support\Arr;

class IamProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * Les URLs OIDC pour Decathlon IAM.
     */
    protected $authUrl = 'https://idpdecathlon.oxylane.com/as/authorization.oauth2';
    protected $tokenUrl = 'https://idpdecathlon.oxylane.com/as/token.oauth2';
    protected $userInfoUrl = 'https://idpdecathlon.oxylane.com/idp/userinfo.openid';

    /**
     * Les portées par défaut (permissions)
     */
    protected $scopes = ['profile', 'email', 'openid'];

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        // Construction de l'URL de redirection vers IAM
        return $this->buildAuthUrlFromBase($this->authUrl, $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        // URL pour obtenir le jeton d'accès
        return $this->tokenUrl;
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        // Récupérer les informations utilisateur après l'obtention du jeton
        $response = $this->getHttpClient()->get($this->userInfoUrl, [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        // Mappage des données de l'utilisateur IAM vers l'objet User de Socialite
        return (new User)->setRaw($user)->map([
            'id'    => Arr::get($user, 'sub'), // 'sub' est l'identifiant unique dans OIDC
            'name'  => Arr::get($user, 'name'),
            'email' => Arr::get($user, 'email'),
            // Tu peux ajouter d'autres champs Decathlon si nécessaire (FEID, rôle, etc.)
        ]);
    }
}