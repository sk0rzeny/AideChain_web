<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AideDistribuee;
use App\Models\Beneficiaire;
use App\Services\BlockchainService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BeneficiaireController extends Controller
{
    public function __construct(private readonly BlockchainService $blockchain) {}

    public function check(Request $request): JsonResponse
    {
        $request->validate([
            'prenom'         => 'required|string',
            'nom'            => 'required|string',
            'date_naissance' => 'required|date_format:Y-m-d',
        ]);

        $hash         = $this->blockchain->computeHash($request->prenom, $request->nom, $request->date_naissance);
        $beneficiaire = $this->blockchain->findBeneficiaire($hash);

        if (!$beneficiaire) {
            return response()->json([
                'data' => ['found' => false],
            ]);
        }

        $aideActive = AideDistribuee::with(['typeAide', 'ong'])
            ->where('beneficiaire_id', $beneficiaire->id)
            ->where('date_expiration', '>', now())
            ->first();

        return response()->json([
            'data' => [
                'found'        => true,
                'beneficiaire' => [
                    'id'        => $beneficiaire->id,
                    'prenom'    => $beneficiaire->prenom,
                    'nom'       => $beneficiaire->nom,
                    'categorie' => $beneficiaire->categorie,
                ],
                'doublon' => $aideActive ? [
                    'type'       => $aideActive->typeAide->nom,
                    'ong'        => $aideActive->ong->nom,
                    'expiration' => $aideActive->date_expiration->format('Y-m-d'),
                ] : null,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'prenom'         => 'required|string|max:100',
            'nom'            => 'required|string|max:100',
            'date_naissance' => 'required|date_format:Y-m-d|before:today',
            'genre'          => 'required|in:homme,femme,autre',
            'categorie'      => 'required|in:individu,famille,enfant,femme_chef_menage,deplacement_interne',
            'notes'          => 'nullable|string|max:500',
        ]);

        $ong  = $request->user()->ong;
        $hash = $this->blockchain->computeHash($request->prenom, $request->nom, $request->date_naissance);

        $existing = $this->blockchain->findBeneficiaire($hash);

        if ($existing) {
            return response()->json([
                'data' => [
                    'beneficiaire' => $existing->only(['id', 'prenom', 'nom', 'categorie', 'genre']),
                    'created'      => false,
                ],
            ], 200);
        }

        $beneficiaire = Beneficiaire::create([
            'identity_hash'  => $hash,
            'prenom'         => $request->prenom,
            'nom'            => $request->nom,
            'date_naissance' => $request->date_naissance,
            'genre'          => $request->genre,
            'categorie'      => $request->categorie,
            'ong_id'         => $ong->id,
            'notes'          => $request->notes,
        ]);

        return response()->json([
            'data' => [
                'beneficiaire' => $beneficiaire->only(['id', 'prenom', 'nom', 'categorie', 'genre']),
                'created'      => true,
            ],
        ], 201);
    }
}
