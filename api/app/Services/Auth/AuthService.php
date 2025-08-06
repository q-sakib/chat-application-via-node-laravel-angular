<?php

namespace App\Services\Auth;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;

class AuthService
{
  protected UserRepository $users;

  public function __construct(UserRepository $users)
  {
    $this->users = $users;
  }

  public function register(array $data): string
  {
    $user = $this->users->create($data);
    return $user->createToken('auth-token')->plainTextToken;
  }

  // public function login(array $credentials): ?string
  // {
  //   $user = $this->users->findByEmail($credentials['email']);
  //   if (!$user || !Hash::check($credentials['password'], $user->password)) {
  //     return null;
  //   }

  //   return $user->createToken('auth-token')->plainTextToken;
  // }

  public function login(array $credentials): ?string
  {
    $user = $this->users->findForLogin($credentials);

    if (!$user || !isset($credentials['password']) || !Hash::check($credentials['password'], $user->password)) {
      return null;
    }

    return $user->createToken('auth-token')->plainTextToken;
  }
}
