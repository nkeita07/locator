<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\HistoriqueStock;
use App\Models\Article;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\HistoriqueStockExport;


class HistoriqueController extends Controller
{
    public function index(Request $request)
    {
        /* ===============================
         * 1) DASHBOARD
         * =============================== */
        $stockTotal = (int) DB::table('article')->sum('stock');
        $stockAdresse = (int) DB::table('adresser')->sum('stock');

        $tauxGlobal = $stockTotal > 0 ? round(($stockAdresse / $stockTotal) * 100, 2) : 0;

        // articles adressés = articles ayant SUM(adresser.stock) > 0
        $articlesAdresses = Article::whereIn('id_article', function ($q) {
            $q->from('adresser')
              ->select('id_article')
              ->groupBy('id_article')
              ->havingRaw('SUM(stock) > 0');
        })->count();

        // articles non adressés = pas dans la liste ci-dessus
        $articlesNonAdresses = Article::whereNotIn('id_article', function ($q) {
            $q->from('adresser')
              ->select('id_article')
              ->groupBy('id_article')
              ->havingRaw('SUM(stock) > 0');
        })->count();

        // articles sur-stockés = SUM(adresser.stock) > article.stock
        $articlesSurStockes = Article::whereIn('id_article', function ($q) {
            $q->from('adresser')
              ->join('article', 'article.id_article', '=', 'adresser.id_article')
              ->select('adresser.id_article')
              ->groupBy('adresser.id_article', 'article.stock')
              ->havingRaw('SUM(adresser.stock) > article.stock');
        })->count();


        /* ===============================
         * 2) TABLE HISTORIQUE (filtrable)
         * =============================== */
        $query = HistoriqueStock::query()->orderByDesc('created_at');

        if ($request->filled('reference')) {
            $query->where('reference_article', $request->reference);
        }

        if ($request->filled('zone')) {
            $query->where('zone', $request->zone);
        }

        if ($request->filled('action')) {
            $query->where('action_type', $request->action);
        }

        if ($request->filled('date_start')) {
            $query->whereDate('created_at', '>=', $request->date_start);
        }

        if ($request->filled('date_end')) {
            $query->whereDate('created_at', '<=', $request->date_end);
        }

        if ($request->filled('taux_min')) {
            $query->where('taux_adressage', '>=', (float) $request->taux_min);
        }

        if ($request->filled('taux_max')) {
            $query->where('taux_adressage', '<=', (float) $request->taux_max);
        }

        // Pagination : on garde les query params, et dans le blade on mettra fragment('table')
        $historiques = $query->paginate(20)->withQueryString();

        return view('historique.index', compact(
            'historiques',
            'stockTotal',
            'stockAdresse',
            'tauxGlobal',
            'articlesAdresses',
            'articlesNonAdresses',
            'articlesSurStockes'
        ));
    }



    /* =========================
     * EXPORT
     * ========================= */
    public function exportExcel(Request $request)
    {
        return Excel::download(
            new HistoriqueStockExport($request),
            'historique_stock.xlsx'
        );
    }


    public function articlesNonAdresses()
{
    $articles = Article::whereNotIn('id_article', function ($q) {
        $q->select('id_article')
          ->from('adresser')
          ->groupBy('id_article')
          ->havingRaw('SUM(stock) > 0');
    })->paginate(20);

    return view('historique.non_adresses', compact('articles'));
}

public function articlesSurStockes()
{
    $articles = Article::join('adresser', 'article.id_article', '=', 'adresser.id_article')
        ->select(
            'article.*',
            DB::raw('SUM(adresser.stock) as stock_adresse')
        )
        ->groupBy('article.id_article', 'article.stock')
        ->havingRaw('SUM(adresser.stock) > article.stock')
        ->paginate(20);

    return view('historique.sur_stockes', compact('articles'));
}

}
