<?php

namespace App\Entity;

use App\Utils\RegexPatterns;

use DateTimeImmutable;
use InvalidArgumentException;

class RatePeriod
{

    public function __construct(
        public ?int $periodId = null,
        public ?string $periodName = null,
        public ?DateTimeImmutable $startingDate = null,
        public ?DateTimeImmutable $endingDate = null,

        public ?DateTimeImmutable $createdAt = null,
        public ?DateTimeImmutable $updatedAt = null
    ) {
        $this->setPeriodName($periodName)
            ->setStartingDate($startingDate)
            ->setEndingDate($endingDate);

        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new DateTimeImmutable();
    }

    // -------------Getters--------------


    public function getPeriodId(): ?int
    {
        return $this->periodId;
    }

    public function getPeriodName(): ?string
    {
        return $this->periodName;
    }

    public function getStartingDate(): ?DateTimeImmutable
    {
        return $this->startingDate;
    }

    public function getEndingDate(): ?DateTimeImmutable
    {
        return $this->endingDate;
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

    public function setPeriodId(?int $periodId): self
    {
        $this->periodId = $periodId;
        return $this;
    }

    public function setPeriodName(?string $periodName): self
    {
        if ($periodName !== null) {
            $periodName = trim($periodName);

            if (empty($periodName)) {
                throw new InvalidArgumentException("Le nom est obligatoire.");
            }

            if (!preg_match(RegexPatterns::ONLY_TEXTE_REGEX, $periodName)) {
                throw new InvalidArgumentException("Le nom doit être compris entre 1 et 60 caractères autorisés.");
            }
            $this->periodName = strtoupper($periodName);
        } else {
            $this->periodName = null;
        }

        $this->updateTimestamp();
        return $this;
    }

    public function setStartingDate(?DateTimeImmutable $startingDate): self
    {
        if ($startingDate === null) {
            $this->startingDate = null;
            return $this;
        }

        $timezone = new \DateTimeZone('Europe/Paris');
        $today = (new DateTimeImmutable('now', $timezone))->setTime(0, 0);


        if ($startingDate < $today) {
            throw new InvalidArgumentException("Le début de la période doit être dans le futur.");
        }

        $this->startingDate = $startingDate;
        $this->updateTimestamp();
        return $this;
    }

    public function setEndingDate(?DateTimeImmutable $endingDate): self
    {
        if ($endingDate === null) {
            $this->endingDate = null;
            return $this;
        }

        if ($endingDate !== null && isset($this->departureDateTime) && $endingDate <= $this->startingDate) {
            throw new InvalidArgumentException("La date de fin doit être supérieure à la date du début.");
        }

        $this->endingDate = $endingDate;
        $this->updateTimestamp();
        return $this;
    }

    // ---- Mise à jour de la date de modification

    private function updateTimestamp(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
