<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Beneficiaire;
use App\Models\ProjetAide;
use App\Services\BlockchainService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AideController extends Controller
{
    public function __construct(private readonly BlockchainService $blockchain) {}

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'beneficiaire_id' => 'required|exists:beneficiaires,id',
            'projet_aide_id'  => 'required|exists:projets_aide,id',
            'notes'           => 'nullable|string|max:500',
        ]);

        $ong    = $request->user()->ong;
        $projet = ProjetAide::findOrFail($request->projet_aide_id);

        if ($projet->ong_id !== $ong->id) {
            return response()->json(['message' => 'Ce projet n\'appartient pas à votre ONG.'], 403);
        }

        if ($projet->statut !== 'active' || $projet->date_expiration->isPast()) {
            return response()->json(['message' => 'Ce projet est inactif ou expiré.'], 422);
        }

        $beneficiaire = Beneficiaire::findOrFail($request->beneficiaire_id);

        if ($this->blockchain->isDuplicate($beneficiaire->identity_hash, $projet->type_aide_id)) {
            $info = $this->blockchain->getDuplicateInfo($beneficiaire->identity_hash, $projet->type_aide_id);

            return response()->json([
                'message' => 'Doublon détecté — ce bénéficiaire reçoit déjà cette aide.',
                'doublon' => [
                    'type'       => $info->typeAide->nom,
                    'ong'        => $info->ong->nom,
                    'expiration' => $info->date_expiration->format('Y-m-d'),
                ],
            ], 409);
        }

        $aide = $this->blockchain->distributeAide(
            beneficiaireId:   $request->beneficiaire_id,
            projetAideId:     $request->projet_aide_id,
            dateDistribution: now()->toDateString(),
            notes:            $request->notes,
        );

        $aide->load(['typeAide', 'projetAide']);

        return response()->json([
            'data' => [
                'aide' => [
                    'id'                => $aide->id,
                    'projet'            => $aide->projetAide->nom,
                    'type'              => $aide->typeAide->nom,
                    'date_distribution' => $aide->date_distribution->format('Y-m-d'),
                    'date_expiration'   => $aide->date_expiration->format('Y-m-d'),
                    'hash_transaction'  => $aide->hash_transaction,
                ],
            ],
        ], 201);
    }
}
