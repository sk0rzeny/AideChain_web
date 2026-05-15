<?php

namespace App\Services;

use App\Models\AideDistribuee;
use App\Models\Beneficiaire;
use App\Models\ProjetAide;

class BlockchainService
{
    public function computeHash(string $prenom, string $nom, string $dateNaissance): string
    {
        $normalized = implode('|', [
            mb_strtolower(trim($prenom)),
            mb_strtolower(trim($nom)),
            $dateNaissance, // YYYY-MM-DD
        ]);

        return hash('sha256', $normalized);
    }

    public function findBeneficiaire(string $identityHash): ?Beneficiaire
    {
        return Beneficiaire::where('identity_hash', $identityHash)->first();
    }

    public function isDuplicate(string $identityHash, int $typeAideId): bool
    {
        return $this->getDuplicateInfo($identityHash, $typeAideId) !== null;
    }

    public function getDuplicateInfo(string $identityHash, int $typeAideId): ?AideDistribuee
    {
        return AideDistribuee::with(['ong', 'typeAide'])
            ->join('beneficiaires', 'beneficiaires.id', '=', 'aides_distribuees.beneficiaire_id')
            ->where('beneficiaires.identity_hash', $identityHash)
            ->where('aides_distribuees.type_aide_id', $typeAideId)
            ->where('aides_distribuees.date_expiration', '>', now())
            ->select('aides_distribuees.*')
            ->first();
    }

    public function distributeAide(
        int $beneficiaireId,
        int $projetAideId,
        string $dateDistribution,
        ?string $notes = null
    ): AideDistribuee {
        $projet = ProjetAide::findOrFail($projetAideId);

        $hashTransaction = hash('sha256', implode('|', [
            $beneficiaireId,
            $projetAideId,
            $projet->ong_id,
            $dateDistribution,
            microtime(true),
        ]));

        return AideDistribuee::create([
            'beneficiaire_id'   => $beneficiaireId,
            'projet_aide_id'    => $projetAideId,
            'type_aide_id'      => $projet->type_aide_id,
            'ong_id'            => $projet->ong_id,
            'date_distribution' => $dateDistribution,
            'date_expiration'   => $projet->date_expiration->toDateString(),
            'hash_transaction'  => $hashTransaction,
            'notes'             => $notes,
        ]);
    }
}
