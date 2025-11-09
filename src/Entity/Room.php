<?php

namespace App\Entity;

use App\Enum\RoomStatus;
use App\Repository\RoomRepository;
use App\Utils\RegexPatterns;

use InvalidArgumentException;
use DateTimeImmutable;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(unique: true)]
    private ?int $number = null;

    #[ORM\Column]
    private ?int $maxOccupancy = null;

    #[ORM\Column]
    private ?int $area = null;

    #[ORM\Column]
    private ?float $dailyRate = null;

    #[ORM\Column(type: Types::STRING, length: 50, enumType: RoomStatus::class)]
    private ?RoomStatus $roomStatus = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): static
    {
        if ($number < 0 || $number >= 100) {
            throw new InvalidArgumentException("Le numéro de chambre doit être supérieure à 0 et inférieure à 100.");
        }

        $this->number = $number;
        return $this;
    }

    public function getMaxOccupancy(): ?int
    {
        return $this->maxOccupancy;
    }

    public function setMaxOccupancy(int $maxOccupancy): static
    {
        if ($maxOccupancy < 0 || $maxOccupancy >= 5) {
            throw new InvalidArgumentException("L'occupation doit être supérieure à 0 et inférieure à 6.");
        }
        $this->maxOccupancy = $maxOccupancy;
        return $this;
    }

    public function getArea(): ?int
    {
        return $this->area;
    }

    public function setArea(int $area): static
    {
        if ($area < 0 || $area >= 50) {
            throw new InvalidArgumentException("La superficie de la chambre doit être supérieure à 0 et inférieure à 50.");
        }

        $this->area = $area;
        return $this;
    }

    public function getDailyRate(): ?float
    {
        return $this->dailyRate;
    }

    public function setDailyRate(float $dailyRate): static
    {
        if ($dailyRate < 0 || $dailyRate >= 100) {
            throw new InvalidArgumentException("Le prix doit être supérieure à 0 et inférieure à 100.");
        }
        $this->dailyRate = $dailyRate;
        return $this;
    }

    public function getRoomStatus(): ?RoomStatus
    {
        return $this->roomStatus;
    }

    public function setRoomStatus(RoomStatus $roomStatus): static
    {
        $this->roomStatus = $roomStatus;

        return $this;
    }
}
