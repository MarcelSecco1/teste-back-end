<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    public function test_user_route_response(): void
    {

        $response = $this->getJson('/user');

        $response->assertStatus(200);
    }

    public function test_user_store_route_response(): void
    {
        User::factory()->count(10)->create();

        $reponse = $this->getJson('/user');

        $reponse->assertJsonCount(10, 'data');
    }

    public function test_user_show_route_response(): void
    {
        $user = User::factory()->create();

        $response = $this->get('/user/' . $user->id);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                "data" => [
                    "id",
                    "name",
                    "email",
                    "cpf",
                    "type",
                    "created_at",
                ]
            ]);
    }

    public function test_user_update_route_response(): void
    {
        $user = User::factory()->create();

        $response = $this->putJson('/user/' . $user->id, [
            'name' => 'John Doe',
            'email' => 'johndoe@gmail.com'
        ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                "updated" => "ok",
            ]);
    }

    public function test_user_destroy_route_response(): void
    {
        $user = User::factory()->create();

        $response = $this->delete('/user/' . $user->id);

        $response
            ->assertStatus(200)
            ->assertJson([
                "deleted" => "ok",
            ]);
    }

    public function test_user_store_route_validation(): void
    {
        $response = $this->postJson('/user', [
            'name' => 'John Doe',
            'email' => '',
            'cpf' => '123.456.789-09',
            'password' => '12345678',
        ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors([
                'email',
            ]);
    }

    public function test_url_get_user(): void
    {
        $response = $this->getJson('/user/fake_value');

        $response->assertStatus(500);
    }
}
