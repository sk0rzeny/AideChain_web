<?php

namespace Tests\Unit;

use App\Services\BlockchainService;
use PHPUnit\Framework\TestCase;

class BlockchainServiceTest extends TestCase
{
    private BlockchainService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BlockchainService();
    }

    public function test_same_inputs_produce_same_hash(): void
    {
        $hash1 = $this->service->computeHash('Ali', 'Hassan', '1990-03-15');
        $hash2 = $this->service->computeHash('Ali', 'Hassan', '1990-03-15');
        $this->assertSame($hash1, $hash2);
    }

    public function test_hash_is_case_insensitive(): void
    {
        $hash1 = $this->service->computeHash('ALI', 'HASSAN', '1990-03-15');
        $hash2 = $this->service->computeHash('ali', 'hassan', '1990-03-15');
        $this->assertSame($hash1, $hash2);
    }

    public function test_hash_is_insensitive_to_extra_spaces(): void
    {
        $hash1 = $this->service->computeHash('  Ali  ', '  Hassan  ', '1990-03-15');
        $hash2 = $this->service->computeHash('Ali', 'Hassan', '1990-03-15');
        $this->assertSame($hash1, $hash2);
    }

    public function test_different_first_names_produce_different_hashes(): void
    {
        $hash1 = $this->service->computeHash('Ali', 'Hassan', '1990-03-15');
        $hash2 = $this->service->computeHash('Ibrahim', 'Hassan', '1990-03-15');
        $this->assertNotSame($hash1, $hash2);
    }

    public function test_different_last_names_produce_different_hashes(): void
    {
        $hash1 = $this->service->computeHash('Ali', 'Hassan', '1990-03-15');
        $hash2 = $this->service->computeHash('Ali', 'Mahamat', '1990-03-15');
        $this->assertNotSame($hash1, $hash2);
    }

    public function test_different_birth_dates_produce_different_hashes(): void
    {
        $hash1 = $this->service->computeHash('Ali', 'Hassan', '1990-03-15');
        $hash2 = $this->service->computeHash('Ali', 'Hassan', '1991-03-15');
        $this->assertNotSame($hash1, $hash2);
    }

    public function test_hash_is_64_char_lowercase_hex(): void
    {
        $hash = $this->service->computeHash('Ali', 'Hassan', '1990-03-15');
        $this->assertSame(64, strlen($hash));
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $hash);
    }
}
