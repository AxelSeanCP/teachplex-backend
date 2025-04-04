<?php

namespace App\Services;

class UserContext 
{
    private ?string $userId = null;

    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }
}