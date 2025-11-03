<?php

namespace App\Entity;

use App\Utils\RegexPatterns;
use InvalidArgumentException;

class Client extends User
{

    public function __construct(
        public ?string $firstName = null,
        public ?string $lastName = null,
        public ?string $phone = null,
    ) {
        $this
            ->setFirstName($firstName)
            ->setLastName($lastName)
            ->setPhone($phone);
    }

    // -------------Getters--------------


    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    //------------Setter--------------

    public function setFirstName(?string $firstName): self
    {
        if ($firstName !== null) {
            $firstName = trim($firstName);

            if (empty($firstName)) {
                throw new InvalidArgumentException("Le prénom est obligatoire.");
            }

            if (!preg_match(RegexPatterns::ONLY_TEXTE_REGEX, $firstName)) {
                throw new InvalidArgumentException("Le prénom doit être compris entre 1 et 60 caractères autorisés.");
            }
            $this->firstName = ucfirst($firstName);
        } else {
            $this->firstName = null;
        }

        $this->updateTimestamp();
        return $this;
    }

    public function setLastName(?string $lastName): self
    {
        if ($lastName !== null) {
            $lastName = trim($lastName);

            if (empty($lastName)) {
                throw new InvalidArgumentException("Le nom est obligatoire.");
            }

            if (!preg_match(RegexPatterns::ONLY_TEXTE_REGEX, $lastName)) {
                throw new InvalidArgumentException("Le nom doit être compris entre 1 et 60 caractères autorisés.");
            }
            $this->lastName = strtoupper($lastName);
        } else {
            $this->lastName = null;
        }

        $this->updateTimestamp();
        return $this;
    }

    public function setPhone(?string $phone): self
    {
        if ($phone !== null) {
            $phone = trim($phone);

            if (!preg_match(RegexPatterns::FRENCH_MOBILE_PHONE, $phone)) {
                throw new InvalidArgumentException("Le numéro de téléphone doit avoir le format téléphone portable français en 06, 07 , +33.");
            }
            $this->phone = $phone;
        } else {
            $this->phone = null;
        }

        $this->updateTimestamp();
        return $this;
    }
}
