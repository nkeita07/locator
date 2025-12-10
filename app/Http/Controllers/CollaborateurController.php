<?php

namespace App\Http\Controllers;

use App\Models\Collaborateur;
use App\Models\RoleCollaborateur;
use App\Models\Magasin;
use Illuminate\Http\Request;

class CollaborateurController extends Controller
{
    /**
     * Affiche la liste des collaborateurs.
     * GET /collaborateurs
     */
    public function index()
    {
        $collaborateurs = Collaborateur::with(['role', 'magasin'])
                                     ->orderBy('nom')
                                     ->paginate(20);
        return view('collaborateurs.index', compact('collaborateurs'));
    }

    // ... (Ajouter les méthodes CRUD complètes)
    // NOTE: La méthode create() nécessitera de charger les Magasins et les Rôles pour les listes déroulantes.
}