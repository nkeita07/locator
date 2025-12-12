<?php

namespace App\Http\Controllers;

use App\Models\Adresse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AdresseController extends Controller
{
    /**
     * ----------------------------------------------------------------------
     * Affiche la page "Adresser un article"
     * Vue : resources/views/adresses/index.blade.php
     * ----------------------------------------------------------------------
     */
    public function index()
    {
        $user = Auth::user();

        /**
         * Sécurité :
         * - par défaut : pas d'accès à l'adressage
         * - seuls les rôles admin ou logisticien sont autorisés
         */
        $canAddress = false;

        if ($user && method_exists($user, 'hasAnyRole')) {
            $canAddress = $user->hasAnyRole(['admin', 'logisticien']);
        }

        return view('adresses.index', [
            'canAddress' => $canAddress
        ]);
    }

    /**
     * ----------------------------------------------------------------------
     * 🔎 Autocomplétion des zones
     * GET /api/adresse/search/{zone}
     * ----------------------------------------------------------------------
     */
    public function searchZone(string $zone): JsonResponse
    {
        $zones = Adresse::where('zone', 'LIKE', $zone . '%')
            ->select('zone')
            ->orderBy('zone')
            ->limit(20)
            ->get();

        return response()->json($zones);
    }
}
