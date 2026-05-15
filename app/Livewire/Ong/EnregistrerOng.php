<?php

namespace App\Livewire\Ong;

use App\Models\DocumentOng;
use App\Models\Ong;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class EnregistrerOng extends Component
{
    use WithFileUploads;

    public string $nom = '';
    public string $email = '';
    public string $telephone = '';
    public string $adresse = '';

    public string $type1 = '';
    public $fichier1 = null;
    public string $type2 = '';
    public $fichier2 = null;
    public string $type3 = '';
    public $fichier3 = null;

    public function mount(): void
    {
        $existing = Ong::where('representant_id', Auth::id())
            ->whereIn('statut', ['pending', 'active'])
            ->exists();

        if ($existing) {
            $this->redirect(route('dashboard'), navigate: true);
        }
    }

    public function soumettre(): void
    {
        $this->validate([
            'nom'       => 'required|string|max:255',
            'email'     => 'required|email|unique:ongs,email',
            'telephone' => 'nullable|string|max:20',
            'adresse'   => 'nullable|string|max:500',
            'type1'     => 'required|string|max:100',
            'fichier1'  => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'type2'     => 'nullable|string|max:100|required_with:fichier2',
            'fichier2'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'type3'     => 'nullable|string|max:100|required_with:fichier3',
            'fichier3'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ], [
            'nom.required'      => 'Le nom de l\'ONG est obligatoire.',
            'email.required'    => 'L\'adresse email est obligatoire.',
            'email.unique'      => 'Cette adresse email est déjà utilisée par une autre ONG.',
            'type1.required'    => 'Le type du premier document est obligatoire.',
            'fichier1.required' => 'Au moins un document est obligatoire.',
            'fichier1.mimes'    => 'Le document doit être au format PDF, JPG ou PNG.',
            'fichier1.max'      => 'Le document ne doit pas dépasser 5 Mo.',
        ]);

        $ong = Ong::create([
            'nom'             => $this->nom,
            'email'           => $this->email,
            'telephone'       => $this->telephone ?: null,
            'adresse'         => $this->adresse ?: null,
            'statut'          => 'pending',
            'representant_id' => Auth::id(),
        ]);

        foreach ([1, 2, 3] as $i) {
            $fichier = $this->{"fichier{$i}"};
            $type    = $this->{"type{$i}"};
            if ($fichier && $type) {
                DocumentOng::create([
                    'ong_id'         => $ong->id,
                    'type_document'  => $type,
                    'chemin_fichier' => $fichier->store('documents_ong', 'public'),
                ]);
            }
        }

        $this->redirect(route('dashboard'), navigate: true);
    }

    public function render()
    {
        return view('livewire.ong.enregistrer-ong');
    }
}
