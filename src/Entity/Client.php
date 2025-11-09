<?php

namespace App\Entity;

use App\Enum\UserRole;
use App\Utils\RegexPatterns;
use App\Repository\ClientRepository;

use DateTimeImmutable;
use InvalidArgumentException;

use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Client implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $firstName = null;

    #[ORM\Column(length: 100)]
    private ?string $lastName = null;

    #[ORM\Column(length: 100, unique: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 20)]
    private ?string $phone = null;

    #[ORM\Column(enumType: UserRole::class)]
    private ?UserRole $role = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 10)]
    private ?string $zipCode = null;

    #[ORM\Column(length: 100)]
    private ?string $city = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
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

    // ----- Méthodes de vérification -----

    private function validatePassword(string $password): void
    {
        $password = trim($password);

        if (!preg_match(RegexPatterns::PASSWORD, $password)) {

            throw new InvalidArgumentException('Le mot de passe doit contenir au minimun une minuscule, une majuscule, un chiffre, un caractère spécial et contenir 12 caractéres au total.');
        };
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

        $firstName = trim($firstName);

        if (empty($firstName)) {
            throw new InvalidArgumentException("Le prénom est obligatoire.");
        }

        if (!preg_match(RegexPatterns::ONLY_TEXTE_REGEX, $firstName)) {
            throw new InvalidArgumentException("Le prénom doit être compris entre 1 et 100 caractères autorisés.");
        }
        $this->firstName = ucfirst($firstName);

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {

        $lastName = trim($lastName);

        if (empty($lastName)) {
            throw new InvalidArgumentException("Le nom est obligatoire.");
        }

        if (!preg_match(RegexPatterns::ONLY_TEXTE_REGEX, $lastName)) {
            throw new InvalidArgumentException("Le nom doit être compris entre 1 et 100 caractères autorisés.");
        }
        $this->lastName = strtoupper($lastName);


        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $email = trim($email);

        if (empty($email)) {
            throw new InvalidArgumentException("L'email est obligatoire. ");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("L'email est invalide.");
        }
        $this->email = strtolower($email);

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password, bool $isHashed = false): static
    {

        $passwordToStore = trim($password);

        if (!$isHashed) {
            $this->validatePassword($passwordToStore);
            $passwordToStore = password_hash($passwordToStore, PASSWORD_DEFAULT);
            //var_dump('INSCRIPTION - HASH stocké:', $passwordToStore);
        }

        $this->password = $passwordToStore;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $phone = trim($phone);

        if (!preg_match(RegexPatterns::FRENCH_MOBILE_PHONE, $phone)) {
            throw new InvalidArgumentException("Le numéro de téléphone doit avoir le format téléphone portable français en 06, 07 , +33.");
        }
        $this->phone = $phone;

        return $this;
    }

    public function getRole(): ?UserRole
    {
        return $this->role;
    }

    public function getRoleValue(): ?string
    {
        return $this->role?->value;
    }

    public function getRoles(): array
    {
        $roles = ($this->role) ? [$this->role->value] : [];

        // Assurez-vous qu'au moins 'ROLE_USER' est toujours présent
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRole(UserRole $role): static
    {
        $this->role = $role;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): static
    {

        $address = trim($address);

        if (!preg_match(RegexPatterns::FREE_TEXT_REGEX, $address)) {
            throw new InvalidArgumentException("L'adresse peut contenir entre 2 et 255 caractères autorisés.");
        }
        $this->address = $address;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): static
    {

        $zipCode = trim($zipCode);

        if (!preg_match(RegexPatterns::ZIP_CODE, $zipCode)) {
            throw new InvalidArgumentException("Le code postal doit contenir exactement 5 chiffres.");
        }
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): static
    {
        $city = trim($city);

        if (!preg_match(RegexPatterns::ONLY_TEXTE_REGEX, $city)) {
            throw new InvalidArgumentException("Le ville doit contenir entre 1 et 100 caractères autorisés.");
        }
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
