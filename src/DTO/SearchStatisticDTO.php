<?php

namespace App\DTO;

use DateTimeImmutable;


class SearchStatisticDTO
{
    public function __construct(
        public ?DateTimeImmutable $startingDate = null,
        public ?DateTimeImmutable $endingDate = null,
    ) {}

    public function getStartingDate(): ?DateTimeImmutable
    {
        return $this->startingDate;
    }

    public function getEndingDate(): ?DateTimeImmutable
    {
        return $this->endingDate;
    }
}
