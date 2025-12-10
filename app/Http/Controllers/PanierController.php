<?php

namespace App\Http\Controllers;

use App\Models\Panier;
use Illuminate\Http\Request;

class PanierController extends Controller
{
    /**
     * Affiche la liste des paniers de prélèvement.
     * GET /paniers
     */
    public function index()
    {
        $paniers = Panier::with(['collaborateur', 'magasin', 'etat'])
                       ->latest('date_creation')
                       ->paginate(20);
        return view('paniers.index', compact('paniers'));
    }

    // ... (Ajouter ici la logique pour créer un panier, ajouter des lignes, valider, etc.)
}