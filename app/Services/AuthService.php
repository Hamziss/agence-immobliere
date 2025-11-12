<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
  public function __construct(
    private UserRepository $userRepository
  ) {}

  /**
   * Register a new user
   */
  public function register(string $name, string $email, string $password, string $role = 'guest'): User
  {
    // Validate role
    if (!in_array($role, ['admin', 'agent', 'guest'])) {
      throw new \Exception('Rôle invalide.');
    }

    // Create user via repository
    $user = $this->userRepository->create([
      'name' => $name,
      'email' => $email,
      'password' => Hash::make($password),
      'role' => $role,
    ]);

    return $user;
  }

  /**
   * Login a user
   */
  public function login(string $email, string $password): array
  {
    $user = $this->userRepository->findByEmail($email);

    if (!$user || !Hash::check($password, $user->password)) {
      throw ValidationException::withMessages([
        'email' => ['Les identifiants sont incorrects.'],
      ]);
    }

    // Create token
    $token = $user->createToken('auth-token')->plainTextToken;

    return [
      'user' => $user,
      'token' => $token,
    ];
  }

  /**
   * Logout a user (revoke all tokens)
   */
  public function logout(User $user): void
  {
    $user->tokens()->delete();
  }

  /**
   * Logout from current device only
   */
  public function logoutCurrentDevice(User $user, string $currentToken): void
  {
    $user->tokens()->where('token', hash('sha256', $currentToken))->delete();
  }
}
