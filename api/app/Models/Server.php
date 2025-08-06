<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    //
    public function users()
    {
        return $this->belongsToMany(User::class, 'server_users')->withPivot('role', 'joined_at', 'banned_at');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
