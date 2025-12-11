<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\StockageController;
use App\Models\Adresse;

/*
|--------------------------------------------------------------------------
| TEST API
|--------------------------------------------------------------------------
*/
Route::get('/test-api', fn() => response()->json(['status' => 'API OK']));


/*
|--------------------------------------------------------------------------
| AUTOCOMPLÉTION ARTICLES
| → recherche par référence OU nom
|--------------------------------------------------------------------------
*/
Route::get('/article/autocomplete/{query}', [ArticleController::class, 'autocomplete']);


/*
|--------------------------------------------------------------------------
| RECHERCHE ARTICLE PRINCIPALE
| → par référence exacte
| → sinon par nom
|--------------------------------------------------------------------------
*/
Route::get('/article/search/{query}', [ArticleController::class, 'search']);



/*
|--------------------------------------------------------------------------
| AUTOCOMPLÉTION DE ZONES
|--------------------------------------------------------------------------
*/
Route::get('/adresse/search/{zone}', function ($zone) {
    return Adresse::where('zone', 'LIKE', $zone . '%')
        ->select('zone')
        ->get();
});


/*
|--------------------------------------------------------------------------
| STOCKAGE
|--------------------------------------------------------------------------
*/
Route::post('/stockage/adresser', [StockageController::class, 'adresserArticle']);
Route::post('/stockage/miseAJourStock', [StockageController::class, 'miseAJourStock']);
