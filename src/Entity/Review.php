<?php

namespace App\Entity;

use App\Enum\ReviewStatus;
use App\Utils\RegexPatterns;
use InvalidArgumentException;
use DateTimeImmutable;

class Review
{

    function __construct(
        private int|string|null $id = null, // n'a pas de valeur au moment de l'instanciation
        private ?int $clientId = null,
        private ?int $bookingId = null,
        private ?int $rating = null,
        private ?string $comment = null,
        private ?ReviewStatus $status = null,
        private ?DateTimeImmutable $createdAt = null,
        private ?DateTimeImmutable $validatedAt = null
    ) {}

    // ---------Les Getters ---------
    public function getId(): int|string|null
    {
        return $this->id;
    }

    public function getClientId(): ?int
    {
        return $this->clientId;
    }

    public function getBookingId(): ?int
    {
        return $this->bookingId;
    }


    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function getStatus(): ?ReviewStatus
    {
        return $this->status;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getValidatedAt(): DateTimeImmutable
    {
        return $this->validatedAt;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setBookingId(?int $bookingId): self
    {
        $this->bookingId = $bookingId;
        return $this;
    }

    public function setClientId(?int $clientId): self
    {
        $this->clientId = $clientId;
        return $this;
    }


    public function setRating(?int $rating): self
    {
        if ($rating < 0 || $rating > 5) {
            throw new InvalidArgumentException("La note doit être comprise entre 0 et 5.");
        }
        $this->rating = $rating;
        return $this;
    }

    public function setComment(?string $comment): self
    {

        if ($comment !== null) {
            $comment = trim($comment);


            if (!preg_match(RegexPatterns::FREE_TEXT_REGEX, $comment)) {
                throw new InvalidArgumentException("Le commentaire peut contenir entre 2 et 255 caractères autorisés.");
            }
        }

        $this->comment = ucfirst($comment);
        return $this;
    }

    public function setStatus(?ReviewStatus $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function setValidatedAt(DateTimeImmutable $validatedAt): self
    {
        $this->validatedAt = $validatedAt;
        return $this;
    }
}
