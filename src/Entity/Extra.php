<?php

namespace App\Entity;

use App\Repository\ExtraRepository;
use App\Utils\RegexPatterns;

use InvalidArgumentException;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExtraRepository::class)]
class Extra
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $rate = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {

        $name = trim($name);

        if (empty($name)) {
            throw new InvalidArgumentException("Le nom est obligatoire.");
        }

        if (!preg_match(RegexPatterns::ONLY_TEXTE_REGEX, $name)) {
            throw new InvalidArgumentException("Le nom doit être compris entre 1 et 60 caractères autorisés.");
        }
        $this->name = ucfirst($name);

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {

        $description = trim($description);

        if (empty($description)) {
            throw new InvalidArgumentException("La description est obligatoire.");
        }

        if (!preg_match(RegexPatterns::FREE_TEXT_REGEX, $description)) {
            throw new InvalidArgumentException("La description doit être compris entre 1 et 255 caractères autorisés.");
        }
        $this->description = ucfirst($description);

        return $this;
    }

    public function getRate(): ?float
    {
        return $this->rate;
    }

    public function setRate(float $rate): static
    {
        if ($rate < 0 || $rate >= 100) {
            throw new InvalidArgumentException("Le prix doit être supérieure à 0 et inférieure à 100.");
        }
        $this->rate = $rate;
        return $this;
    }
}
