<?php

namespace App\Entity;

use App\Enum\Role;
use App\Utils\RegexPatterns;

use DateTimeImmutable;
use InvalidArgumentException;

class User
{

    public function __construct(
        public ?int $userId = null,
        public ?string $email = null,
        public ?string $password = null,
        public ?Role $role = null,

        public ?DateTimeImmutable $createdAt = null,
        public ?DateTimeImmutable $updatedAt = null,

        public bool $isFromDatabase = false
    ) {
        $this
            ->setUserEmail($email)
            ->setUserRoles($role);

        if ($this->isFromDatabase) {
            $this->password = $password;
        } else {
            $this->setUserPassword($password);
        }

        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new DateTimeImmutable();
    }

    // -------------Getters--------------

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getUserEmail(): ?string
    {
        return $this->email;
    }

    public function getUserPassword(): ?string
    {
        return $this->password;
    }

    public function getUserRole(): ?Role
    {
        return $this->role;
    }

    public function getUserCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUserUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    //------------Setter--------------

    public function setUserId(?int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    public function setUserEmail(?string $email): self
    {

        if ($email !== null) {
            $email = trim($email);

            if (empty($email)) {
                throw new InvalidArgumentException("L'email est obligatoire. ");
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException("L'email est invalide.");
            }
            $this->email = strtolower($email);
        } else {
            $this->email = null;
        }

        $this->updateTimestamp();
        return $this;
    }

    public function setUserPassword(?string $password, bool $isHashed = false): self
    {

        if ($password === null) {
            $this->password = null;
            return $this;
        }

        $passwordToStore = trim($password);

        if (!$isHashed) {
            $this->validatePassword($passwordToStore);
            $passwordToStore = password_hash($passwordToStore, PASSWORD_DEFAULT);
            //var_dump('INSCRIPTION - HASH stocké:', $passwordToStore);
        }

        $this->password = $passwordToStore;
        $this->updateTimestamp();
        return $this;
    }

    public function setHashedPasswordFromDb(?string $hashedPassword): self
    {
        // On assume que le mot de passe est déjà un HASH stocké.
        $this->password = $hashedPassword;
        return $this;
    }

    public function setUserRoles(?Role $role): self
    {
        $this->role = $role;
        $this->updateTimestamp();
        return $this;
    }


    // ----- Méthodes de vérification -----

    private function validatePassword(string $password): void
    {
        $password = trim($password);

        if (!preg_match(RegexPatterns::PASSWORD, $password)) {

            throw new InvalidArgumentException('Le mot de passe doit contenir au minimun une minuscule, une majuscule, un chiffre, un caractère spécial et contenir 12 caractéres au total.');
        };
    }

    // ---- Mise à jour de la date de modification

    protected function updateTimestamp(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
