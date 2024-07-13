<?php

namespace Tests\Feature;

use App\Models\Balance;
use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_transaction(): void
    {
        User::unsetEventDispatcher();
        $payer = User::factory()->create();
        $payee = User::factory()->create();

        User::observe(UserObserver::class);

        Balance::factory()->create([
            'user_id' => $payer->id,
            'amount' => 100.00,
        ]);

        Balance::factory()->create([
            'user_id' => $payee->id,
            'amount' => 0.00,
        ]);

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

    public function test_fail_transaction_insuficient_balance(): void
    {
        $payer = User::factory()->create();
        $payee = User::factory()->create();


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

    public function test_transaction_transfer_balance(): void
    {
        User::unsetEventDispatcher();

        $payer = User::factory()->create();
        $payee = User::factory()->create();

        User::observe(UserObserver::class);


        $balancePayer = Balance::factory()->create([
            'user_id' => $payer->id,
            'amount' => 100.00,
        ]);

        $balancePayee = Balance::factory()->create([
            'user_id' => $payee->id,
            'amount' => 0.00,
        ]);

        $response = $this->postJson('/transaction', [
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'value' => 50.00,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('balances', [
            'id' => $balancePayer->id,
            'amount' => 50.00,
        ]);
        $this->assertDatabaseHas('balances', [
            'id' => $balancePayee->id,
            'amount' => 50.00,
        ]);
    }

    public function test_updated_transaction_completed(){
        User::unsetEventDispatcher();

        $payer = User::factory()->create();
        $payee = User::factory()->create();

        User::observe(UserObserver::class);

        $balancePayer = Balance::factory()->create([
            'user_id' => $payer->id,
            'amount' => 100.00,
        ]);

        $balancePayee = Balance::factory()->create([
            'user_id' => $payee->id,
            'amount' => 0.00,
        ]);

        $response = $this->postJson('/transaction', [
            'payer_id' => $payer->id,
            'payee_id' => $payee->id,
            'value' => 50.00,
        ]);

        $response->assertStatus(201);

        $response = $this->putJson('/transaction/' . $response->json('id'), [
            'value' => 100.00,
        ]);

        $response->assertJson([
            'message' => 'Transaction already completed',
        ]);
        $response->assertStatus(400);

    }
}
