<?php

namespace App\Repositories;

use App\Models\User;

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
    if (isset($credentials['email'])) {
      return $this->findByEmail($credentials['email']);
    }

    if (isset($credentials['username'])) {
      return $this->findByUsername($credentials['username']);
    }

    return null;
  }
}