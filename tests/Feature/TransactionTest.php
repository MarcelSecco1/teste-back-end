<?php

namespace Tests\Feature;

use App\Models\Balance;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_transaction(): void
    {

        $payer = User::factory()->create();
        $balance = Balance::factory()->create([
            'user_id' => $payer->id,
            'amount' => 100.00,
        ]);
        $payee = User::factory()->create();

        $response = $this->postJson('/transaction', [
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'value' => 50.00,
        ]);


        $response->assertStatus(201);
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

        $response->assertJsonValidationErrors('value');
        $response->assertStatus(422);
    }


    public function test_fail_transaction_to_shoppeker(): void
    {
        $payer = User::factory()->create(['type' => 'shopkeeper']);
        $payee = User::factory()->create();

        $response = $this->postJson('/transaction', [
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'value' => 100.00,
        ]);

        $response->assertJson([
            'message' => 'Shopkeepers cannot make transactions',
        ]);
        $response->assertStatus(400);
    }

    public function teste_fail_transaction_insuficient_balance(): void
    {
        $payer = User::factory()->create();
        $payee = User::factory()->create();

        Balance::factory()->create([
            'user_id' => $payer->id,
            'amount' => 100.00,
        ]);

        $response = $this->postJson('/transaction', [
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'value' => 1000.00,
        ]);

        $response->assertJson([
            'message' => 'Insufficient balance',
        ]);
        $response->assertStatus(400);
    }
}
