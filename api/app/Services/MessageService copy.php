<?php

namespace App\Services;

use App\Models\Server;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ServerService
{
  public function createServer(array $data, User $creator): Server
  {
    return DB::transaction(function () use ($data, $creator) {
      $server = Server::create([
        'name' => $data['name'],
        'owner_id' => $creator->id,
      ]);

      $server->users()->attach($creator->id, [
        'role' => 'host',
        'joined_at' => now()
      ]);

      return $server;
    });
  }
  public function assignRole(Server $server, User $user, string $role)
  {
    $roleConfig = config("server_roles.roles.$role");

    if (!$roleConfig) {
      throw new \Exception("Invalid role");
    }

    $existingCount = $server->users()->wherePivot('role', $role)->count();

    if ($roleConfig['max'] && $existingCount >= $roleConfig['max']) {
      throw new \Exception("Maximum number of {$roleConfig['name']}s reached.");
    }

    $server->users()->updateExistingPivot($user->id, [
      'role' => $role,
    ]);
  }
  // public function removeUser(Server $server, User $user)
  // {
  //   $server->users()->detach($user->id);
  // }
}