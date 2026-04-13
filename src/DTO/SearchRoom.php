<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

use DateTimeImmutable;

class SearchRoom
{
    public ?DateTimeImmutable $startingDate = null;
    public ?DateTimeImmutable $endingDate = null;

    #[Assert\Callback]
    public function validate($context): void
    {
        if ($this->startingDate && $this->endingDate) {
            if ($this->startingDate >= $this->endingDate) {
                $context->buildViolation('La date de départ doit être après la date d\'arrivée')
                    ->atPath('endingDate')
                    ->addViolation();
            }
        }

        if ($this->startingDate < new DateTimeImmutable("today")) {
            $context->buildViolation('La date d\'arrivée ne peut pas être dans le passé')
                ->atPath('startingDate')
                ->addViolation();
        }
    }
}
