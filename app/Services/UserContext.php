<?php

namespace App\Services;

class UserContext 
{
    private ?string $userId = null;
    private ?string $role = null;

    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    public function getUserId(): ?string
    {
        return $this->userId;
    }

    public function setRole(string $role): void
    {
        $this->role = $role;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }
}