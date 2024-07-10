<?php

namespace Tests\Feature;

use App\Models\Balance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BalanceTest extends TestCase
{
    public function create_balance(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/balance', [
            'user_id' => $user->id,
            'amount' => 100,
        ]);

        $response->assertStatus(201);
    }

    public function test_get_balance(): void
    {
        $user = User::factory()->create();
        $balance = Balance::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->getJson('/balance/' . $balance->id);

        $response->assertStatus(200);
    }

    public function test_update_balance(): void
    {
        $user = User::factory()->create();
        $balance = Balance::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->putJson('/balance/' . $balance->id, [
            'amount' => 200.0,
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'updated' => 'ok',
        ]);
    }
}
