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
     * Vérifie que l'utilisateur est logisticien ou admin.
     */
    private function authorizeLogisticien(Request $request)
    {
        $user = $request->user();

        if (!$user || !$user->roles()->whereIn('libelle', ['admin', 'logisticien'])->exists()) {
            abort(403, "Vous n'avez pas les permissions pour effectuer cette opération.");
        }
    }

    /**
     * Validation de la zone + existence de l'article
     * (ne touche PAS au stock)
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
            return response()->json(['error' => $e->errors()], 422);
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

        return response()->json([
            'success' => true,
            'article' => $article,
            'adresse' => $adresse,
            'message' => "Adresse valide pour l'article.",
        ], 200);
    }

    /**
     * Mise à jour du stock d'un article sur une zone.
     *
     * Règles:
     * - On NE modifie PAS article.stock (stock global ERP).
     * - La somme des stocks adressés (toutes zones) NE DOIT PAS
     *   dépasser article.stock.
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
            // 1. Article + lock pour éviter les race conditions
            $article = Article::where('reference', $data['reference'])
                ->lockForUpdate()
                ->first();

            if (!$article) {
                DB::rollBack();
                return response()->json(['error' => "Article non trouvé."], 404);
            }

            // 2. Adresse
            $adresse = Adresse::where('zone', $data['zone'])->first();
            if (!$adresse) {
                DB::rollBack();
                return response()->json(['error' => "Zone non trouvée."], 404);
            }

            // 3. Adressage (ligne zone ↔ article)
            $adressage = Adresser::firstOrCreate(
                ['id_article' => $article->id_article, 'id_adresse' => $adresse->id_adresse],
                ['stock' => 0]
            );

            // 4. Total déjà adressé sur TOUTES les zones
            $totalDejaAdresse = Adresser::where('id_article', $article->id_article)->sum('stock');

            // Stock global théorique (table article)
            $stockGlobal = (int) $article->stock;

            // Stock encore disponible à adresser
            $stockDisponible = $stockGlobal - $totalDejaAdresse;

            if ($stockDisponible <= 0) {
                DB::rollBack();
                return response()->json([
                    'error'   => "Aucun stock disponible à adresser pour cet article.",
                    'details' => [
                        'stock_article'     => $stockGlobal,
                        'deja_adresse'      => $totalDejaAdresse,
                        'reste_disponible'  => 0,
                    ],
                ], 422);
            }

            // 5. Vérifier que la quantité demandée ne dépasse pas le disponible
            if ($data['quantite'] > $stockDisponible) {
                DB::rollBack();
                return response()->json([
                    'error'   => "Quantité supérieure au stock disponible.",
                    'details' => [
                        'stock_article'     => $stockGlobal,
                        'deja_adresse'      => $totalDejaAdresse,
                        'reste_disponible'  => $stockDisponible,
                        'quantite_demandee' => $data['quantite'],
                    ],
                ], 422);
            }

            // 6. Mise à jour DU STOCK ZONE UNIQUEMENT
            $adressage->stock += $data['quantite'];
            $adressage->date_update = now();
            $adressage->save();

            // Nouveau total adressé après l'opération
            $nouveauTotalAdresse = $totalDejaAdresse + $data['quantite'];
            $resteApres = $stockGlobal - $nouveauTotalAdresse;

            // ⛔️ ON NE TOUCHE PAS A $article->stock !!
            // $article->stock reste le stock global réel.

            DB::commit();

            return response()->json([
                'success'               => true,
                'message'               => "Stock adressé avec succès.",
                'zone'                  => $adresse->zone,
                'zone_stock'            => $adressage->stock,
                'stock_article'         => $stockGlobal,
                'stock_total_adresse'   => $nouveauTotalAdresse,
                'stock_restant_a_adresser' => $resteApres,
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
