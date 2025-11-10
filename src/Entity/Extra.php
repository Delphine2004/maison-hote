<?php

namespace App\Entity;

use App\Repository\ExtraRepository;

use App\Enum\ExtraCategory;
use App\Utils\RegexPatterns;

use InvalidArgumentException;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: ExtraRepository::class)]
class Extra
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 50, enumType: ExtraCategory::class)]
    private ?ExtraCategory $category = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $rate = null;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\ManyToMany(targetEntity: Booking::class, mappedBy: 'extras')]
    private Collection $bookings;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getCategory(): ?ExtraCategory
    {
        return $this->category;
    }

    public function setCategory(ExtraCategory $category): static
    {
        $this->category = $category;

        return $this;
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

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): static
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->addExtra($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            $booking->removeExtra($this);
        }

        return $this;
    }
}
