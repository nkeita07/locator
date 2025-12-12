<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\StockageController;
use App\Models\Adresse;

/*
|--------------------------------------------------------------------------|
| Test API
|--------------------------------------------------------------------------|
*/
Route::get('/test-api', fn() => response()->json(['status' => 'API OK']));

/*
|--------------------------------------------------------------------------|
| Articles - Autocomplete (AVANT search)
|--------------------------------------------------------------------------|
*/
Route::get('/article/autocomplete/{query}', [ArticleController::class, 'autocomplete'])
    ->where('query', '.*');

/*
|--------------------------------------------------------------------------|
| Articles - Search (référence exacte, sinon désignation partielle)
|--------------------------------------------------------------------------|
*/
Route::get('/article/search/{query}', [ArticleController::class, 'search'])
    ->where('query', '.*');

/*
|--------------------------------------------------------------------------|
| Zones - Autocomplete
|--------------------------------------------------------------------------|
*/
Route::get('/adresse/search/{zone}', function ($zone) {
    return Adresse::where('zone', 'LIKE', $zone . '%')
        ->select('zone')
        ->limit(20)
        ->get();
});

/*
|--------------------------------------------------------------------------|
| Stockage
|--------------------------------------------------------------------------|
*/
Route::post('/stockage/adresser', [StockageController::class, 'adresserArticle']);
Route::post('/stockage/miseAJourStock', [StockageController::class, 'miseAJourStock']);
