<?php

namespace App\Http\Controllers;

use App\Models\Adresse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdresseController extends Controller
{
    /**
     * Affiche l'interface "Adresser un article".
     * Utilise la vue resources/views/adresses/index.blade.php
     */
    public function index()
    {
        return view('adresses.index');
    }

    /**
     * 🔎 Auto-complétion des zones
     * GET /api/adresse/search/{zone}
     */
    public function searchZone(string $zone): JsonResponse
    {
        $zones = Adresse::where('zone', 'LIKE', $zone.'%')
                        ->select('zone')
                        ->limit(10)
                        ->get();

        return response()->json($zones, 200);
    }
}
