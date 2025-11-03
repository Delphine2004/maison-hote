<?php

namespace App\Entity;

use App\Utils\RegexPatterns;

use DateTimeImmutable;
use InvalidArgumentException;

class Extra
{

    public function __construct(
        public ?int $extraId = null,
        public ?string $extraName = null,
        public ?string $description = null,
        public ?float $rate = null,

        public ?DateTimeImmutable $createdAt = null,
        public ?DateTimeImmutable $updatedAt = null
    ) {
        $this->setExtraName($extraName)
            ->setDescription($description)
            ->setRate($rate);

        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new DateTimeImmutable();
    }

    // -------------Getters--------------
    public function getExtraId(): ?int
    {
        return $this->extraId;
    }

    public function getExtraName(): ?string
    {
        return $this->extraName;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getRate(): ?float
    {
        return $this->rate;
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
    public function setExtraId(?int $extraId): self
    {
        $this->extraId = $extraId;
        return $this;
    }

    public function setExtraName(?string $extraName): self
    {
        if ($extraName !== null) {
            $extraName = trim($extraName);

            if (empty($extraName)) {
                throw new InvalidArgumentException("Le nom est obligatoire.");
            }

            if (!preg_match(RegexPatterns::ONLY_TEXTE_REGEX, $extraName)) {
                throw new InvalidArgumentException("Le nom doit être compris entre 1 et 60 caractères autorisés.");
            }
            $this->extraName = ucfirst($extraName);
        } else {
            $this->extraName = null;
        }
        return $this;
    }


    public function setDescription(?string $description): self
    {
        if ($description !== null) {
            $description = trim($description);

            if (empty($description)) {
                throw new InvalidArgumentException("La description est obligatoire.");
            }

            if (!preg_match(RegexPatterns::FREE_TEXT_REGEX, $description)) {
                throw new InvalidArgumentException("La description doit être compris entre 1 et 255 caractères autorisés.");
            }
            $this->description = ucfirst($description);
        } else {
            $this->description = null;
        }
        return $this;
    }

    public function setRate(?float $rate): self
    {
        if ($rate < 0 || $rate >= 100) {
            throw new InvalidArgumentException("Le prix doit être supérieure à 0 et inférieure à 100.");
        }
        $this->rate = $rate;
        return $this;
    }
}
