<?php

namespace App\DTO;

use App\Enum\BookingStatus;
use DateTimeImmutable;


class SearchBooking
{

    public ?int $id = null;
    public ?string $lastName = null;
    public ?BookingStatus $status = null;
    public ?DateTimeImmutable $startingDate = null;
    public ?DateTimeImmutable $endingDate = null;
    public ?DateTimeImmutable $createdAt = null;

    public function getBookingId(): ?int
    {
        return $this->id;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function getStatus(): ?BookingStatus
    {
        return $this->status;
    }

    public function getStartingDate(): ?DateTimeImmutable
    {
        return $this->startingDate;
    }

    public function getEndingDate(): ?DateTimeImmutable
    {
        return $this->endingDate;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }
}
