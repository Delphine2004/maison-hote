<?php

namespace App\Entity;

use App\Enum\BookingStatus;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use InvalidArgumentException;
use DateTimeImmutable;

use App\Repository\BookingRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?DateTimeImmutable $startingDate = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?DateTimeImmutable $endingDate = null;

    #[ORM\Column]
    private ?float $totalAmount = null;

    #[ORM\Column]
    private ?int $paxNumber = null;

    #[ORM\Column(type: Types::STRING, length: 50, enumType: BookingStatus::class)]
    private ?BookingStatus $status = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Room $room = null;

    /**
     * @var Collection<int, Extra>
     */
    #[ORM\ManyToMany(targetEntity: Extra::class, inversedBy: 'bookings')]
    private Collection $extras;

    public function __construct()
    {
        $this->extras = new ArrayCollection();
    }


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

    public function getTotalAmount(): ?float
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(float $totalAmount): static
    {
        if ($totalAmount < 0 || $totalAmount >= 5000) {
            throw new InvalidArgumentException("Le prix doit être supérieure à 0 et inférieure à 5000.");
        }
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function getPaxNumber(): ?int
    {
        return $this->paxNumber;
    }

    public function setPaxNumber(int $paxNumber): static
    {
        if ($paxNumber < 1 || $paxNumber >= 2) {
            throw new InvalidArgumentException("Le nombre de personne doit être entre 1 et 2.");
        }
        $this->paxNumber = $paxNumber;

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

    /**
     * @return Collection<int, Extra>
     */
    public function getExtras(): Collection
    {
        return $this->extras;
    }

    public function addExtra(Extra $extra): static
    {
        if (!$this->extras->contains($extra)) {
            $this->extras->add($extra);
        }

        return $this;
    }

    public function removeExtra(Extra $extra): static
    {
        $this->extras->removeElement($extra);

        return $this;
    }
}
