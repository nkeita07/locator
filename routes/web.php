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

    // -------- Dashboard Principale --------
    Route::get('/dashboard', function () {
        return view('dashboard.home');
    })->name('dashboard.home');

    // -------- CRUD --------
    Route::resource('articles', ArticleController::class);
    Route::resource('collaborateurs', CollaborateurController::class);
    Route::resource('paniers', PanierController::class)->except(['destroy']);

    // -------- Adressage --------
    Route::get('/adresser', [AdresseController::class, 'index'])->name('article.location');
    Route::get('/adresser-article', [AdresseController::class, 'index'])->name('adresses.index');

    // -------- Zones --------
    Route::get('/zones', function () {
        return view('zones.index');
    })->name('zones.index');

    // -------- Historique --------
    Route::get('/historique', function () {
        return view('historique.index');
    })->name('historique.index');

    // -------- API internes --------
    Route::post('/api/stockage/adresser', [StockageController::class, 'adresserArticle']);
    Route::post('/api/stockage/miseAJourStock', [StockageController::class, 'miseAJourStock']);
});
