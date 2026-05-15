<?php

namespace App\Livewire\Coordination;

use App\Models\AideDistribuee;
use App\Models\Beneficiaire;
use App\Models\Ong;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class DashboardCoordination extends Component
{
    public bool $isAdmin = false;
    public ?int $ongId = null;

    public function mount(): void
    {
        $this->isAdmin = Auth::user()->role?->name === 'super_admin';
        $this->ongId   = $this->isAdmin ? null : Auth::user()->ongRepresentee?->id;
    }

    public function render()
    {
        $totalBeneficiaires    = Beneficiaire::count();
        $aidesActives          = AideDistribuee::where('date_expiration', '>', now())->count();
        $ongsActives           = Ong::where('statut', 'active')->count();
        $beneficiairesCouverts = Beneficiaire::whereHas('aides', fn($q) => $q->where('date_expiration', '>', now()))->count();
        $tauxCouverture        = $totalBeneficiaires > 0
            ? round($beneficiairesCouverts / $totalBeneficiaires * 100)
            : 0;

        $couvByOng = Ong::where('statut', 'active')
            ->withCount('beneficiaires')
            ->orderByDesc('beneficiaires_count')
            ->get()
            ->map(function (Ong $ong) {
                $couverts = Beneficiaire::where('ong_id', $ong->id)
                    ->whereHas('aides', fn($q) => $q->where('date_expiration', '>', now()))
                    ->count();

                $taux = $ong->beneficiaires_count > 0
                    ? round($couverts / $ong->beneficiaires_count * 100)
                    : null;

                return [
                    'nom'           => $ong->nom,
                    'beneficiaires' => $ong->beneficiaires_count,
                    'couverts'      => $couverts,
                    'taux'          => $taux,
                    'statut'        => match(true) {
                        $taux === null      => 'vide',
                        $taux === 0         => 'nul',
                        $taux < 70          => 'partiel',
                        default             => 'bon',
                    },
                ];
            });

        $nonCouvertsQuery = Beneficiaire::with('ong')
            ->whereDoesntHave('aides', fn($q) => $q->where('date_expiration', '>', now()));

        if (!$this->isAdmin && $this->ongId) {
            $nonCouvertsQuery->where('ong_id', $this->ongId);
        }

        $nonCouverts = $nonCouvertsQuery->latest()->take(20)->get();

        return view('livewire.coordination.dashboard-coordination', [
            'kpis' => [
                'totalBeneficiaires'    => $totalBeneficiaires,
                'aidesActives'          => $aidesActives,
                'ongsActives'           => $ongsActives,
                'tauxCouverture'        => $tauxCouverture,
                'beneficiairesCouverts' => $beneficiairesCouverts,
            ],
            'couvByOng'   => $couvByOng,
            'nonCouverts' => $nonCouverts,
        ]);
    }
}
