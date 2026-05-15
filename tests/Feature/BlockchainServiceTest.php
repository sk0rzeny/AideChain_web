<?php

namespace Tests\Feature;

use App\Models\AideDistribuee;
use App\Models\Beneficiaire;
use App\Models\Ong;
use App\Models\Role;
use App\Models\TypeAide;
use App\Models\User;
use App\Services\BlockchainService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlockchainServiceTest extends TestCase
{
    use RefreshDatabase;

    private BlockchainService $service;
    private Ong $ong;
    private TypeAide $typeAide;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BlockchainService();

        $role = Role::create(['name' => 'ong_representant']);
        $user = User::factory()->create(['role_id' => $role->id]);
        $this->ong = Ong::create([
            'nom'             => 'ONG Test',
            'email'           => 'test@ong.td',
            'statut'          => 'active',
            'representant_id' => $user->id,
        ]);
        $this->typeAide = TypeAide::create(['nom' => 'Alimentaire']);
    }

    private function creerBeneficiaire(string $hash): Beneficiaire
    {
        return Beneficiaire::create([
            'identity_hash'  => $hash,
            'prenom'         => 'Ali',
            'nom'            => 'Hassan',
            'date_naissance' => '1990-03-15',
            'genre'          => 'homme',
            'categorie'      => 'adulte',
            'ong_id'         => $this->ong->id,
        ]);
    }

    public function test_find_beneficiaire_returns_null_when_not_exists(): void
    {
        $this->assertNull($this->service->findBeneficiaire('hash_inexistant'));
    }

    public function test_find_beneficiaire_returns_record_when_exists(): void
    {
        $hash = $this->service->computeHash('Ali', 'Hassan', '1990-03-15');
        $this->creerBeneficiaire($hash);

        $found = $this->service->findBeneficiaire($hash);
        $this->assertNotNull($found);
        $this->assertSame($hash, $found->identity_hash);
    }

    public function test_is_duplicate_returns_false_when_no_aide_exists(): void
    {
        $hash = $this->service->computeHash('Ali', 'Hassan', '1990-03-15');
        $this->creerBeneficiaire($hash);

        $this->assertFalse($this->service->isDuplicate($hash, $this->typeAide->id));
    }

    public function test_is_duplicate_returns_true_when_active_aide_exists(): void
    {
        $hash = $this->service->computeHash('Ali', 'Hassan', '1990-03-15');
        $beneficiaire = $this->creerBeneficiaire($hash);

        $this->service->distributeAide(
            $beneficiaire->id,
            $this->typeAide->id,
            $this->ong->id,
            now()->toDateString(),
            now()->addDays(30)->toDateString()
        );

        $this->assertTrue($this->service->isDuplicate($hash, $this->typeAide->id));
    }

    public function test_is_duplicate_returns_false_when_aide_is_expired(): void
    {
        $hash = $this->service->computeHash('Ali', 'Hassan', '1990-03-15');
        $beneficiaire = $this->creerBeneficiaire($hash);

        AideDistribuee::create([
            'beneficiaire_id'   => $beneficiaire->id,
            'type_aide_id'      => $this->typeAide->id,
            'ong_id'            => $this->ong->id,
            'date_distribution' => now()->subDays(60)->toDateString(),
            'date_expiration'   => now()->subDay()->toDateString(),
            'hash_transaction'  => hash('sha256', 'aide_expiree'),
        ]);

        $this->assertFalse($this->service->isDuplicate($hash, $this->typeAide->id));
    }

    public function test_distribute_aide_creates_immutable_record(): void
    {
        $hash = $this->service->computeHash('Ali', 'Hassan', '1990-03-15');
        $beneficiaire = $this->creerBeneficiaire($hash);

        $aide = $this->service->distributeAide(
            $beneficiaire->id,
            $this->typeAide->id,
            $this->ong->id,
            now()->toDateString(),
            now()->addDays(30)->toDateString(),
            'Notes de test'
        );

        $this->assertInstanceOf(AideDistribuee::class, $aide);
        $this->assertSame(64, strlen($aide->hash_transaction));
        $this->assertDatabaseHas('aides_distribuees', [
            'beneficiaire_id' => $beneficiaire->id,
            'type_aide_id'    => $this->typeAide->id,
            'ong_id'          => $this->ong->id,
        ]);
    }

    public function test_get_duplicate_info_returns_aide_with_relations(): void
    {
        $hash = $this->service->computeHash('Ali', 'Hassan', '1990-03-15');
        $beneficiaire = $this->creerBeneficiaire($hash);

        $this->service->distributeAide(
            $beneficiaire->id,
            $this->typeAide->id,
            $this->ong->id,
            now()->toDateString(),
            now()->addDays(30)->toDateString()
        );

        $info = $this->service->getDuplicateInfo($hash, $this->typeAide->id);
        $this->assertNotNull($info);
        $this->assertSame($this->ong->id, $info->ong_id);
        $this->assertNotNull($info->ong);
        $this->assertSame('Alimentaire', $info->typeAide->nom);
    }

    public function test_different_type_aide_is_not_a_duplicate(): void
    {
        $hash = $this->service->computeHash('Ali', 'Hassan', '1990-03-15');
        $beneficiaire = $this->creerBeneficiaire($hash);
        $autreType = TypeAide::create(['nom' => 'Sanitaire']);

        $this->service->distributeAide(
            $beneficiaire->id,
            $this->typeAide->id,
            $this->ong->id,
            now()->toDateString(),
            now()->addDays(30)->toDateString()
        );

        // Aide alimentaire existe mais on vérifie aide sanitaire → pas de doublon
        $this->assertFalse($this->service->isDuplicate($hash, $autreType->id));
    }
}
