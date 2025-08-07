<?php

namespace App\Services\Auth;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;





use App\Models\User;

use Illuminate\Support\Str;
use Carbon\Carbon;






class AuthService

{
  protected UserRepository $users;

  public function __construct(UserRepository $users)
  {
    $this->users = $users;
  }

  public function register(array $data): array
  {
    $user = $this->users->create($data);
    return $this->issueTokens($user);
  }

  public function login(array $credentials): ?array
  {
    $user = $this->users->findForLogin($credentials);

    if (!$user || !isset($credentials['password']) || !Hash::check($credentials['password'], $user->password)) {
      return null;
    }

    return $this->issueTokens($user);
  }

  public function refresh(string $refreshToken): ?array
  {
    $user = $this->users->findByValidRefreshToken($refreshToken);
    if (!$user) return null;

    return $this->issueTokens($user);
  }

  public function logout(User $user): void
  {
    $user->tokens()->delete();
    $user->refresh_token = null;
    $user->refresh_token_expires_at = null;
    $user->save();
  }

  protected function issueTokens(User $user): array
  {
    $user->tokens()->delete();

    $accessToken = $user->createToken('auth-token')->plainTextToken;

    $refreshTokenPlain = Str::random(64);
    $user->refresh_token = Hash::make($refreshTokenPlain);
    $user->refresh_token_expires_at = Carbon::now()->addDays(7);
    $user->save();

    return [
      'user' => $user,
      'access_token' => $accessToken,
      'refresh_token' => $refreshTokenPlain,
      'expires_in' => config('sanctum.expiration') ? config('sanctum.expiration') * 60 : null,
    ];
  }
}




// {
//   protected UserRepository $users;

//   public function __construct(UserRepository $users)
//   {
//     $this->users = $users;
//   }

//   public function register(array $data): string
//   {
//     $user = $this->users->create($data);
//     return $user->createToken('auth-token')->plainTextToken;
//   }

//   // public function login(array $credentials): ?string
//   // {
//   //   $user = $this->users->findByEmail($credentials['email']);
//   //   if (!$user || !Hash::check($credentials['password'], $user->password)) {
//   //     return null;
//   //   }

//   //   return $user->createToken('auth-token')->plainTextToken;
//   // }

//   public function login(array $credentials): ?string
//   {
//     $user = $this->users->findForLogin($credentials);

//     if (!$user || !isset($credentials['password']) || !Hash::check($credentials['password'], $user->password)) {
//       return null;
//     }

//     return $user->createToken('auth-token')->plainTextToken;
//   }
// }
