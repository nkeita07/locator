<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Adresser;

class EmplacementArticleController extends Controller
{
    /**
     * Page de recherche d’article.
     */
    public function index()
    {
        return view('articles.emplacement.search');
    }

    /**
     * Affiche l’emplacement d’un article.
     */
    public function show(string $reference)
    {
        // 1. Trouver article
        $article = Article::where('reference', $reference)->first();

        if (!$article) {
            abort(404, "Aucun article trouvé pour la référence {$reference}.");
        }

        // 2. Récupérer les adressages
        $adressages = Adresser::where('id_article', $article->id_article)
                              ->with('adresse')
                              ->get();

        // Stock total adressé
        $stockTotalAdresses = $adressages->sum('stock');

        // Stock global
        $stockGlobal = $article->stock;

        // 3. Vue
        return view('articles.emplacement.show', compact(
            'article',
            'adressages',
            'stockTotalAdresses',
            'stockGlobal'
        ));
    }
}
