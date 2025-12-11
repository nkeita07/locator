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
     * ----------------------------------------------------------------------
     *  AUTOCOMPLÉTION
     *  Recherche par référence OU désignation — renvoie 10 résultats max
     * ----------------------------------------------------------------------
     */
    public function autocomplete(string $query): JsonResponse
    {
        try {
            $search = trim($query);

            if (strlen($search) < 2) {
                return response()->json([]);
            }

            $articles = Article::where('reference', 'LIKE', "%{$search}%")
                ->orWhere('designation', 'LIKE', "%{$search}%")
                ->select('reference', 'designation', 'image')
                ->limit(10)
                ->get()
                ->map(function ($a) {
                    return [
                        'reference'   => $a->reference,
                        'designation' => $a->designation,
                        'image'       => $a->image ?: asset('images/default.jpg'),
                    ];
                });

            return response()->json($articles);

        } catch (\Exception $e) {
            Log::error("Erreur autocomplete : " . $e->getMessage());
            return response()->json([], 500);
        }
    }


    /**
     * ----------------------------------------------------------------------
     *  RECHERCHE PRINCIPALE
     *  Recherche par :
     *  - Référence exacte
     *  - Sinon par désignation partielle
     * ----------------------------------------------------------------------
     */
    public function search(string $query): JsonResponse
    {
        try {
            $clean = trim($query);

            // 1) Recherche exacte sur la référence
            $article = Article::where('reference', $clean)->first();

            // 2) Sinon recherche sur la désignation
            if (!$article) {
                $article = Article::where('designation', 'LIKE', "%{$clean}%")->first();
            }

            if (!$article) {
                return response()->json([
                    'error' => 'Article non trouvé.'
                ], 404);
            }

            // 3) Stock adressé par zone
            $zones = Adresser::where('id_article', $article->id_article)
                ->join('adresse', 'adresse.id_adresse', '=', 'adresser.id_adresse')
                ->select('adresse.zone', 'adresser.stock')
                ->orderBy('adresse.zone')
                ->get();

            // 4) Image fallback automatique
            $image = $article->image ?: asset('images/default.jpg');

            return response()->json([
                'id_article'    => $article->id_article,
                'reference'     => $article->reference,
                'designation'   => $article->designation,
                'image'         => $image,
                'stock_total'   => $article->stock,
                'zones'         => $zones,
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur recherche article : " . $e->getMessage());

            return response()->json([
                'error' => 'Erreur interne du serveur'
            ], 500);
        }
    }
}
