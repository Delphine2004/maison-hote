<?php

namespace App\Entity;

use App\Repository\RatePeriodRepository;
use App\Utils\RegexPatterns;

use InvalidArgumentException;
use DateTimeImmutable;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RatePeriodRepository::class)]
class RatePeriod
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?DateTimeImmutable $startingDate = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?DateTimeImmutable $endingDate = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        if ($this->createdAt === null) {
            $this->createdAt = new DateTimeImmutable();
        }

        if ($this->updatedAt === null) {
            $this->updatedAt =  new DateTimeImmutable();
        }
    }

    // ---- Mise à jour de la date de modification

    private function updateTimestamp(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        if ($name !== null) {
            $name = trim($name);

            if (empty($name)) {
                throw new InvalidArgumentException("Le nom est obligatoire.");
            }

            if (!preg_match(RegexPatterns::ONLY_TEXTE_REGEX, $name)) {
                throw new InvalidArgumentException("Le nom doit être compris entre 1 et 60 caractères autorisés.");
            }
            $this->name = strtoupper($name);
        } else {
            $this->name = null;
        }

        $this->updateTimestamp();
        return $this;
    }

    public function getStartingDate(): ?DateTimeImmutable
    {
        return $this->startingDate;
    }

    public function setStartingDate(DateTimeImmutable $startingDate): static
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

    public function getEndingDate(): ?DateTimeImmutable
    {
        return $this->endingDate;
    }

    public function setEndingDate(DateTimeImmutable $endingDate): static
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

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }


    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
