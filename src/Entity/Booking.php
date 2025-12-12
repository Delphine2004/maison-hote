<?php

namespace App\Entity;

use App\Repository\BookingRepository;

use App\Enum\BookingStatus;

use InvalidArgumentException;
use DateTimeImmutable;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: BookingRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotNull]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?DateTimeImmutable $startingDate = null;

    #[Assert\NotNull]
    #[Assert\GreaterThan(propertyPath: 'startingDate')]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?DateTimeImmutable $endingDate = null;

    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    #[ORM\Column(type: Types::INTEGER)]
    private int $totalAmountCents = 0;

    #[Assert\NotNull]
    #[ORM\Column(type: Types::STRING, length: 50, enumType: BookingStatus::class)]
    private ?BookingStatus $status = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'bookings')]
    private ?Client $client = null;

    #[ORM\ManyToOne(targetEntity: Room::class, inversedBy: 'bookings')]
    private ?Room $room = null;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'bookingsCreated')]
    private ?Client $createdBy = null;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'bookingsUpdatedByClient')]
    private ?Client $updatedByClient = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'bookingsUpdatedByUser')]
    private ?User $updatedByUser = null;


    public function __construct() {}


    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
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
            throw new InvalidArgumentException("La date de début doit être dans le futur.");
        }

        $this->startingDate = $startingDate;

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

        if ($endingDate <= $this->startingDate) {
            throw new InvalidArgumentException("La date de fin doit être postérieure à la date du début.");
        }

        $this->endingDate = $endingDate;

        return $this;
    }

    public function getTotalAmount(): float
    {
        return $this->totalAmountCents / 100;
    }

    public function setTotalAmount(float $amount): static
    {
        $this->totalAmountCents = (int) round($amount * 100);
        return $this;
    }


    public function getStatus(): ?BookingStatus
    {
        return $this->status;
    }

    public function setStatus(BookingStatus $status): static
    {
        $this->status = $status;

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

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): static
    {
        $this->room = $room;

        return $this;
    }

    public function getCreatedBy(): ?Client
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?Client $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getBookingsUpdatedByClient(): ?Client
    {
        return $this->updatedByClient;
    }

    public function setBookingsUpdatedByClient(?Client $updatedByClient): static
    {
        $this->updatedByClient = $updatedByClient;

        return $this;
    }

    public function getBookingsUpdatedByUser(): ?User
    {
        return $this->updatedByUser;
    }

    public function setBookingsUpdatedByUser(?User $updatedByUser): static
    {
        $this->updatedByUser = $updatedByUser;

        return $this;
    }
}
