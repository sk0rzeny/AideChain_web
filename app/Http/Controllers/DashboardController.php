<?php

namespace App\Http\Controllers;

use App\Models\AideDistribuee;
use App\Models\DemandeAdhesion;
use App\Models\ProjetAide;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private function terrainDashboard($user)
    {
        $demande = DemandeAdhesion::with('ong')
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        $data = ['demande' => $demande];

        if ($user->ong_id) {
            $ong = $user->ong;
            $data['ong']              = $ong;
            $data['nbBeneficiaires']  = $ong->beneficiaires()->count();
            $data['nbDistributions']  = AideDistribuee::where('ong_id', $ong->id)->count();
            $data['nbProjets']        = ProjetAide::where('ong_id', $ong->id)->where('statut', 'active')->count();
            $data['aidesRecentes']    = AideDistribuee::with(['beneficiaire', 'typeAide', 'projetAide'])
                ->where('ong_id', $ong->id)
                ->latest()
                ->take(5)
                ->get();
        }

        return view('pages.terrain.dashboard', $data);
    }

    public function __invoke(Request $request)
    {
        $user = $request->user();

        return match($user->role?->name) {
            'super_admin'      => view('pages.admin.dashboard'),
            'ong_representant' => view('pages.ong.dashboard', [
                'ong' => $user->ongRepresentee()->with('documents')->first(),
            ]),
            'ong_agent'        => $user->ong_id
                ? $this->terrainDashboard($user)
                : redirect()->route('choisir-ong'),
            default => view('dashboard'),
        };
    }
}
