<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\Models\Article;
use App\Models\Adresse;
use App\Models\Adresser;
use App\Models\HistoriqueStock;

class StockageController extends Controller
{
    /**
     * Autorisation : admin / logisticien uniquement
     */
    private function authorizeLogisticien(Request $request): void
    {
        $user = $request->user();

        if (
            !$user ||
            !$user->roles()
                ->whereIn('libelle', ['admin', 'logisticien'])
                ->exists()
        ) {
            abort(403, "Vous n'avez pas les permissions pour effectuer cette opération.");
        }
    }

    /**
     * ------------------------------------------------------------------
     * 1) VALIDATION DE ZONE (ADRESSAGE)
     * ------------------------------------------------------------------
     * → Ne modifie PAS le stock
     * → Sert à valider la zone avant dépôt
     * → Historisé comme ADRESSAGE
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

        // Stock total déjà adressé (avant toute action)
        $totalAdresse = Adresser::where('id_article', $article->id_article)->sum('stock');

        $taux = $article->stock > 0
            ? round(($totalAdresse / $article->stock) * 100, 2)
            : 0;

        return response()->json([
            'success' => true,
            'message' => "Zone validée.",
            'article' => $article,
            'adresse' => $adresse
        ], 200);
    }

    /**
     * ------------------------------------------------------------------
     * 2) MISE À JOUR DU STOCK (+ / -)
     * ------------------------------------------------------------------
     * → Ajout / retrait de stock par zone
     * → Historisé (ADD / REMOVE)
     */
    public function miseAJourStock(Request $request): JsonResponse
    {
        $this->authorizeLogisticien($request);

        $request->validate([
            'reference' => 'required|string',
            'zone'      => 'required|string',
            'quantite'  => 'required|integer', // peut être négatif
        ]);

        DB::beginTransaction();

        try {
            // 🔒 Lock article
            $article = Article::where('reference', $request->reference)
                ->lockForUpdate()
                ->first();

            if (!$article) {
                DB::rollBack();
                return response()->json(['error' => "Article non trouvé."], 404);
            }

            $adresse = Adresse::where('zone', $request->zone)->first();
            if (!$adresse) {
                DB::rollBack();
                return response()->json(['error' => "Zone non trouvée."], 404);
            }

            // Récupération ou création de l’adressage
            $adressage = Adresser::firstOrCreate(
                [
                    'id_article' => $article->id_article,
                    'id_adresse' => $adresse->id_adresse
                ],
                ['stock' => 0]
            );

            // Stocks avant
            $stockAvantZone = $adressage->stock;
            $totalAvant = Adresser::where('id_article', $article->id_article)->sum('stock');

            // Calculs
            $nouveauStockZone = $stockAvantZone + $request->quantite;
            $totalApres = $totalAvant + $request->quantite;

            if ($nouveauStockZone < 0) {
                DB::rollBack();
                return response()->json(['error' => "Stock insuffisant dans cette zone."], 422);
            }

            if ($totalApres > $article->stock) {
                DB::rollBack();
                return response()->json(['error' => "Dépassement du stock global de l’article."], 422);
            }

            // 🔹 MAJ ZONE
            $adressage->stock = $nouveauStockZone;
            $adressage->date_update = now();
            $adressage->save();

            // Taux après
            $taux = $article->stock > 0
                ? round(($totalApres / $article->stock) * 100, 2)
                : 0;

            $actionType = $request->quantite > 0 ? 'ADD' : 'REMOVE';

            // 🔹 HISTORIQUE
            HistoriqueStock::create([
                'id_article'            => $article->id_article,
                'reference_article'     => $article->reference,
                'designation_article'   => $article->designation,
                'zone'                  => $adresse->zone,
                'action_type'           => $actionType,
                'quantite'              => abs($request->quantite),
                'stock_avant'           => $totalAvant,
                'stock_apres'           => $totalApres,
                'stock_total_article'   => $article->stock,
                'stock_total_adresse'   => $totalApres,
                'taux_adressage'        => $taux,
                'id_collaborateur'      => $request->user()->id_collaborateur,
                'nom_collaborateur'     => $request->user()->feid ?? null,
                'role_collaborateur'    => $request->user()->roles()->pluck('libelle')->implode(','),
            ]);

            DB::commit();

            return response()->json([
                'success'    => true,
                'message'    => "Stock mis à jour.",
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
