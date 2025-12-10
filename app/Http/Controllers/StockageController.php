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
    /**
     * TEMPORAIRE : on désactive le contrôle du rôle
     * pour que l'API fonctionne sans erreur 403.
     * Tu pourras le réactiver plus tard si besoin.
     */
    private function authorizeLogisticien(Request $request)
    {
        return; // on laisse tout passer pour l’instant
    }

    /**
     * ✅ Validation de la zone pour un article :
     * - vérifie que l'article existe
     * - vérifie que la zone existe
     * - crée l'adressage (stock = 0 si nouveau)
     */
    public function adresserArticle(Request $request): JsonResponse
    {
        $this->authorizeLogisticien($request);

        try {
            $data = $request->validate([
                'reference' => 'required|string|max:100',
                'zone'      => 'required|string|max:100',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        // ARTICLE
        $article = Article::where('reference', $data['reference'])->first();
        if (!$article) {
            return response()->json(['error' => "Article non trouvé."], 404);
        }

        // ZONE
        $adresse = Adresse::where('zone', $data['zone'])->first();
        if (!$adresse) {
            return response()->json(['error' => "Zone de stock inconnue."], 404);
        }

        // CRÉATION / RÉCUPÉRATION DE L'ADRESSAGE
        $adressage = Adresser::firstOrCreate(
            ['id_article' => $article->id_article, 'id_adresse' => $adresse->id_adresse],
            ['stock' => 0]
        );

        return response()->json([
            'success'      => true,
            'message'      => "Zone validée pour cet article.",
            'adressage_id' => $adressage->id_adresser,
        ], 200);
    }

    /**
     * ✅ Mise à jour du stock d'un article sur une zone
     * POST /api/stockage/miseAJourStock
     */
    public function miseAJourStock(Request $request): JsonResponse
    {
        $this->authorizeLogisticien($request);

        try {
            $data = $request->validate([
                'reference' => 'required|string|max:100',
                'zone'      => 'required|string|max:100',
                'quantite'  => 'required|integer|min:1',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        DB::beginTransaction();

        try {
            // Article
            $article = Article::where('reference', $data['reference'])->lockForUpdate()->first();
            if (!$article) {
                return response()->json(['error' => "Article non trouvé."], 404);
            }

            // Zone
            $adresse = Adresse::where('zone', $data['zone'])->first();
            if (!$adresse) {
                return response()->json(['error' => "Zone non trouvée."], 404);
            }

            // Adressage
            $adressage = Adresser::firstOrCreate(
                ['id_article' => $article->id_article, 'id_adresse' => $adresse->id_adresse],
                ['stock' => 0]
            );

            // MAJ du stock dans la zone
            $adressage->stock += $data['quantite'];
            $adressage->date_update = now();
            $adressage->save();

            // Recalcul du stock total
            $totalStock = Adresser::where('id_article', $article->id_article)->sum('stock');
            $article->stock = $totalStock;
            $article->save();

            DB::commit();

            return response()->json([
                'success'             => true,
                'message'             => "Stock mis à jour.",
                'zone_stock'          => $adressage->stock,
                'stock_total_article' => $totalStock,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error'   => "Erreur interne.",
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
