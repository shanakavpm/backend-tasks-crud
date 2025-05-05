<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected $token;

    /**
     * Set up the user and token before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create a user and generate a token for them
        $this->user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);
        $this->token = $this->user->createToken('TestApp')->plainTextToken;
    }

    /**
     * Helper method to set the Authorization header.
     */
    protected function authenticatedHeader(): array
    {
        return ['Authorization' => 'Bearer '.$this->token];
    }

    /**
     * Test user login functionality.
     *
     * @return void
     */
    public function test_login_success()
    {
        $loginData = [
            'email' => $this->user->email,
            'password' => 'password123',
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'User logged in successfully.',
                'data' => [
                    'token' => true,
                    'user' => [
                        'id' => $this->user->id,
                        'name' => $this->user->name,
                    ],
                ],
            ]);
    }

    /**
     * Test user login with incorrect credentials.
     *
     * @return void
     */
    public function test_login_failure()
    {
        $loginData = [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ];

        $response = $this->postJson('/api/login', $loginData);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED)
            ->assertJson([
                'success' => false,
                'message' => 'Incorrect email or password. Please try again.',
            ]);
    }

    /**
     * Test user registration functionality.
     *
     * @return void
     */
    public function test_register_success()
    {
        $registerData = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $registerData);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'User created successfully.',
                'data' => [
                    'token' => true,
                    'user' => [
                        'name' => 'Test User',
                        'id' => true,
                    ],
                ],
            ]);
    }

    /**
     * Test user registration with incorrect password confirmation.
     *
     * @return void
     */
    public function test_register_failure()
    {
        $registerData = [
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'wrongpassword',
        ];

        $response = $this->postJson('/api/register', $registerData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test user logout functionality.
     *
     * @return void
     */
    public function test_logout_success()
    {
        $response = $this->withHeaders($this->authenticatedHeader())
            ->postJson('/api/logout');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'success' => true,
                'message' => 'User logged out successfully.',
            ]);
    }
}
