<?php

namespace Database\Seeders;

use App\Models\AideDistribuee;
use App\Models\Beneficiaire;
use App\Models\Ong;
use App\Models\ProjetAide;
use App\Models\TypeAide;
use App\Services\BlockchainService;
use Illuminate\Database\Seeder;

/**
 * Scénario doublon pré-chargé :
 *
 *   Amir Abakar (1985-03-12) → ONG Espoir Tchad
 *   Projet "Aide alimentaire – Mayo Kebbi" actif 6 mois
 *
 *   → Se connecter en tant que representant@ong-soleil.td
 *   → Distribuer une aide → sélectionner "Aide alimentaire – Lac Tchad"
 *   → ⛔ DOUBLON ACTIF bloqué (même type_aide_id que le projet Espoir)
 */
class BeneficiaireSeeder extends Seeder
{
    public function __construct(private readonly BlockchainService $blockchain) {}

    public function run(): void
    {
        $ongEspoir = Ong::where('email', 'contact@ong-espoir.td')->firstOrFail();
        $ongSoleil = Ong::where('email', 'contact@ong-soleil.td')->firstOrFail();

        $alimentaire  = TypeAide::where('nom', 'Aide alimentaire')->firstOrFail();
        $hygiene      = TypeAide::where('nom', 'Kit d\'hygiène')->firstOrFail();
        $abri         = TypeAide::where('nom', 'Abri / NFI')->firstOrFail();
        $medical      = TypeAide::where('nom', 'Soutien médical')->firstOrFail();
        $protection   = TypeAide::where('nom', 'Protection enfance')->firstOrFail();
        $psychosocial = TypeAide::where('nom', 'Soutien psychosocial')->firstOrFail();

        // ── Projets d'aide — ONG Espoir Tchad ────────────────────────────

        $projetAlimEspoir = ProjetAide::firstOrCreate(
            ['ong_id' => $ongEspoir->id, 'nom' => 'Aide alimentaire – Mayo Kebbi'],
            [
                'type_aide_id'    => $alimentaire->id,
                'date_expiration' => now()->addMonths(6)->toDateString(),
                'zone_cible'      => 'Région de Mayo Kebbi',
                'statut'          => 'active',
            ]
        );

        $projetHygieneEspoir = ProjetAide::firstOrCreate(
            ['ong_id' => $ongEspoir->id, 'nom' => 'Distribution kits hygiène – N\'Djamena'],
            [
                'type_aide_id'    => $hygiene->id,
                'date_expiration' => now()->addMonths(3)->toDateString(),
                'zone_cible'      => 'N\'Djamena',
                'statut'          => 'active',
            ]
        );

        $projetMedicalEspoir = ProjetAide::firstOrCreate(
            ['ong_id' => $ongEspoir->id, 'nom' => 'Soutien médical – Déplacés internes'],
            [
                'type_aide_id'    => $medical->id,
                'date_expiration' => now()->addMonths(2)->toDateString(),
                'zone_cible'      => 'Camp de Goz Beïda',
                'statut'          => 'active',
            ]
        );

        $projetProtectionEspoir = ProjetAide::firstOrCreate(
            ['ong_id' => $ongEspoir->id, 'nom' => 'Protection enfance – Lac Tchad'],
            [
                'type_aide_id'    => $protection->id,
                'date_expiration' => now()->addMonths(12)->toDateString(),
                'zone_cible'      => 'Région du Lac',
                'statut'          => 'active',
            ]
        );

        $projetAbriEspoir = ProjetAide::firstOrCreate(
            ['ong_id' => $ongEspoir->id, 'nom' => 'Kits abri – Retournés'],
            [
                'type_aide_id'    => $abri->id,
                'date_expiration' => now()->addMonths(4)->toDateString(),
                'zone_cible'      => 'Moyen-Chari',
                'statut'          => 'active',
            ]
        );

        // ── Projets d'aide — ONG Soleil du Sahel ─────────────────────────

        $projetPsychoSoleil = ProjetAide::firstOrCreate(
            ['ong_id' => $ongSoleil->id, 'nom' => 'Soutien psychosocial – Réfugiés'],
            [
                'type_aide_id'    => $psychosocial->id,
                'date_expiration' => now()->addMonths(3)->toDateString(),
                'zone_cible'      => 'Région de l\'Est',
                'statut'          => 'active',
            ]
        );

        // ★ SUJET DU DOUBLON — même type_aide_id que $projetAlimEspoir
        $projetAlimSoleil = ProjetAide::firstOrCreate(
            ['ong_id' => $ongSoleil->id, 'nom' => 'Aide alimentaire – Lac Tchad'],
            [
                'type_aide_id'    => $alimentaire->id,
                'date_expiration' => now()->addMonths(1)->toDateString(),
                'zone_cible'      => 'Région du Lac',
                'statut'          => 'active',
            ]
        );

        // ── ONG Espoir Tchad — 10 bénéficiaires ──────────────────────────

        // ★ SUJET DU DOUBLON — Aide alimentaire active 6 mois
        $amir = $this->createBeneficiaire(
            prenom: 'Amir', nom: 'Abakar', dateNaissance: '1985-03-12',
            genre: 'homme', categorie: 'individu', ong: $ongEspoir
        );
        $this->distribuerSiAbsent($amir, $projetAlimEspoir);

        $khadija = $this->createBeneficiaire(
            prenom: 'Khadija', nom: 'Hassan', dateNaissance: '1992-07-22',
            genre: 'femme', categorie: 'femme_chef_menage', ong: $ongEspoir
        );
        $this->distribuerSiAbsent($khadija, $projetHygieneEspoir);

        $moussa = $this->createBeneficiaire(
            prenom: 'Moussa', nom: 'Deby', dateNaissance: '1978-11-05',
            genre: 'homme', categorie: 'deplacement_interne', ong: $ongEspoir
        );
        $this->distribuerSiAbsent($moussa, $projetMedicalEspoir);

        $halime = $this->createBeneficiaire(
            prenom: 'Halimé', nom: 'Abderamane', dateNaissance: '2010-04-15',
            genre: 'femme', categorie: 'enfant', ong: $ongEspoir
        );
        $this->distribuerSiAbsent($halime, $projetProtectionEspoir);

        $mariam = $this->createBeneficiaire(
            prenom: 'Mariam', nom: 'Ngaradoum', dateNaissance: '1965-09-30',
            genre: 'femme', categorie: 'femme_chef_menage', ong: $ongEspoir
        );
        $this->distribuerSiAbsent($mariam, $projetAbriEspoir);

        // Bénéficiaires sans aide active
        $this->createBeneficiaire('Adam',   'Tirgo',    '1990-02-18', 'homme', 'individu',         $ongEspoir);
        $this->createBeneficiaire('Zara',   'Ouddei',   '2005-08-12', 'femme', 'enfant',            $ongEspoir);
        $this->createBeneficiaire('Yaya',   'Kamougue', '1983-06-24', 'homme', 'individu',          $ongEspoir);
        $this->createBeneficiaire('Fatimé', 'Bichara',  '1998-12-01', 'femme', 'famille',           $ongEspoir);
        $this->createBeneficiaire('Hassan', 'Gorane',   '2001-03-09', 'homme', 'individu',          $ongEspoir);

        // ── ONG Soleil du Sahel — 5 bénéficiaires ────────────────────────

        $noura = $this->createBeneficiaire(
            prenom: 'Noura', nom: 'Mahamad', dateNaissance: '1987-05-19',
            genre: 'femme', categorie: 'femme_chef_menage', ong: $ongSoleil
        );
        $this->distribuerSiAbsent($noura, $projetPsychoSoleil);

        $idriss = $this->createBeneficiaire(
            prenom: 'Idriss', nom: 'Marouf', dateNaissance: '1975-02-14',
            genre: 'homme', categorie: 'deplacement_interne', ong: $ongSoleil
        );
        $this->distribuerSiAbsent($idriss, $projetAlimSoleil);

        // Bénéficiaires sans aide active
        $this->createBeneficiaire('Rokia',    'Ngarta',     '2008-11-22', 'femme', 'enfant',   $ongSoleil);
        $this->createBeneficiaire('Abdallah', 'Kanem',      '1991-07-08', 'homme', 'individu', $ongSoleil);
        $this->createBeneficiaire('Aïcha',    'Tombalbaye', '1969-04-17', 'femme', 'famille',  $ongSoleil);
    }

    private function createBeneficiaire(
        string $prenom,
        string $nom,
        string $dateNaissance,
        string $genre,
        string $categorie,
        Ong $ong,
    ): Beneficiaire {
        $hash = $this->blockchain->computeHash($prenom, $nom, $dateNaissance);

        return Beneficiaire::firstOrCreate(
            ['identity_hash' => $hash],
            [
                'prenom'         => $prenom,
                'nom'            => $nom,
                'date_naissance' => $dateNaissance,
                'genre'          => $genre,
                'categorie'      => $categorie,
                'ong_id'         => $ong->id,
            ]
        );
    }

    private function distribuerSiAbsent(Beneficiaire $beneficiaire, ProjetAide $projet): void
    {
        $dejaActif = AideDistribuee::where('beneficiaire_id', $beneficiaire->id)
            ->where('type_aide_id', $projet->type_aide_id)
            ->where('date_expiration', '>', now())
            ->exists();

        if ($dejaActif) {
            return;
        }

        $this->blockchain->distributeAide(
            beneficiaireId:   $beneficiaire->id,
            projetAideId:     $projet->id,
            dateDistribution: now()->toDateString(),
        );
    }
}
