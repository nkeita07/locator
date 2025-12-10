<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\StockageController;
use Illuminate\Support\Facades\Route;
use App\Models\Adresse;

/*
|--------------------------------------------------------------------------
| Route de test API (publique)
|--------------------------------------------------------------------------
*/
Route::get('/test-api', function () {
    return response()->json(['status' => 'API OK']);
});

/*
|--------------------------------------------------------------------------
| Routes API (pour JavaScript / fetch)
| → on les laisse SANS auth pour le moment, pour débugger plus simple
|--------------------------------------------------------------------------
*/

// Recherche de désignation par référence
Route::get('/article/search/{reference}', [ArticleController::class, 'searchDesignationByReference']);

// Autocomplétion zones de stockage
Route::get('/adresse/search/{zone}', function ($zone) {
    return Adresse::where('zone', 'LIKE', $zone . '%')
        ->select('zone')
        ->get();
});

// Adresser un article à une zone
Route::post('/stockage/adresser', [StockageController::class, 'adresserArticle']);

// Mise à jour du stock
Route::post('/stockage/miseAJourStock', [StockageController::class, 'miseAJourStock']);
