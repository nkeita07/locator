<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HistoriqueStock;

class HistoriqueStockController extends Controller
{
    // HistoriqueController.php

public function index(Request $request)
{
    $query = HistoriqueStock::query();

    // filtres existants
    if ($request->reference) {
        $query->where('reference_article', $request->reference);
    }

    if ($request->zone) {
        $query->where('zone', $request->zone);
    }

    if ($request->action) {
        $query->where('action_type', $request->action);
    }

    if ($request->date_start) {
        $query->whereDate('created_at', '>=', $request->date_start);
    }

    if ($request->date_end) {
        $query->whereDate('created_at', '<=', $request->date_end);
    }

    if ($request->taux_min) {
        $query->where('taux_adressage', '>=', $request->taux_min);
    }

    $historiques = $query
        ->orderByDesc('created_at')
        ->paginate(25);

    // 🔥 DASHBOARD DATA
    $stockAdresse = $query->sum('stock_total_adresse');
    $stockArticle = $query->sum('stock_total_article');

    $tauxGlobal = $stockArticle > 0
        ? round(($stockAdresse / $stockArticle) * 100, 2)
        : 0;

    return view('historique.index', compact(
        'historiques',
        'tauxGlobal'
    ));
}

}
