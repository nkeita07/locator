<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\IamAuthController;
use App\Http\Controllers\StockageController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AdresseController;
use App\Http\Controllers\CollaborateurController;
use App\Http\Controllers\PanierController;
use App\Http\Controllers\HistoriqueController;

/*
|--------------------------------------------------------------------------
| Routes publiques
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => view('welcome'))->name('welcome');

// Auth IAM
Route::get('/login', [IamAuthController::class, 'redirectToProvider'])->name('login');
Route::get('/login/iam/callback', [IamAuthController::class, 'handleProviderCallback']);
Route::get('/login/error', fn() => view('auth.error'))->name('login.error');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('welcome');
})->name('logout');

/*
|--------------------------------------------------------------------------
| Routes protégées (auth)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', fn() => view('dashboard.home'))->name('dashboard.home');

    // CRUD
    Route::resource('articles', ArticleController::class);
    Route::resource('collaborateurs', CollaborateurController::class);
    Route::resource('paniers', PanierController::class)->except(['destroy']);

    // Adressage
    Route::get('/adresser', [AdresseController::class, 'index'])->name('article.location');

    // Zones (assure-toi que cette route existe bien car ton dashboard l'appelle)
    Route::get('/zones', fn() => view('zones.index'))->name('zones.index');

    // Historique
    Route::get('/historique', [HistoriqueController::class, 'index'])->name('historique.index');

    // Listes dashboard
    Route::get('/historique/articles-non-adresses', [HistoriqueController::class, 'articlesNonAdresses'])
        ->name('historique.articles.non_adresses');

    Route::get('/historique/articles-sur-stockes', [HistoriqueController::class, 'articlesSurStockes'])
        ->name('historique.articles.sur_stockes');

    // Export Excel (table filtrée)
    Route::get('/historique/export-excel', [HistoriqueController::class, 'exportExcel'])
        ->name('historique.export.excel');

    // API internes (POST)
    Route::post('/api/stockage/adresser', [StockageController::class, 'adresserArticle']);
    Route::post('/api/stockage/miseAJourStock', [StockageController::class, 'miseAJourStock']);

    // Historique
Route::get('/historique', [HistoriqueController::class, 'index'])
    ->name('historique.index');

Route::get('/historique/articles-non-adresses', [HistoriqueController::class, 'articlesNonAdresses'])
    ->name('historique.non_adresses');

Route::get('/historique/articles-sur-stockes', [HistoriqueController::class, 'articlesSurStockes'])
    ->name('historique.sur_stockes');

});
