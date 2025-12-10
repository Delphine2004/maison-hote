<?php

namespace App\Entity;

use App\Repository\ClientRepository;

use App\Utils\RegexPatterns;

use DateTimeImmutable;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Cet email est déjà utilisé.')]
#[ORM\HasLifecycleCallbacks]
class Client implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: "Le prénom est obligatoire.")]
    #[Assert\Regex(RegexPatterns::ONLY_TEXTE_REGEX)]
    #[Assert\Length(min: 2, maxMessage: "Le prénom doit contenir au minimum 2 lettres.")]
    #[Assert\Length(max: 100, maxMessage: "Le prénom ne doit pas dépasser 100 lettres.")]
    #[ORM\Column(length: 100)]
    private ?string $firstName = null;

    #[Assert\NotBlank(message: "Le nom est obligatoire.")]
    #[Assert\Regex(RegexPatterns::ONLY_TEXTE_REGEX)]
    #[Assert\Length(min: 2, maxMessage: "Le nom doit contenir au minimum 2 lettres.")]
    #[Assert\Length(max: 100, maxMessage: "Le nom ne doit pas dépasser 100 lettres.")]
    #[ORM\Column(length: 100)]
    private ?string $lastName = null;

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
    #[ORM\Column(length: 20)]
    private ?string $phone = null;

    #[Assert\Regex(RegexPatterns::ONLY_TEXTE_REGEX)]
    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[Assert\Regex(RegexPatterns::ZIP_CODE)]
    #[ORM\Column(length: 10)]
    private ?string $zipCode = null;

    #[Assert\Regex(RegexPatterns::ONLY_TEXTE_REGEX)]
    #[ORM\Column(length: 100)]
    private ?string $city = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $updatedAt = null;


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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {

        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {

        $this->password = $password;

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

    public function getRoles(): array
    {
        $roles = ($this->roles);

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
}
