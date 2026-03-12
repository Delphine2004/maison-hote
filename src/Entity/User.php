<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Enum\UserRole;

use App\Utils\RegexPatterns;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé.')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\Regex(RegexPatterns::ONLY_TEXTE_REGEX)]
    #[Assert\Length(min: 2, maxMessage: "Le prénom doit contenir au minimum 2 lettres.")]
    #[Assert\Length(max: 100, maxMessage: "Le prénom ne doit pas dépasser 100 lettres.")]
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $firstName = null;

    #[Assert\Regex(RegexPatterns::ONLY_TEXTE_REGEX)]
    #[Assert\Length(min: 2, maxMessage: "Le nom doit contenir au minimum 2 lettres.")]
    #[Assert\Length(max: 100, maxMessage: "Le nom ne doit pas dépasser 100 lettres.")]
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $lastName = null;

    #[Assert\Regex(RegexPatterns::FREE_TEXT_REGEX)]
    #[Assert\Length(min: 8, maxMessage: "Le login doit contenir au minimum 8 lettres et/ou chiffres.")]
    #[Assert\Length(max: 25, maxMessage: "Le login ne doit pas dépasser 25 lettres et/ou chiffres.")]
    #[ORM\Column(length: 25, nullable: true, unique: true)]
    private ?string $login = null;

    #[Assert\NotBlank(message: "L'email est obligatoire.")]
    #[Assert\Email(message: "Email invalide")]
    #[Assert\Length(max: 255, maxMessage: "L'email ne doit pas dépasser 255 caractères.")]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    // Validation faite dans le type
    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[Assert\NotNull(message: "Veuillez sélectionner un rôle.")]
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[Assert\Regex(RegexPatterns::FRENCH_MOBILE_PHONE)]
    #[ORM\Column(length: 20, nullable: true)]
    private ?string $phone = null;

    #[Assert\Regex(RegexPatterns::ONLY_TEXTE_REGEX)]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $address = null;

    #[Assert\Regex(RegexPatterns::ZIP_CODE)]
    #[ORM\Column(length: 10, nullable: true)]
    private ?string $zipCode = null;

    #[Assert\Regex(RegexPatterns::ONLY_TEXTE_REGEX)]
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Room>
     */
    #[ORM\OneToMany(targetEntity: Room::class, mappedBy: 'createdBy')]
    private Collection $roomsCreated;

    /**
     * @var Collection<int, Room>
     */
    #[ORM\OneToMany(targetEntity: Room::class, mappedBy: 'updatedBy')]
    private Collection $roomsUpdated;

    /**
     * @var Collection<int, Rate>
     */
    #[ORM\OneToMany(targetEntity: Rate::class, mappedBy: 'createdBy')]
    private Collection $ratesCreated;

    /**
     * @var Collection<int, Rate>
     */
    #[ORM\OneToMany(targetEntity: Rate::class, mappedBy: 'updatedBy')]
    private Collection $ratesUpdated;

    /**
     * @var Collection<int, Period>
     */
    #[ORM\OneToMany(targetEntity: Period::class, mappedBy: 'createdBy')]
    private Collection $periodsCreated;

    /**
     * @var Collection<int, Period>
     */
    #[ORM\OneToMany(targetEntity: Period::class, mappedBy: 'updatedBy')]
    private Collection $periodsUpdated;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'user')]
    private Collection $bookings;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'createdBy')]
    private Collection $bookingsCreatedBy;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'updatedBy')]
    private Collection $bookingsUpdatedBy;



    public function __construct()
    {
        $this->roomsCreated = new ArrayCollection();
        $this->roomsUpdated = new ArrayCollection();
        $this->ratesCreated = new ArrayCollection();
        $this->ratesUpdated = new ArrayCollection();
        $this->periodsCreated = new ArrayCollection();
        $this->periodsUpdated = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->bookingsCreatedBy = new ArrayCollection();
        $this->bookingsUpdatedBy = new ArrayCollection();
    }



    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->updatedAt = new DateTimeImmutable();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {

        $this->firstName = ucfirst($firstName);

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {

        $this->lastName = strtoupper($lastName);

        return $this;
    }
    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): static
    {

        $this->login = $login;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = strtolower($email);

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
    {

        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles ?? [];

        $roles[] = 'ROLE_USER'; // rôle requis par symfony

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    public function addRole(string $role): self
    {
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {
        $this->address = $address;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): static
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $this->city = strtoupper($city);

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function eraseCredentials(): void {}

    /**
     * @return Collection<int, Room>
     */
    public function getRoomsCreated(): Collection
    {
        return $this->roomsCreated;
    }

    public function addRoomsCreated(Room $roomsCreated): static
    {
        if (!$this->roomsCreated->contains($roomsCreated)) {
            $this->roomsCreated->add($roomsCreated);
            $roomsCreated->setCreatedBy($this);
        }

        return $this;
    }

    public function removeRoomsCreated(Room $roomsCreated): static
    {
        if ($this->roomsCreated->removeElement($roomsCreated)) {
            // set the owning side to null (unless already changed)
            if ($roomsCreated->getCreatedBy() === $this) {
                $roomsCreated->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Room>
     */
    public function getRoomsUpdated(): Collection
    {
        return $this->roomsUpdated;
    }

    public function addRoomsUpdated(Room $roomsUpdated): static
    {
        if (!$this->roomsUpdated->contains($roomsUpdated)) {
            $this->roomsUpdated->add($roomsUpdated);
            $roomsUpdated->setUpdatedBy($this);
        }

        return $this;
    }

    public function removeRoomsUpdated(Room $roomsUpdated): static
    {
        if ($this->roomsUpdated->removeElement($roomsUpdated)) {
            // set the owning side to null (unless already changed)
            if ($roomsUpdated->getUpdatedBy() === $this) {
                $roomsUpdated->setUpdatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Rate>
     */
    public function getRatesCreated(): Collection
    {
        return $this->ratesCreated;
    }

    public function addRatesCreated(Rate $ratesCreated): static
    {
        if (!$this->ratesCreated->contains($ratesCreated)) {
            $this->ratesCreated->add($ratesCreated);
            $ratesCreated->setCreatedBy($this);
        }

        return $this;
    }

    public function removeRatesCreated(Rate $ratesCreated): static
    {
        if ($this->ratesCreated->removeElement($ratesCreated)) {
            // set the owning side to null (unless already changed)
            if ($ratesCreated->getCreatedBy() === $this) {
                $ratesCreated->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Rate>
     */
    public function getRatesUpdated(): Collection
    {
        return $this->ratesUpdated;
    }

    public function addRatesUpdated(Rate $ratesUpdated): static
    {
        if (!$this->ratesUpdated->contains($ratesUpdated)) {
            $this->ratesUpdated->add($ratesUpdated);
            $ratesUpdated->setUpdatedBy($this);
        }

        return $this;
    }

    public function removeRatesUpdated(Rate $ratesUpdated): static
    {
        if ($this->ratesUpdated->removeElement($ratesUpdated)) {
            // set the owning side to null (unless already changed)
            if ($ratesUpdated->getUpdatedBy() === $this) {
                $ratesUpdated->setUpdatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Period>
     */
    public function getPeriodsCreated(): Collection
    {
        return $this->periodsCreated;
    }

    public function addPeriodsCreated(Period $periodsCreated): static
    {
        if (!$this->periodsCreated->contains($periodsCreated)) {
            $this->periodsCreated->add($periodsCreated);
            $periodsCreated->setCreatedBy($this);
        }

        return $this;
    }

    public function removePeriodsCreated(Period $periodsCreated): static
    {
        if ($this->periodsCreated->removeElement($periodsCreated)) {
            // set the owning side to null (unless already changed)
            if ($periodsCreated->getCreatedBy() === $this) {
                $periodsCreated->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Period>
     */
    public function getPeriodsUpdated(): Collection
    {
        return $this->periodsUpdated;
    }

    public function addPeriodsUpdated(Period $periodsUpdated): static
    {
        if (!$this->periodsUpdated->contains($periodsUpdated)) {
            $this->periodsUpdated->add($periodsUpdated);
            $periodsUpdated->setUpdatedBy($this);
        }

        return $this;
    }

    public function removePeriodsUpdated(Period $periodsUpdated): static
    {
        if ($this->periodsUpdated->removeElement($periodsUpdated)) {
            // set the owning side to null (unless already changed)
            if ($periodsUpdated->getUpdatedBy() === $this) {
                $periodsUpdated->setUpdatedBy(null);
            }
        }

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
            $booking->setUser($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getUser() === $this) {
                $booking->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookingsCreatedBy(): Collection
    {
        return $this->bookingsCreatedBy;
    }

    public function addBookingsCreatedBy(Booking $bookingsCreatedBy): static
    {
        if (!$this->bookingsCreatedBy->contains($bookingsCreatedBy)) {
            $this->bookingsCreatedBy->add($bookingsCreatedBy);
            $bookingsCreatedBy->setCreatedBy($this);
        }

        return $this;
    }

    public function removeBookingsCreatedBy(Booking $bookingsCreatedBy): static
    {
        if ($this->bookingsCreatedBy->removeElement($bookingsCreatedBy)) {
            // set the owning side to null (unless already changed)
            if ($bookingsCreatedBy->getCreatedBy() === $this) {
                $bookingsCreatedBy->setCreatedBy(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookingsUpdatedBy(): Collection
    {
        return $this->bookingsUpdatedBy;
    }

    public function addBookingsUpdatedBy(Booking $bookingsUpdatedBy): static
    {
        if (!$this->bookingsUpdatedBy->contains($bookingsUpdatedBy)) {
            $this->bookingsUpdatedBy->add($bookingsUpdatedBy);
            $bookingsUpdatedBy->setUpdatedBy($this);
        }

        return $this;
    }

    public function removeBookingsUpdatedBy(Booking $bookingsUpdatedBy): static
    {
        if ($this->bookingsUpdatedBy->removeElement($bookingsUpdatedBy)) {
            // set the owning side to null (unless already changed)
            if ($bookingsUpdatedBy->getUpdatedBy() === $this) {
                $bookingsUpdatedBy->setUpdatedBy(null);
            }
        }

        return $this;
    }
}
