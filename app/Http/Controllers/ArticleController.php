<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Adresser;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ArticleController extends Controller
{
    /**
     * Autocomplétion : référence OU désignation (10 max)
     */
    public function autocomplete(string $query): JsonResponse
    {
        try {
            $search = trim($query);

            if (mb_strlen($search) < 2) {
                return response()->json([]);
            }

            $articles = Article::where('reference', 'LIKE', "%{$search}%")
                ->orWhere('designation', 'LIKE', "%{$search}%")
                ->select('reference', 'designation', 'image')
                ->limit(10)
                ->get();

            return response()->json($articles);

        } catch (\Exception $e) {
            Log::error("Erreur autocomplete : " . $e->getMessage());
            return response()->json([], 500);
        }
    }

    /**
     * Search :
     * 1) référence exacte
     * 2) sinon désignation partielle (1er résultat)
     */
    public function search(string $query): JsonResponse
    {
        try {
            $clean = trim($query);

            if ($clean === '') {
                return response()->json(['error' => 'Paramètre vide.'], 422);
            }

            $article = Article::where('reference', $clean)->first();

            if (!$article) {
                $article = Article::where('designation', 'LIKE', "%{$clean}%")->first();
            }

            if (!$article) {
                return response()->json(['error' => 'Article non trouvé.'], 404);
            }

            $zones = Adresser::where('id_article', $article->id_article)
                ->join('adresse', 'adresse.id_adresse', '=', 'adresser.id_adresse')
                ->select('adresse.zone', 'adresser.stock')
                ->orderBy('adresse.zone')
                ->get();

            return response()->json([
                'id_article'  => $article->id_article,
                'reference'   => $article->reference,
                'designation' => $article->designation,
                'image'       => $article->image ?: asset('images/default.jpg'),
                'stock_total' => $article->stock,
                'zones'       => $zones,
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur recherche article : " . $e->getMessage());
            return response()->json(['error' => 'Erreur interne du serveur'], 500);
        }
    }
}
