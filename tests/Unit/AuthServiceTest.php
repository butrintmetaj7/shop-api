<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\Auth\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    private AuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authService = new AuthService();
    }

    public function test_register_user_creates_new_user()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
        ];

        $user = $this->authService->registerUser($userData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertTrue(Hash::check('password123', $user->password));
        
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    public function test_register_user_hashes_password()
    {
        $userData = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'plaintext',
        ];

        $user = $this->authService->registerUser($userData);

        $this->assertNotEquals('plaintext', $user->password);
        $this->assertTrue(Hash::check('plaintext', $user->password));
    }

    public function test_attempt_login_returns_user_with_valid_credentials()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $credentials = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $authenticatedUser = $this->authService->attemptLogin($credentials);

        $this->assertInstanceOf(User::class, $authenticatedUser);
        $this->assertEquals($user->id, $authenticatedUser->id);
        $this->assertEquals('test@example.com', $authenticatedUser->email);
    }

    public function test_attempt_login_throws_exception_with_invalid_email()
    {
        $credentials = [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The provided credentials are incorrect.');

        $this->authService->attemptLogin($credentials);
    }

    public function test_attempt_login_throws_exception_with_invalid_password()
    {
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('correctpassword'),
        ]);

        $credentials = [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ];

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('The provided credentials are incorrect.');

        $this->authService->attemptLogin($credentials);
    }
}
