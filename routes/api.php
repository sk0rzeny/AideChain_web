<?php

use App\Http\Controllers\Api\AideController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BeneficiaireController;
use App\Models\ProjetAide;
use App\Models\TypeAide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Auth — public
Route::post('/login', [AuthController::class, 'login']);

// Routes protégées — agent terrain authentifié + rattaché à une ONG
Route::middleware(['auth:sanctum', 'agent.ong'])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    Route::get('/types-aide', fn () => response()->json([
        'data' => TypeAide::orderBy('nom')->get(['id', 'nom', 'description']),
    ]));

    Route::get('/projets', function (Request $request) {
        $ongId = $request->user()->ong->id;

        $projets = ProjetAide::with('typeAide')
            ->where('ong_id', $ongId)
            ->where('statut', 'active')
            ->where('date_expiration', '>', now())
            ->orderBy('nom')
            ->get()
            ->map(fn ($p) => [
                'id'              => $p->id,
                'nom'             => $p->nom,
                'type_aide_id'    => $p->type_aide_id,
                'type_aide'       => $p->typeAide->nom,
                'date_expiration' => $p->date_expiration->format('Y-m-d'),
                'zone_cible'      => $p->zone_cible,
            ]);

        return response()->json(['data' => $projets]);
    });

    Route::get('/beneficiaires/check', [BeneficiaireController::class, 'check']);
    Route::post('/beneficiaires',      [BeneficiaireController::class, 'store']);

    Route::post('/aides', [AideController::class, 'store']);
});
