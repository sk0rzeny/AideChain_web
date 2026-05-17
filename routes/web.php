<?php

use App\Http\Controllers\DashboardController;
use App\Livewire\ChoisirOng;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::get('/map/regions', [\App\Http\Controllers\MapController::class, 'regions'])->name('map.regions');

Route::post('/locale/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'fr', 'ar'])) {
        session(['locale' => $locale]);
    }

    return back();
})->name('locale');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Agent terrain — sélection de l'ONG
    Route::middleware('role:ong_agent')
        ->get('/choisir-ong', ChoisirOng::class)
        ->name('choisir-ong');

    // Coordination — super_admin + ong_representant
    Route::middleware('role:super_admin,ong_representant')
        ->get('/coordination', \App\Livewire\Coordination\DashboardCoordination::class)
        ->name('coordination');

    // Routes admin (contenu vient en Bloc 1)
    Route::middleware('role:super_admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            // Bloc 1
        });

    // Routes ONG — représentant uniquement
    Route::middleware('role:ong_representant')
        ->prefix('ong')
        ->name('ong.')
        ->group(function () {
            Route::get('/inscription', \App\Livewire\Ong\EnregistrerOng::class)->name('inscription');
            Route::get('/projets/creer', \App\Livewire\Ong\CreerProjetAide::class)->name('projets.creer');
        });

    // Routes ONG — représentant ET agent (agent doit avoir une ONG)
    Route::middleware('role:ong_representant,ong_agent')
        ->prefix('ong')
        ->name('ong.')
        ->group(function () {
            Route::get('/beneficiaires/nouveau', \App\Livewire\Ong\EnregistrerBeneficiaire::class)->name('beneficiaires.nouveau');
            Route::get('/aides/nouvelle', \App\Livewire\Ong\EnregistrerAide::class)->name('aides.nouvelle');
        });

    // Routes terrain (contenu vient en Bloc 3)
    Route::middleware('role:ong_agent')
        ->prefix('terrain')
        ->name('terrain.')
        ->group(function () {
            // Bloc 3
        });
});

require __DIR__.'/settings.php';
