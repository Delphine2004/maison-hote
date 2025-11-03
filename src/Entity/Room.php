<?php

namespace App\Entity;

use App\Enum\RoomStatus;
use InvalidArgumentException;

class Room
{

    public function __construct(
        public ?int $roomNumber = null,
        public ?int $maxOccupancy = null,
        public ?float $dailyRate = null,
        public ?RoomStatus $roomStatus = null
    ) {
        $this->setRoomNumber($roomNumber)
            ->setMaxOccupancy($maxOccupancy)
            ->setDailyRate($dailyRate)
            ->setRoomStatus($roomStatus);
    }

    // -------------Getters--------------

    public function getRoomNumber(): ?int
    {
        return $this->roomNumber;
    }

    public function getMaxOccupancy(): ?int
    {
        return $this->maxOccupancy;
    }

    public function getDailyRate(): ?float
    {
        return $this->dailyRate;
    }

    public function getRoomStatus(): ?RoomStatus
    {
        return $this->roomStatus;
    }

    //------------Setter--------------

    public function setRoomNumber(?int $roomNumber): self
    {
        if ($roomNumber < 0 || $roomNumber >= 100) {
            throw new InvalidArgumentException("Le numéro de chambre doit être supérieure à 0 et inférieure à 100.");
        }

        $this->roomNumber = $roomNumber;
        return $this;
    }

    public function setMaxOccupancy(?int $maxOccupancy): self
    {
        if ($maxOccupancy < 0 || $maxOccupancy >= 5) {
            throw new InvalidArgumentException("L'occupation doit être supérieure à 0 et inférieure à 6.");
        }
        $this->maxOccupancy = $maxOccupancy;
        return $this;
    }

    public function setDailyRate(?float $dailyRate): self
    {
        if ($dailyRate < 0 || $dailyRate >= 100) {
            throw new InvalidArgumentException("Le prix doit être supérieure à 0 et inférieure à 100.");
        }
        $this->dailyRate = $dailyRate;
        return $this;
    }

    public function setRoomStatus(?RoomStatus $roomStatus): self
    {
        $this->roomStatus = $roomStatus;
        return $this;
    }
}
