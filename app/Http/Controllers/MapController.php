<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class MapController extends Controller
{
    public function regions(): JsonResponse
    {
        // Compter les bénéficiaires distincts par zone via aides_distribuees → projets_aide
        $byZone = DB::table('aides_distribuees')
            ->join('projets_aide', 'projets_aide.id', '=', 'aides_distribuees.projet_aide_id')
            ->select('projets_aide.zone_cible', DB::raw('COUNT(DISTINCT aides_distribuees.beneficiaire_id) as total'))
            ->whereNotNull('projets_aide.zone_cible')
            ->groupBy('projets_aide.zone_cible')
            ->get();

        // Mapper zone_cible → nom de région (correspond aux noms GADM via JS GADM_TO_FR)
        $byRegion = [];
        foreach ($byZone as $row) {
            $region = $this->zoneToRegion($row->zone_cible);
            if ($region !== null) {
                $byRegion[$region] = ($byRegion[$region] ?? 0) + (int) $row->total;
            }
        }

        $totalBenef  = DB::table('beneficiaires')->count();
        $totalAides  = DB::table('aides_distribuees')->count();

        return response()->json([
            'regions'             => $byRegion,
            'total_beneficiaires' => $totalBenef,
            'total_aides'         => $totalAides,
            'regions_couvertes'   => count($byRegion),
        ]);
    }

    private function zoneToRegion(string $zone): ?string
    {
        // Normalise : minuscules + suppression des accents pour la comparaison
        $z = mb_strtolower(trim($zone));
        $zAscii = iconv('UTF-8', 'ASCII//TRANSLIT', $z) ?: $z;

        // Table de correspondance zone_cible → nom français (= clé dans GADM_TO_FR côté JS)
        // Les clés les plus spécifiques sont testées en premier
        $map = [
            "n'djamena"          => "N'Djamena",
            'ndjamena'           => "N'Djamena",
            'ville de n'         => "N'Djamena",
            'moyen-chari'        => 'Moyen-Chari',
            'moyen chari'        => 'Moyen-Chari',
            'logone occidental'  => 'Logone Occidental',
            'logone oriental'    => 'Logone Oriental',
            'mayo-kebbi est'     => 'Mayo-Kebbi Est',
            'mayo-kebbi ouest'   => 'Mayo-Kebbi Ouest',
            'mayo kebbi est'     => 'Mayo-Kebbi Est',
            'mayo kebbi ouest'   => 'Mayo-Kebbi Ouest',
            'mayo kebbi'         => 'Mayo-Kebbi Ouest',
            'mayo-kebbi'         => 'Mayo-Kebbi Ouest',
            'chari-baguirmi'     => 'Chari-Baguirmi',
            'chari baguirmi'     => 'Chari-Baguirmi',
            'hadjer-lamis'       => 'Hadjer-Lamis',
            'hadjer lamis'       => 'Hadjer-Lamis',
            'wadi fira'          => 'Wadi Fira',
            'wadi-fira'          => 'Wadi Fira',
            'barh el gazel'      => 'Barh El Gazel',
            'barh-el-gazel'      => 'Barh El Gazel',
            'barh'               => 'Barh El Gazel',
            'ennedi est'         => 'Ennedi Est',
            'ennedi ouest'       => 'Ennedi Ouest',
            'ennedi'             => 'Ennedi Ouest',
            'goz beida'          => 'Sila',
            'goz beïda'          => 'Sila',
            'goz'                => 'Sila',
            'sila'               => 'Sila',
            'ouaddai'            => 'Ouaddaï',
            'ouaddaï'            => 'Ouaddaï',
            'tandjile'           => 'Tandjilé',
            'tandjilé'           => 'Tandjilé',
            'mandoul'            => 'Mandoul',
            'salamat'            => 'Salamat',
            'kanem'              => 'Kanem',
            'batha'              => 'Batha',
            'borkou'             => 'Borkou',
            'tibesti'            => 'Tibesti',
            'guera'              => 'Guéra',
            'guéra'              => 'Guéra',
            'lac'                => 'Lac',
        ];

        foreach ($map as $keyword => $region) {
            if (str_contains($z, $keyword) || str_contains($zAscii, $keyword)) {
                return $region;
            }
        }

        return null;
    }
}
