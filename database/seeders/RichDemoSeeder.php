<?php

namespace Database\Seeders;

use App\Models\AideDistribuee;
use App\Models\Beneficiaire;
use App\Models\DemandeAdhesion;
use App\Models\Ong;
use App\Models\ProjetAide;
use App\Models\Role;
use App\Models\TypeAide;
use App\Models\User;
use App\Services\BlockchainService;
use Illuminate\Database\Seeder;

/**
 * Scénario enrichi — 3 ONGs supplémentaires, 9 nouvelles régions, ~36 bénéficiaires
 *
 * Comptes ajoutés :
 *   rep@ong-ahs.td           / password  → ONG Action Humanitaire Sahel (Abéché)
 *   agent@ong-ahs.td         / password  → agent ONG AHS
 *   rep@ong-fed.td           / password  → ONG Femmes et Développement (Sarh)
 *   agent@ong-fed.td         / password  → agent ONG FED
 *   rep@ong-pet.td           / password  → ONG Protection Enfance Tchad (Bol)
 *   agent1@ong-pet.td        / password  → agent ONG PET
 *   agent2@ong-pet.td        / password  → agent ONG PET
 *
 * Régions couvertes (nouvelles) :
 *   Ouaddaï, Batha, Wadi Fira, Mandoul, Tandjilé, Logone Oriental,
 *   Kanem, Chari-Baguirmi, Hadjer-Lamis, Guéra, Logone Occidental
 */
class RichDemoSeeder extends Seeder
{
    public function __construct(private readonly BlockchainService $blockchain) {}

    public function run(): void
    {
        $roleRep   = Role::firstOrCreate(['name' => 'ong_representant']);
        $roleAgent = Role::firstOrCreate(['name' => 'ong_agent']);

        $alimentaire  = TypeAide::where('nom', 'Aide alimentaire')->firstOrFail();
        $hygiene      = TypeAide::where('nom', 'Kit d\'hygiène')->firstOrFail();
        $abri         = TypeAide::where('nom', 'Abri / NFI')->firstOrFail();
        $medical      = TypeAide::where('nom', 'Soutien médical')->firstOrFail();
        $protection   = TypeAide::where('nom', 'Protection enfance')->firstOrFail();
        $psychosocial = TypeAide::where('nom', 'Soutien psychosocial')->firstOrFail();

        // ══════════════════════════════════════════════════════════════════
        // ONG 3 — Action Humanitaire Sahel (Abéché)
        // Couvre : Ouaddaï, Batha, Wadi Fira
        // ══════════════════════════════════════════════════════════════════

        $repAhs = User::firstOrCreate(
            ['email' => 'rep@ong-ahs.td'],
            [
                'name'              => 'Mahamat Saleh Annadif',
                'password'          => 'password',
                'role_id'           => $roleRep->id,
                'email_verified_at' => now(),
            ]
        );

        $ongAhs = Ong::firstOrCreate(
            ['email' => 'contact@ong-ahs.td'],
            [
                'nom'             => 'Action Humanitaire Sahel',
                'telephone'       => '+235 66 77 88 99',
                'adresse'         => 'Abéché, Quartier Al-Moustapha',
                'statut'          => 'active',
                'representant_id' => $repAhs->id,
            ]
        );

        $agentAhs = User::firstOrCreate(
            ['email' => 'agent@ong-ahs.td'],
            [
                'name'              => 'Hassane Brahim Madet',
                'password'          => 'password',
                'role_id'           => $roleAgent->id,
                'ong_id'            => $ongAhs->id,
                'email_verified_at' => now(),
            ]
        );
        DemandeAdhesion::firstOrCreate(
            ['user_id' => $agentAhs->id, 'ong_id' => $ongAhs->id],
            ['statut' => 'accepted']
        );

        $pAhsAlimOuaddai = ProjetAide::firstOrCreate(
            ['ong_id' => $ongAhs->id, 'nom' => 'Aide alimentaire – Ouaddaï'],
            [
                'type_aide_id'    => $alimentaire->id,
                'date_expiration' => now()->addMonths(8)->toDateString(),
                'zone_cible'      => 'Ouaddaï',
                'statut'          => 'active',
            ]
        );

        $pAhsAbri = ProjetAide::firstOrCreate(
            ['ong_id' => $ongAhs->id, 'nom' => 'Kits abri – Batha'],
            [
                'type_aide_id'    => $abri->id,
                'date_expiration' => now()->addMonths(6)->toDateString(),
                'zone_cible'      => 'Batha',
                'statut'          => 'active',
            ]
        );

        $pAhsMedical = ProjetAide::firstOrCreate(
            ['ong_id' => $ongAhs->id, 'nom' => 'Soutien médical – Wadi Fira'],
            [
                'type_aide_id'    => $medical->id,
                'date_expiration' => now()->addMonths(4)->toDateString(),
                'zone_cible'      => 'Wadi Fira',
                'statut'          => 'active',
            ]
        );

        $pAhsHygiene = ProjetAide::firstOrCreate(
            ['ong_id' => $ongAhs->id, 'nom' => 'Kits hygiène – Déplacés Ouaddaï'],
            [
                'type_aide_id'    => $hygiene->id,
                'date_expiration' => now()->addMonths(3)->toDateString(),
                'zone_cible'      => 'Ouaddaï',
                'statut'          => 'active',
            ]
        );

        $benAhs = [
            ['Salma',    'Abdelkerim',  '1989-04-18', 'femme', 'femme_chef_menage', $pAhsAlimOuaddai],
            ['Moukhtar', 'Goukouni',    '1981-09-03', 'homme', 'individu',          $pAhsAlimOuaddai],
            ['Fatouma',  'Bichara',     '2009-02-27', 'femme', 'enfant',            $pAhsAlimOuaddai],
            ['Adoum',    'Hassaballah', '1975-12-10', 'homme', 'deplacement_interne', $pAhsAbri],
            ['Aïcha',    'Kodoi',       '1992-06-15', 'femme', 'famille',           $pAhsAbri],
            ['Tahir',    'Mabrouk',     '1968-03-22', 'homme', 'individu',          $pAhsAbri],
            ['Hawa',     'Abderrahim',  '1985-11-07', 'femme', 'femme_chef_menage', $pAhsMedical],
            ['Youssouf', 'Djamal',      '1999-08-30', 'homme', 'individu',          $pAhsMedical],
            ['Kalthoum', 'Ousman',      '2011-01-14', 'femme', 'enfant',            $pAhsHygiene],
            ['Ibrahim',  'Tahirou',     '1990-05-21', 'homme', 'deplacement_interne', $pAhsHygiene],
            ['Faidah',   'Gorane',      '1973-07-16', 'femme', 'famille',           null],
            ['Maïna',    'Allamine',    '2003-10-05', 'homme', 'individu',          null],
        ];

        foreach ($benAhs as [$p, $n, $dob, $g, $cat, $projet]) {
            $b = $this->createBeneficiaire($p, $n, $dob, $g, $cat, $ongAhs);
            if ($projet !== null) {
                $this->distribuerSiAbsent($b, $projet);
            }
        }

        // ══════════════════════════════════════════════════════════════════
        // ONG 4 — Femmes et Développement (Sarh)
        // Couvre : Mandoul, Tandjilé, Logone Oriental, Guéra
        // ══════════════════════════════════════════════════════════════════

        $repFed = User::firstOrCreate(
            ['email' => 'rep@ong-fed.td'],
            [
                'name'              => 'Bernadette Mbailoum Ngarissem',
                'password'          => 'password',
                'role_id'           => $roleRep->id,
                'email_verified_at' => now(),
            ]
        );

        $ongFed = Ong::firstOrCreate(
            ['email' => 'contact@ong-fed.td'],
            [
                'nom'             => 'Femmes et Développement',
                'telephone'       => '+235 66 44 55 66',
                'adresse'         => 'Sarh, Rue de la Solidarité',
                'statut'          => 'active',
                'representant_id' => $repFed->id,
            ]
        );

        $agentFed = User::firstOrCreate(
            ['email' => 'agent@ong-fed.td'],
            [
                'name'              => 'Claudine Ndouba Laokein',
                'password'          => 'password',
                'role_id'           => $roleAgent->id,
                'ong_id'            => $ongFed->id,
                'email_verified_at' => now(),
            ]
        );
        DemandeAdhesion::firstOrCreate(
            ['user_id' => $agentFed->id, 'ong_id' => $ongFed->id],
            ['statut' => 'accepted']
        );

        $pFedProtection = ProjetAide::firstOrCreate(
            ['ong_id' => $ongFed->id, 'nom' => 'Protection enfance – Mandoul'],
            [
                'type_aide_id'    => $protection->id,
                'date_expiration' => now()->addMonths(12)->toDateString(),
                'zone_cible'      => 'Mandoul',
                'statut'          => 'active',
            ]
        );

        $pFedAlimTandjile = ProjetAide::firstOrCreate(
            ['ong_id' => $ongFed->id, 'nom' => 'Aide alimentaire – Tandjilé'],
            [
                'type_aide_id'    => $alimentaire->id,
                'date_expiration' => now()->addMonths(6)->toDateString(),
                'zone_cible'      => 'Tandjilé',
                'statut'          => 'active',
            ]
        );

        $pFedPsychoLogone = ProjetAide::firstOrCreate(
            ['ong_id' => $ongFed->id, 'nom' => 'Soutien psychosocial – Logone Oriental'],
            [
                'type_aide_id'    => $psychosocial->id,
                'date_expiration' => now()->addMonths(5)->toDateString(),
                'zone_cible'      => 'Logone Oriental',
                'statut'          => 'active',
            ]
        );

        $pFedHygieneGuera = ProjetAide::firstOrCreate(
            ['ong_id' => $ongFed->id, 'nom' => 'Kits hygiène – Guéra'],
            [
                'type_aide_id'    => $hygiene->id,
                'date_expiration' => now()->addMonths(4)->toDateString(),
                'zone_cible'      => 'Guéra',
                'statut'          => 'active',
            ]
        );

        $benFed = [
            ['Odette',   'Ngakoutou',   '1988-03-14', 'femme', 'femme_chef_menage', $pFedProtection],
            ['Sylvie',   'Dassert',     '2012-07-09', 'femme', 'enfant',            $pFedProtection],
            ['Jean',     'Mbainadji',   '2014-11-23', 'homme', 'enfant',            $pFedProtection],
            ['Marthe',   'Kemtere',     '1979-02-06', 'femme', 'famille',           $pFedAlimTandjile],
            ['Gédéon',   'Toura',       '1995-09-18', 'homme', 'individu',          $pFedAlimTandjile],
            ['Philomène','Beltola',     '1967-06-01', 'femme', 'femme_chef_menage', $pFedAlimTandjile],
            ['Lucie',    'Nayambaye',   '1982-04-25', 'femme', 'deplacement_interne', $pFedPsychoLogone],
            ['Emmanuel', 'Mbainao',     '1991-10-12', 'homme', 'deplacement_interne', $pFedPsychoLogone],
            ['Hortense', 'Naïmbaye',    '2006-08-17', 'femme', 'enfant',            $pFedHygieneGuera],
            ['Joachim',  'Kette',       '1974-01-30', 'homme', 'individu',          $pFedHygieneGuera],
            ['Agnès',    'Djimasde',    '1960-12-08', 'femme', 'famille',           null],
            ['Théophile','Laoukein',    '2000-05-04', 'homme', 'individu',          null],
        ];

        foreach ($benFed as [$p, $n, $dob, $g, $cat, $projet]) {
            $b = $this->createBeneficiaire($p, $n, $dob, $g, $cat, $ongFed);
            if ($projet !== null) {
                $this->distribuerSiAbsent($b, $projet);
            }
        }

        // ══════════════════════════════════════════════════════════════════
        // ONG 5 — Protection Enfance Tchad (Bol)
        // Couvre : Kanem, Chari-Baguirmi, Hadjer-Lamis, Logone Occidental
        // ══════════════════════════════════════════════════════════════════

        $repPet = User::firstOrCreate(
            ['email' => 'rep@ong-pet.td'],
            [
                'name'              => 'Adoum Youssouf Maïna',
                'password'          => 'password',
                'role_id'           => $roleRep->id,
                'email_verified_at' => now(),
            ]
        );

        $ongPet = Ong::firstOrCreate(
            ['email' => 'contact@ong-pet.td'],
            [
                'nom'             => 'Protection Enfance Tchad',
                'telephone'       => '+235 66 11 00 77',
                'adresse'         => 'Bol, Bord du Lac Tchad',
                'statut'          => 'active',
                'representant_id' => $repPet->id,
            ]
        );

        $agentPet1 = User::firstOrCreate(
            ['email' => 'agent1@ong-pet.td'],
            [
                'name'              => 'Haoua Djimadoum Alifa',
                'password'          => 'password',
                'role_id'           => $roleAgent->id,
                'ong_id'            => $ongPet->id,
                'email_verified_at' => now(),
            ]
        );
        DemandeAdhesion::firstOrCreate(
            ['user_id' => $agentPet1->id, 'ong_id' => $ongPet->id],
            ['statut' => 'accepted']
        );

        $agentPet2 = User::firstOrCreate(
            ['email' => 'agent2@ong-pet.td'],
            [
                'name'              => 'Ngarissem Belem Douba',
                'password'          => 'password',
                'role_id'           => $roleAgent->id,
                'ong_id'            => $ongPet->id,
                'email_verified_at' => now(),
            ]
        );
        DemandeAdhesion::firstOrCreate(
            ['user_id' => $agentPet2->id, 'ong_id' => $ongPet->id],
            ['statut' => 'accepted']
        );

        $pPetProtKanem = ProjetAide::firstOrCreate(
            ['ong_id' => $ongPet->id, 'nom' => 'Protection enfance – Kanem'],
            [
                'type_aide_id'    => $protection->id,
                'date_expiration' => now()->addMonths(9)->toDateString(),
                'zone_cible'      => 'Kanem',
                'statut'          => 'active',
            ]
        );

        $pPetAlimChari = ProjetAide::firstOrCreate(
            ['ong_id' => $ongPet->id, 'nom' => 'Aide alimentaire – Chari-Baguirmi'],
            [
                'type_aide_id'    => $alimentaire->id,
                'date_expiration' => now()->addMonths(5)->toDateString(),
                'zone_cible'      => 'Chari-Baguirmi',
                'statut'          => 'active',
            ]
        );

        $pPetHygieneHadjer = ProjetAide::firstOrCreate(
            ['ong_id' => $ongPet->id, 'nom' => 'Kits hygiène – Hadjer-Lamis'],
            [
                'type_aide_id'    => $hygiene->id,
                'date_expiration' => now()->addMonths(6)->toDateString(),
                'zone_cible'      => 'Hadjer-Lamis',
                'statut'          => 'active',
            ]
        );

        $pPetAbriLogoneOcc = ProjetAide::firstOrCreate(
            ['ong_id' => $ongPet->id, 'nom' => 'Kits abri – Logone Occidental'],
            [
                'type_aide_id'    => $abri->id,
                'date_expiration' => now()->addMonths(7)->toDateString(),
                'zone_cible'      => 'Logone Occidental',
                'statut'          => 'active',
            ]
        );

        $benPet = [
            ['Amina',    'Tahirou',     '2010-06-03', 'femme', 'enfant',            $pPetProtKanem],
            ['Oumar',    'Idriss',      '2013-09-17', 'homme', 'enfant',            $pPetProtKanem],
            ['Fatima',   'Hamid',       '2007-03-28', 'femme', 'enfant',            $pPetProtKanem],
            ['Kadidja',  'Mahamat',     '1984-01-11', 'femme', 'femme_chef_menage', $pPetAlimChari],
            ['Salleh',   'Aboubakar',   '1976-08-20', 'homme', 'famille',           $pPetAlimChari],
            ['Zeinab',   'Abdramane',   '1993-11-04', 'femme', 'deplacement_interne', $pPetAlimChari],
            ['Rachid',   'Moukhtar',    '1988-05-13', 'homme', 'individu',          $pPetHygieneHadjer],
            ['Mariam',   'Garba',       '2004-02-07', 'femme', 'enfant',            $pPetHygieneHadjer],
            ['Djimet',   'Nalbey',      '1970-10-25', 'homme', 'individu',          $pPetAbriLogoneOcc],
            ['Clémentine','Moïse',      '1986-07-19', 'femme', 'famille',           $pPetAbriLogoneOcc],
            ['Alphonse', 'Kessou',      '1997-04-08', 'homme', 'individu',          null],
            ['Roukia',   'Abdelkader',  '2001-12-31', 'femme', 'individu',          null],
        ];

        foreach ($benPet as [$p, $n, $dob, $g, $cat, $projet]) {
            $b = $this->createBeneficiaire($p, $n, $dob, $g, $cat, $ongPet);
            if ($projet !== null) {
                $this->distribuerSiAbsent($b, $projet);
            }
        }
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
