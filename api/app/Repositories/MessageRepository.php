<?php

namespace App\Repositories;


use App\Models\Message;

class MessageRepository
{
  public function store(array $data): Message
  {
    return Message::create($data);
  }

  public function getConversation($user1, $user2)
  {
    return Message::where(function ($query) use ($user1, $user2) {
      $query->where('sender_id', $user1)
        ->where('receiver_id', $user2);
    })
      ->orWhere(function ($query) use ($user1, $user2) {
        $query->where('sender_id', $user2)
          ->where('receiver_id', $user1);
      })
      ->orderBy('created_at', 'asc')
      ->get();
  }
}