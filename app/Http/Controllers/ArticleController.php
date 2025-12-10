<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Adresser;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ArticleController extends Controller
{
    /**
     * Recherche complète d'un article :
     * - référence exacte
     * - retourne id, designation, image, stock total
     * - retourne ses emplacements (zones + stock)
     */
    public function searchDesignationByReference(string $reference): JsonResponse
    {
        $cleanedReference = trim($reference);

        try {
            // 🎯 1. Recherche exacte obligatoire
            $article = Article::where('reference', $cleanedReference)->first();

            if (!$article) {
                return response()->json([
                    'error' => 'Article non trouvé.'
                ], 404);
            }

            // 🎯 2. Récupérer les zones déjà adressées
            $zones = Adresser::where('id_article', $article->id_article)
                ->join('adresse', 'adresse.id_adresse', '=', 'adresser.id_adresse')
                ->select('adresse.zone', 'adresser.stock')
                ->orderBy('adresse.zone')
                ->get();

            // 🎯 3. Formatage de la réponse
            return response()->json([
                'id_article'    => $article->id_article,
                'reference'     => $article->reference,
                'designation'   => $article->designation,
                'image'         => $article->image,
                'stock_total'   => $article->stock,
                'zones'         => $zones,
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur recherche article : ' . $e->getMessage());

            return response()->json([
                'error' => 'Erreur interne du serveur'
            ], 500);
        }
    }
}
