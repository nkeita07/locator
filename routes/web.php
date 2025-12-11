<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\IamAuthController;
use App\Http\Controllers\StockageController;
use App\Http\Controllers\EmplacementArticleController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AdresseController;
use App\Http\Controllers\CollaborateurController;
use App\Http\Controllers\PanierController;
use App\Http\Controllers\ArticleLocationController;

/*
|--------------------------------------------------------------------------
| Routes publiques (sans authentification)
|--------------------------------------------------------------------------
*/

// Accueil
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
| Routes EMPLACEMENT (désactivées temporairement)
|--------------------------------------------------------------------------
| Nous commentons ces routes car la fonctionnalité "Adresser un article"
| remplace totalement "Emplacement" pour l’instant. Cela évite les conflits.
|--------------------------------------------------------------------------
*/

// Route::get('/article/emplacement', [EmplacementArticleController::class, 'index'])
//     ->name('article.location.search');

// Route::get('/article/emplacement/{reference}', [EmplacementArticleController::class, 'show'])
//     ->name('article.location.show');

// Route::get('/article-location', [ArticleLocationController::class, 'index'])
//     ->name('article.location');

/*
|--------------------------------------------------------------------------
| Routes protégées (IAM obligatoire)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', fn() => view('dashboard.main'))->name('dashboard');

    // CRUD
    Route::resource('articles', ArticleController::class);
    //Route::resource('adresses', AdresseController::class);
    Route::resource('collaborateurs', CollaborateurController::class);
    Route::resource('paniers', PanierController::class)->except(['destroy']);

    // Page : Adresser un article
    Route::get('/adresser-article', [AdresseController::class, 'index'])
        ->name('adresses.index');

    // API internes
    Route::post('/api/stockage/adresser', [StockageController::class, 'adresserArticle']);
    Route::post('/api/stockage/miseAJourStock', [StockageController::class, 'miseAJourStock']);

    Route::get('/adresser-article', [\App\Http\Controllers\AdresseController::class, 'index'])
    ->name('article.location');

    // Page principale d’adressage
Route::get('/adresser', [\App\Http\Controllers\AdresseController::class, 'index'])
    ->name('article.location');

// Page Zones d’adressage (même si tu ne l’as pas encore)
Route::get('/zones', function () {
    return view('zones.index');
})->name('zones.index');

// Page Historique (même si vide)
Route::get('/historique', function () {
    return view('historique.index');
})->name('historique.index');

});
