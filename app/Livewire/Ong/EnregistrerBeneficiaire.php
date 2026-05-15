<?php

namespace App\Livewire\Ong;

use App\Models\AideDistribuee;
use App\Models\Beneficiaire;
use App\Models\Ong;
use App\Services\BlockchainService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class EnregistrerBeneficiaire extends Component
{
    public string $prenom = '';
    public string $nom = '';
    public string $dateNaissance = '';
    public string $genre = '';
    public string $categorie = '';
    public string $notes = '';

    public ?array $checkResult = null;
    public ?int $beneficiaireId = null;
    public bool $success = false;

    public function checkIdentity(): void
    {
        if (empty(trim($this->prenom)) || empty(trim($this->nom)) || empty($this->dateNaissance)) {
            $this->checkResult = null;
            return;
        }

        $blockchain = app(BlockchainService::class);
        $hash = $blockchain->computeHash($this->prenom, $this->nom, $this->dateNaissance);
        $beneficiaire = $blockchain->findBeneficiaire($hash);

        if (!$beneficiaire) {
            $this->checkResult = ['type' => 'new'];
            $this->beneficiaireId = null;
            return;
        }

        $this->beneficiaireId = $beneficiaire->id;

        $aideActive = AideDistribuee::with(['typeAide', 'ong'])
            ->where('beneficiaire_id', $beneficiaire->id)
            ->where('date_expiration', '>', now())
            ->first();

        if ($aideActive) {
            $this->checkResult = [
                'type'       => 'duplicate',
                'nom'        => $beneficiaire->prenom . ' ' . $beneficiaire->nom,
                'aide'       => $aideActive->typeAide->nom,
                'ong'        => $aideActive->ong->nom,
                'expiration' => $aideActive->date_expiration->format('d/m/Y'),
            ];
        } else {
            $this->checkResult = [
                'type' => 'exists',
                'nom'  => $beneficiaire->prenom . ' ' . $beneficiaire->nom,
                'ong'  => $beneficiaire->ong->nom,
            ];
        }
    }

    public function enregistrer(): void
    {
        $this->validate([
            'prenom'        => 'required|string|max:100',
            'nom'           => 'required|string|max:100',
            'dateNaissance' => 'required|date|before:today',
            'genre'         => 'required|in:homme,femme,autre',
            'categorie'     => 'required|in:individu,famille,enfant,femme_chef_menage,deplacement_interne',
            'notes'         => 'nullable|string|max:500',
        ], [
            'prenom.required'        => 'Le prénom est obligatoire.',
            'nom.required'           => 'Le nom est obligatoire.',
            'dateNaissance.required' => 'La date de naissance est obligatoire.',
            'dateNaissance.before'   => 'La date de naissance doit être dans le passé.',
            'genre.required'         => 'Le genre est obligatoire.',
            'genre.in'               => 'Genre invalide.',
            'categorie.required'     => 'La catégorie est obligatoire.',
            'categorie.in'           => 'Catégorie invalide.',
        ]);

        $ong = $this->currentOng();
        $blockchain = app(BlockchainService::class);
        $hash = $blockchain->computeHash($this->prenom, $this->nom, $this->dateNaissance);
        $beneficiaire = $blockchain->findBeneficiaire($hash);

        if (!$beneficiaire) {
            $beneficiaire = Beneficiaire::create([
                'identity_hash'  => $hash,
                'prenom'         => $this->prenom,
                'nom'            => $this->nom,
                'date_naissance' => $this->dateNaissance,
                'genre'          => $this->genre,
                'categorie'      => $this->categorie,
                'ong_id'         => $ong->id,
                'notes'          => $this->notes ?: null,
            ]);
        }

        $this->beneficiaireId = $beneficiaire->id;
        $this->success = true;
        $this->dispatch('beneficiaire-pret', id: $beneficiaire->id);
    }

    public function reinitialiser(): void
    {
        $this->reset(['prenom', 'nom', 'dateNaissance', 'genre', 'categorie', 'notes', 'checkResult', 'beneficiaireId', 'success']);
    }

    private function currentOng(): Ong
    {
        return Auth::user()->ongRepresentee ?? Auth::user()->ong;
    }

    public function render()
    {
        return view('livewire.ong.enregistrer-beneficiaire');
    }
}
