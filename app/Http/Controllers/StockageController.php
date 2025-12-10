<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use App\Models\Article;
use App\Models\Adresse;
use App\Models\Adresser;

class StockageController extends Controller
{
    private function authorizeLogisticien(Request $request)
    {
        $user = $request->user();

        if (!$user->roles()->whereIn('libelle', ['admin', 'logisticien'])->exists()) {
            abort(403, "Vous n'avez pas les permissions pour effectuer cette opération.");
        }
    }

    /**
     * Validation de zone (étape 2)
     * NE DOIT PAS EXIGER la quantité.
     */
    public function adresserArticle(Request $request): JsonResponse
    {
        $this->authorizeLogisticien($request);

        $request->validate([
            'reference' => 'required|string',
            'zone'      => 'required|string',
        ]);

        $article = Article::where('reference', $request->reference)->first();
        if (!$article) {
            return response()->json(['error' => "Article non trouvé."], 404);
        }

        $adresse = Adresse::where('zone', $request->zone)->first();
        if (!$adresse) {
            return response()->json(['error' => "Zone inconnue."], 404);
        }

        return response()->json([
            'success' => true,
            'message' => "Zone validée.",
            'article' => $article,
            'adresse' => $adresse
        ], 200);
    }

    /**
     * Mise à jour du stock : + ou - 
     */
    public function miseAJourStock(Request $request): JsonResponse
    {
        $this->authorizeLogisticien($request);

        $request->validate([
            'reference' => 'required|string',
            'zone'      => 'required|string',
            'quantite'  => 'required|integer', // IMPORTANT : PLUS DE min:1
        ]);

        DB::beginTransaction();

        try {
            $article = Article::where('reference', $request->reference)
                              ->lockForUpdate()
                              ->first();

            if (!$article) {
                return response()->json(['error' => "Article non trouvé."], 404);
            }

            $adresse = Adresse::where('zone', $request->zone)->first();
            if (!$adresse) {
                return response()->json(['error' => "Zone non trouvée."], 404);
            }

            // Trouver ou créer l’adressage
            $adressage = Adresser::firstOrCreate(
                ['id_article' => $article->id_article, 'id_adresse' => $adresse->id_adresse],
                ['stock' => 0]
            );

            $nouveauStockZone = $adressage->stock + $request->quantite;

            if ($nouveauStockZone < 0) {
                return response()->json(['error' => "Stock insuffisant dans cette zone."], 422);
            }

            // Nouveau stock total adressé
            $totalActuel = Adresser::where('id_article', $article->id_article)->sum('stock');
            $totalApres = $totalActuel + $request->quantite;

            if ($totalApres > $article->stock) {
                return response()->json(['error' => "Impossible : dépassement du stock global de l’article."], 422);
            }

            // Mise à jour de la zone
            $adressage->stock = $nouveauStockZone;
            $adressage->date_update = now();
            $adressage->save();

            DB::commit();

            return response()->json([
                'success'  => true,
                'message'  => "Stock mis à jour.",
                'zone_stock' => $adressage->stock,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error'   => "Erreur interne.",
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
