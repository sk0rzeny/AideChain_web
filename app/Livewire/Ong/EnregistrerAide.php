<?php

namespace App\Livewire\Ong;

use App\Models\Beneficiaire;
use App\Models\Ong;
use App\Models\ProjetAide;
use App\Services\BlockchainService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class EnregistrerAide extends Component
{
    // Étape 1 — Identification
    public string $prenom = '';
    public string $nom = '';
    public string $dateNaissance = '';
    public string $rechercheErreur = '';

    // Navigation
    public int $step = 1;
    public string $identityHash = '';
    public ?array $beneficiaireInfo = null;

    // Étape 2 — Projet
    public string $projetAideId = '';
    public string $notes = '';
    public ?array $duplicateInfo = null;

    // Succès
    public bool $success = false;
    public ?array $aideInfo = null;

    public function mount(): void
    {
        $id = request()->query('beneficiaire_id');
        if ($id) {
            $beneficiaire = Beneficiaire::with('ong')->find((int) $id);
            if ($beneficiaire) {
                $this->chargerBeneficiaire($beneficiaire);
            }
        }
    }

    private function chargerBeneficiaire(Beneficiaire $beneficiaire): void
    {
        $this->identityHash    = $beneficiaire->identity_hash;
        $this->beneficiaireInfo = [
            'id'  => $beneficiaire->id,
            'nom' => $beneficiaire->prenom . ' ' . $beneficiaire->nom,
            'ong' => $beneficiaire->ong->nom,
        ];
        $this->step = 2;
    }

    public function rechercherBeneficiaire(): void
    {
        $this->rechercheErreur = '';

        $this->validate([
            'prenom'        => 'required|string|max:100',
            'nom'           => 'required|string|max:100',
            'dateNaissance' => 'required|date|before:today',
        ], [
            'prenom.required'        => 'Le prénom est obligatoire.',
            'nom.required'           => 'Le nom est obligatoire.',
            'dateNaissance.required' => 'La date de naissance est obligatoire.',
            'dateNaissance.before'   => 'La date de naissance doit être dans le passé.',
        ]);

        $blockchain   = app(BlockchainService::class);
        $hash         = $blockchain->computeHash($this->prenom, $this->nom, $this->dateNaissance);
        $beneficiaire = Beneficiaire::with('ong')->where('identity_hash', $hash)->first();

        if (!$beneficiaire) {
            $this->rechercheErreur = 'Bénéficiaire introuvable dans le registre. Enregistrez-le d\'abord via "Nouveau bénéficiaire".';
            return;
        }

        $this->chargerBeneficiaire($beneficiaire);
    }

    public function checkDuplicate(): void
    {
        if (!$this->projetAideId || !$this->identityHash) {
            $this->duplicateInfo = null;
            return;
        }

        $projet = ProjetAide::find((int) $this->projetAideId);
        if (!$projet) {
            $this->duplicateInfo = null;
            return;
        }

        $aide = app(BlockchainService::class)
            ->getDuplicateInfo($this->identityHash, $projet->type_aide_id);

        $this->duplicateInfo = $aide ? [
            'aide'       => $aide->typeAide->nom,
            'ong'        => $aide->ong->nom,
            'expiration' => $aide->date_expiration->format('d/m/Y'),
        ] : null;
    }

    public function distribuer(): void
    {
        if (!$this->beneficiaireInfo || !$this->identityHash) {
            return;
        }

        $this->validate([
            'projetAideId' => 'required|exists:projets_aide,id',
            'notes'        => 'nullable|string|max:500',
        ], [
            'projetAideId.required' => 'Sélectionnez un projet d\'aide.',
            'projetAideId.exists'   => 'Projet invalide.',
        ]);

        $projet     = ProjetAide::findOrFail((int) $this->projetAideId);
        $blockchain = app(BlockchainService::class);

        if ($blockchain->isDuplicate($this->identityHash, $projet->type_aide_id)) {
            $this->checkDuplicate();
            return;
        }

        $aide = $blockchain->distributeAide(
            beneficiaireId:   $this->beneficiaireInfo['id'],
            projetAideId:     (int) $this->projetAideId,
            dateDistribution: now()->toDateString(),
            notes:            $this->notes ?: null,
        );

        $aide->load(['typeAide', 'projetAide']);

        $this->aideInfo = [
            'beneficiaire' => $this->beneficiaireInfo['nom'],
            'projet'       => $aide->projetAide->nom,
            'type'         => $aide->typeAide->nom,
            'distribution' => $aide->date_distribution->format('d/m/Y'),
            'expiration'   => $aide->date_expiration->format('d/m/Y'),
            'hash'         => substr($aide->hash_transaction, 0, 16) . '...',
        ];

        $this->success = true;
    }

    public function nouvelleAide(): void
    {
        $this->reset(['projetAideId', 'notes', 'duplicateInfo', 'success', 'aideInfo']);
    }

    public function reinitialiser(): void
    {
        $this->reset(['prenom', 'nom', 'dateNaissance', 'identityHash', 'beneficiaireInfo',
            'projetAideId', 'notes', 'duplicateInfo', 'success', 'aideInfo', 'rechercheErreur']);
        $this->step = 1;
    }

    private function currentOng(): Ong
    {
        return Auth::user()->ongRepresentee ?? Auth::user()->ong;
    }

    public function render()
    {
        $ong = $this->currentOng();

        return view('livewire.ong.enregistrer-aide', [
            'projets' => ProjetAide::with('typeAide')
                ->where('ong_id', $ong->id)
                ->where('statut', 'active')
                ->where('date_expiration', '>', now())
                ->orderBy('nom')
                ->get(),
        ]);
    }
}
