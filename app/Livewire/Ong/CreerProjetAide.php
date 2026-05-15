<?php

namespace App\Livewire\Ong;

use App\Models\ProjetAide;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CreerProjetAide extends Component
{
    public string $nom = '';
    public string $typeAideId = '';
    public string $dateExpiration = '';
    public string $zoneCible = '';
    public string $description = '';

    public function enregistrer(): void
    {
        $this->validate([
            'nom'           => 'required|string|max:255',
            'typeAideId'    => 'required|exists:types_aide,id',
            'dateExpiration'=> 'required|date|after:today',
            'zoneCible'     => 'nullable|string|max:255',
            'description'   => 'nullable|string|max:1000',
        ], [
            'nom.required'            => 'Le nom du projet est obligatoire.',
            'typeAideId.required'     => 'Le type d\'aide est obligatoire.',
            'typeAideId.exists'       => 'Type d\'aide invalide.',
            'dateExpiration.required' => 'La date d\'expiration est obligatoire.',
            'dateExpiration.after'    => 'La date d\'expiration doit être dans le futur.',
        ]);

        $ong = Auth::user()->ongRepresentee;

        ProjetAide::create([
            'ong_id'          => $ong->id,
            'nom'             => $this->nom,
            'type_aide_id'    => (int) $this->typeAideId,
            'date_expiration' => $this->dateExpiration,
            'zone_cible'      => $this->zoneCible ?: null,
            'statut'          => 'active',
            'description'     => $this->description ?: null,
        ]);

        $this->redirect(route('dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.ong.creer-projet-aide', [
            'typesAide' => \App\Models\TypeAide::orderBy('nom')->get(),
        ]);
    }
}
