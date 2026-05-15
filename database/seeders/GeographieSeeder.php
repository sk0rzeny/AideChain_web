<?php

namespace Database\Seeders;

use App\Models\Departement;
use App\Models\Pays;
use App\Models\Region;
use App\Models\Village;
use App\Models\Ville;
use Illuminate\Database\Seeder;

class GeographieSeeder extends Seeder
{
    public function run(): void
    {
        $tchad = Pays::firstOrCreate(['nom' => 'Tchad']);

        // Région N'Djamena
        $ndjamena = Region::firstOrCreate(['nom' => 'N\'Djamena', 'pays_id' => $tchad->id]);
        $depChari  = Departement::firstOrCreate(['nom' => 'Département du Chari', 'region_id' => $ndjamena->id]);
        $villeNdj  = Ville::firstOrCreate(['nom' => 'N\'Djamena', 'departement_id' => $depChari->id]);
        Village::firstOrCreate(['nom' => 'Sabangali',   'ville_id' => $villeNdj->id]);
        Village::firstOrCreate(['nom' => 'Moursal',     'ville_id' => $villeNdj->id]);

        // Région Logone Oriental
        $logone    = Region::firstOrCreate(['nom' => 'Logone Oriental', 'pays_id' => $tchad->id]);
        $depPende  = Departement::firstOrCreate(['nom' => 'Département de la Pendé', 'region_id' => $logone->id]);
        $villeDoba = Ville::firstOrCreate(['nom' => 'Doba', 'departement_id' => $depPende->id]);
        Village::firstOrCreate(['nom' => 'Bénoye',  'ville_id' => $villeDoba->id]);
        Village::firstOrCreate(['nom' => 'Koumra',  'ville_id' => $villeDoba->id]);

        // Région Kanem
        $kanem    = Region::firstOrCreate(['nom' => 'Kanem', 'pays_id' => $tchad->id]);
        $depKanem = Departement::firstOrCreate(['nom' => 'Département de Kanem', 'region_id' => $kanem->id]);
        $villeMao = Ville::firstOrCreate(['nom' => 'Mao', 'departement_id' => $depKanem->id]);
        Village::firstOrCreate(['nom' => 'Moussoro', 'ville_id' => $villeMao->id]);
        Village::firstOrCreate(['nom' => 'Bol',      'ville_id' => $villeMao->id]);
    }
}
