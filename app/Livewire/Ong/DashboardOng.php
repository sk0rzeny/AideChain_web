<?php

namespace App\Livewire\Ong;

use App\Models\AideDistribuee;
use App\Models\DemandeAdhesion;
use App\Models\ProjetAide;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DashboardOng extends Component
{
    public function accepterDemande(int $demandeId): void
    {
        $ong = Auth::user()->ongRepresentee;
        $demande = DemandeAdhesion::with('user')->findOrFail($demandeId);

        abort_if($demande->ong_id !== $ong->id, 403);

        $demande->update(['statut' => 'accepted']);
        $demande->user->update(['ong_id' => $ong->id]);
    }

    public function rejeterDemande(int $demandeId): void
    {
        $ong = Auth::user()->ongRepresentee;
        $demande = DemandeAdhesion::findOrFail($demandeId);

        abort_if($demande->ong_id !== $ong->id, 403);

        $demande->update(['statut' => 'rejected']);
    }

    public function retirerAgent(int $userId): void
    {
        $ong = Auth::user()->ongRepresentee;
        $agent = User::findOrFail($userId);

        abort_if($agent->ong_id !== $ong->id, 403);

        $agent->update(['ong_id' => null]);

        DemandeAdhesion::where('user_id', $userId)
            ->where('ong_id', $ong->id)
            ->where('statut', 'accepted')
            ->update(['statut' => 'rejected']);
    }

    public function suspendreProjet(int $projetId): void
    {
        $ong = Auth::user()->ongRepresentee;
        $projet = ProjetAide::findOrFail($projetId);

        abort_if($projet->ong_id !== $ong->id, 403);

        $projet->update(['statut' => 'suspendue']);
    }

    public function reactiverProjet(int $projetId): void
    {
        $ong = Auth::user()->ongRepresentee;
        $projet = ProjetAide::findOrFail($projetId);

        abort_if($projet->ong_id !== $ong->id, 403);

        $projet->update(['statut' => 'active']);
    }

    public function render()
    {
        $ong = Auth::user()->ongRepresentee;

        return view('livewire.ong.dashboard-ong', [
            'ong'             => $ong,
            'nbBeneficiaires' => $ong->beneficiaires()->count(),
            'nbDistributions' => $ong->aidesDistribuees()->count(),
            'nbAgents'        => $ong->agents()->count(),
            'nbProjets'       => $ong->projetsAide()->where('statut', 'active')->count(),
            'demandesPending' => DemandeAdhesion::with('user')
                ->where('ong_id', $ong->id)
                ->where('statut', 'pending')
                ->latest()
                ->get(),
            'projets'         => ProjetAide::with(['typeAide'])
                ->withCount('distributions')
                ->where('ong_id', $ong->id)
                ->orderByRaw("FIELD(statut,'active','suspendue','terminee')")
                ->orderBy('date_expiration')
                ->get(),
            'agents'          => $ong->agents()->latest()->get(),
            'aidesRecentes'   => AideDistribuee::with(['beneficiaire', 'typeAide', 'projetAide'])
                ->where('ong_id', $ong->id)
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }
}
