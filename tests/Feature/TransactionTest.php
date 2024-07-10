<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    // use RefreshDatabase;

    public function test_create_transaction(): void
    {

        $payer = User::factory()->create();
        $payee = User::factory()->create();

        $response = $this->postJson('/transaction', [
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'value' => 100.00,
        ]);

        // dd($response->getContent());

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'id',
            'payer_id',
            'payee_id',
            'value',
        ]);
    }

    public function test_create_transaction_with_invalid_value(): void
    {
        $payer = User::factory()->create();
        $payee = User::factory()->create();

        $response = $this->postJson('/transaction', [
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'value' => 0.00,
        ]);

        // dd($response->getContent());
        $response->assertJsonValidationErrors('value');
        $response->assertStatus(422);
    }
}
