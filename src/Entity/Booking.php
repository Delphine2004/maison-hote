<?php

namespace App\Entity;

use App\Enum\BookingStatus;

use InvalidArgumentException;
use DateTimeImmutable;

use App\Repository\BookingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?DateTimeImmutable $startingDate = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?DateTimeImmutable $endingDate = null;

    #[ORM\Column]
    private ?float $totalAmount = null;

    #[ORM\Column]
    private ?int $paxNumber = null;

    #[ORM\Column(length: 50)]
    private ?BookingStatus $bookingStatus = null;

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

    protected function updateTimestamp(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }


    public function getId(): ?int
    {
        return $this->id;
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
            throw new \InvalidArgumentException("La date de départ doit être dans le futur.");
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
            throw new \InvalidArgumentException("La date de fin de réservation doit être supérieure à la date du début.");
        }

        $this->endingDate = $endingDate;
        $this->updateTimestamp();
        return $this;
    }

    public function getTotalAmount(): ?float
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(float $totalAmount): static
    {
        if ($totalAmount < 0 || $totalAmount >= 5000) {
            throw new \InvalidArgumentException("Le prix doit être supérieure à 0 et inférieure à 5000.");
        }
        $this->totalAmount = $totalAmount;
        $this->updateTimestamp();
        return $this;
    }

    public function getPaxNumber(): ?int
    {
        return $this->paxNumber;
    }

    public function setPaxNumber(int $paxNumber): static
    {
        if ($paxNumber < 0 || $paxNumber >= 100) {
            throw new \InvalidArgumentException("Le nombre de personne doit être entre 1 et 2.");
        }
        $this->paxNumber = $paxNumber;
        $this->updateTimestamp();
        return $this;
    }

    public function getBookingStatus(): ?BookingStatus
    {
        return $this->bookingStatus;
    }

    public function setBookingStatus(BookingStatus $bookingStatus): static
    {
        $this->bookingStatus = $bookingStatus;
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
