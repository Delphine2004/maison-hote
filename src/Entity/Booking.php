<?php

namespace App\Entity;

use App\Entity\Client;
use App\Entity\Room;
use App\Enum\BookingStatus;

use DateTimeImmutable;
use InvalidArgumentException;

class Booking
{

    public function __construct(
        public ?int $bookingId = null,

        public ?int $userId = null,
        public ?Client $client = null,

        public ?int $roomId = null,
        public ?Room $room = null,

        public ?DateTimeImmutable $startingDate = null,
        public ?DateTimeImmutable $endingDate = null,
        public ?float $totalAmount = null,
        public ?int $paxNumber = null,

        public ?BookingStatus $bookingStatus = null,

        public ?DateTimeImmutable $createdAt = null,
        public ?DateTimeImmutable $updatedAt = null

    ) {


        $this->setUserId($userId)
            ->setRoomId($roomId)
            ->setStartingDate($startingDate)
            ->setEndingDate($endingDate)
            ->setTotalAmount($totalAmount)
            ->setPaxNumber($paxNumber)
            ->setBookingStatus($bookingStatus);

        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new DateTimeImmutable();
    }

    // -------------Getters--------------
    public function getBookingId(): ?int
    {
        return $this->bookingId;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function getRoomId(): ?int
    {
        return $this->roomId;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function getStartingDate(): ?DateTimeImmutable
    {
        return $this->startingDate;
    }

    public function getEndingingDate(): ?DateTimeImmutable
    {
        return $this->endingDate;
    }

    public function getTotalAmount(): ?float
    {
        return $this->totalAmount;
    }

    public function getPaxNumber(): ?int
    {
        return $this->paxNumber;
    }

    public function getBookingStatus(): ?BookingStatus
    {
        return $this->bookingStatus;
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

    public function setBookingId(?int $bookingId): self
    {
        $this->bookingId = $bookingId;
        $this->updateTimestamp();
        return $this;
    }

    public function setUserId(?int $userId): self
    {
        $this->userId = $userId;
        $this->updateTimestamp();
        return $this;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;
        $this->updateTimestamp();
        return $this;
    }

    public function setRoomId(?int $roomId): self
    {
        $this->roomId = $roomId;
        $this->updateTimestamp();
        return $this;
    }

    public function setRoom(?Room $room): self
    {
        $this->room = $room;
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
            throw new InvalidArgumentException("La date de départ doit être dans le futur.");
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
            throw new InvalidArgumentException("La date de fin de réservation doit être supérieure à la date du début.");
        }

        $this->endingDate = $endingDate;
        $this->updateTimestamp();
        return $this;
    }

    public function setTotalAmount(?float $totalAmount): self
    {
        if ($totalAmount < 0 || $totalAmount >= 1000) {
            throw new InvalidArgumentException("Le prix doit être supérieure à 0 et inférieure à 1000.");
        }
        $this->totalAmount = $totalAmount;
        $this->updateTimestamp();
        return $this;
    }

    public function setPaxNumber(?int $paxNumber): self
    {
        if ($paxNumber < 0 || $paxNumber >= 100) {
            throw new InvalidArgumentException("Le nombre de personne doit être entre 1 et 2.");
        }
        $this->paxNumber = $paxNumber;
        $this->updateTimestamp();
        return $this;
    }

    public function setBookingStatus(?BookingStatus $bookingStatus): self
    {
        $this->bookingStatus = $bookingStatus;
        $this->updateTimestamp();
        return $this;
    }

    // ---- Mise à jour de la date de modification
    private function updateTimestamp(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }
}
