<?php

namespace App\Livewire;

use App\Models\DemandeAdhesion;
use App\Models\Ong;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ChoisirOng extends Component
{
    public ?int $ong_id = null;
    public ?string $statutDemande = null;
    public ?string $nomOng = null;

    public function mount(): void
    {
        $demande = DemandeAdhesion::with('ong')
            ->where('user_id', Auth::id())
            ->latest()
            ->first();

        if ($demande) {
            $this->ong_id = $demande->ong_id;
            $this->statutDemande = $demande->statut;
            $this->nomOng = $demande->ong->nom;
        }
    }

    public function soumettre(): void
    {
        $this->validate(['ong_id' => 'required|exists:ongs,id']);

        DemandeAdhesion::updateOrCreate(
            ['user_id' => Auth::id()],
            ['ong_id' => $this->ong_id, 'statut' => 'pending']
        );

        $this->statutDemande = 'pending';
        $this->nomOng = Ong::find($this->ong_id)?->nom;
    }

    public function render()
    {
        return view('livewire.choisir-ong', [
            'ongs' => Ong::where('statut', 'active')->orderBy('nom')->get(),
        ]);
    }
}
