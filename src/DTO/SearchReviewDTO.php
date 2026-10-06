<?php

namespace App\DTO;

use App\Entity\Room;
use App\Enum\ReviewStatus;
use DateTimeImmutable;

class SearchReviewDTO
{

    public ?int $id = null;
    public ?int $rate = null;
    public ?Room $room = null;
    public ?int $userId = null;
    public ?ReviewStatus $status = null;
    public ?DateTimeImmutable $createdAt = null;


    public function getReviewId(): ?int
    {
        return $this->id;
    }

    public function getRate(): ?int
    {
        return $this->rate;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }


    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function getStatus(): ?ReviewStatus
    {
        return $this->status;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }
}
