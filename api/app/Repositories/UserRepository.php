<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{
  public function create(array $data): User
  {
    $data['password'] = bcrypt($data['password']);
    return User::create($data);
  }

  public function findByUsername(string $username): ?User
  {
    return User::where('username', $username)->first();
  }

  public function findByEmail(string $email): ?User
  {
    return User::where('email', $email)->first();
  }

  public function findForLogin(array $credentials): ?User
  {
    return $credentials['email'] ?? $credentials['username']
      ? User::where('email', $credentials['email'] ?? '')
      ->orWhere('username', $credentials['username'] ?? '')
      ->first()
      : null;
  }

  public function findByValidRefreshToken(string $refreshToken): ?User
  {
    $users = User::whereNotNull('refresh_token')->get();

    foreach ($users as $user) {
      if (
        Hash::check($refreshToken, $user->refresh_token) &&
        $user->refresh_token_expires_at &&
        $user->refresh_token_expires_at->isFuture()
      ) {
        return $user;
      }
    }

    return null;
  }

  public function allUsers(array $data)
  {
    $perPage = $data['per_page'] ?? 10;
    return User::paginate($perPage);
  }
}