<?php

namespace App\Livewire\Admin;

use App\Models\Ong;
use Livewire\Component;

class GestionOngs extends Component
{
    public string $filtre = 'pending';

    public function valider(int $ongId): void
    {
        Ong::findOrFail($ongId)->update(['statut' => 'active']);
    }

    public function rejeter(int $ongId): void
    {
        Ong::findOrFail($ongId)->update(['statut' => 'rejected']);
    }

    public function render()
    {
        $query = Ong::with(['representant', 'documents'])->latest();

        if ($this->filtre !== 'all') {
            $query->where('statut', $this->filtre);
        }

        return view('livewire.admin.gestion-ongs', [
            'ongs'          => $query->get(),
            'countPending'  => Ong::where('statut', 'pending')->count(),
            'countActive'   => Ong::where('statut', 'active')->count(),
            'countRejected' => Ong::where('statut', 'rejected')->count(),
            'countTotal'    => Ong::count(),
        ]);
    }
}
