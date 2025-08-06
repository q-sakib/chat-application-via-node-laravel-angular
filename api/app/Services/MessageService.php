<?php

namespace App\Services;

use App\Repositories\MessageRepository;

class MessageService
{
  protected MessageRepository $repo;

  public function __construct(MessageRepository $repo)
  {
    $this->repo = $repo;
  }

  public function sendMessage(array $data)
  {
    return $this->repo->store($data);
  }

  public function getConversation(int $authUserId, int $otherUserId)
  {
    return $this->repo->getConversation($authUserId, $otherUserId);
  }
}
