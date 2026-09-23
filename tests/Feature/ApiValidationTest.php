<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApiValidationTest extends TestCase
{
    public function test_invalid_public_blockchain_hash_returns_unprocessable_entity(): void
    {
        $response = $this->getJson('/api/blockchain/verify?hash=short');

        $response
            ->assertUnprocessable()
            ->assertJsonPath('status', false)
            ->assertJsonStructure(['errors' => ['hash']]);
    }
}
