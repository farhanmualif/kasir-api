<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@mail.com',
            'password' => bcrypt('testpassword')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@mail.com',
            'password' => 'testpassword'
        ]);

        $response->assertStatus(202);
    }
    public function test_user_canot_login_with_incorrect_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@mail.com',
            'password' => bcrypt('testpassword')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test123@mail.com',
            'password' => 'testpassword'
        ]);

        $response->assertStatus(500);
    }

    public function test_login_validation_fails_with_missing_fields()
    {
        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422);
    }
}
