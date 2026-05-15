<?php

namespace Database\Seeders;

use App\Models\TypeAide;
use Illuminate\Database\Seeder;

class TypeAideSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['nom' => 'Aide alimentaire',    'description' => 'Vivres, rations alimentaires et compléments nutritionnels'],
            ['nom' => 'Kit d\'hygiène',       'description' => 'Articles d\'hygiène, savon, serviettes hygiéniques'],
            ['nom' => 'Abri / NFI',           'description' => 'Tentes, bâches, couvertures et articles non-alimentaires'],
            ['nom' => 'Soutien médical',      'description' => 'Consultations, médicaments et soins de santé primaires'],
            ['nom' => 'Protection enfance',   'description' => 'Services de protection et soutien pour les enfants vulnérables'],
            ['nom' => 'Soutien psychosocial', 'description' => 'Accompagnement psychologique et soutien communautaire'],
        ];

        foreach ($types as $type) {
            TypeAide::firstOrCreate(['nom' => $type['nom']], $type);
        }
    }
}
