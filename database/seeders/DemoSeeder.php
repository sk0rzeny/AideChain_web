<?php

namespace Database\Seeders;

use App\Models\DemandeAdhesion;
use App\Models\Ong;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Comptes de démonstration :
 *
 *   admin@aidechain.td          / password  → super_admin
 *   representant@ong-espoir.td  / password  → ong_representant (ONG Espoir Tchad — active)
 *   representant@ong-soleil.td  / password  → ong_representant (ONG Soleil du Sahel — active)
 *   agent@ong-espoir.td         / password  → ong_agent (rattaché à ONG Espoir Tchad)
 *   agent@ong-soleil.td         / password  → ong_agent (rattaché à ONG Soleil du Sahel)
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $roleAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $roleRep   = Role::firstOrCreate(['name' => 'ong_representant']);
        $roleAgent = Role::firstOrCreate(['name' => 'ong_agent']);

        // ── Super Admin ──────────────────────────────────────────────
        User::firstOrCreate(
            ['email' => 'admin@aidechain.td'],
            [
                'name'              => 'Administrateur AideChain',
                'password'          => 'password',
                'role_id'           => $roleAdmin->id,
                'email_verified_at' => now(),
            ]
        );

        // ── Représentant ONG Espoir Tchad ─────────────────────────────
        $rep1 = User::firstOrCreate(
            ['email' => 'representant@ong-espoir.td'],
            [
                'name'              => 'Fatima Mahamat',
                'password'          => 'password',
                'role_id'           => $roleRep->id,
                'email_verified_at' => now(),
            ]
        );

        $ong1 = Ong::firstOrCreate(
            ['email' => 'contact@ong-espoir.td'],
            [
                'nom'             => 'ONG Espoir Tchad',
                'telephone'       => '+235 66 00 11 22',
                'adresse'         => 'N\'Djamena, Quartier Sabangali',
                'statut'          => 'active',
                'representant_id' => $rep1->id,
            ]
        );

        // ── Représentant ONG Soleil du Sahel ──────────────────────────
        $rep2 = User::firstOrCreate(
            ['email' => 'representant@ong-soleil.td'],
            [
                'name'              => 'Ibrahim Oumar',
                'password'          => 'password',
                'role_id'           => $roleRep->id,
                'email_verified_at' => now(),
            ]
        );

        $ong2 = Ong::firstOrCreate(
            ['email' => 'contact@ong-soleil.td'],
            [
                'nom'             => 'ONG Soleil du Sahel',
                'telephone'       => '+235 66 33 44 55',
                'adresse'         => 'Moundou, Avenue de la Paix',
                'statut'          => 'active',
                'representant_id' => $rep2->id,
            ]
        );

        // ── Agent terrain rattaché à ONG Espoir Tchad ─────────────────
        $agent = User::firstOrCreate(
            ['email' => 'agent@ong-espoir.td'],
            [
                'name'              => 'Amina Nassour',
                'password'          => 'password',
                'role_id'           => $roleAgent->id,
                'ong_id'            => $ong1->id,
                'email_verified_at' => now(),
            ]
        );

        DemandeAdhesion::firstOrCreate(
            ['user_id' => $agent->id, 'ong_id' => $ong1->id],
            ['statut' => 'accepted']
        );

        // ── Agent terrain rattaché à ONG Soleil du Sahel ──────────────
        $agent2 = User::firstOrCreate(
            ['email' => 'agent@ong-soleil.td'],
            [
                'name'              => 'Moussa Kanem',
                'password'          => 'password',
                'role_id'           => $roleAgent->id,
                'ong_id'            => $ong2->id,
                'email_verified_at' => now(),
            ]
        );

        DemandeAdhesion::firstOrCreate(
            ['user_id' => $agent2->id, 'ong_id' => $ong2->id],
            ['statut' => 'accepted']
        );
    }
}
