<?php

namespace App\DTO;

class SearchClientDTO
{

    public ?int $id = null;
    public ?string $lastName = null;
    public ?string $email = null;

    public function getUserId(): ?int
    {
        return $this->id;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }
}
