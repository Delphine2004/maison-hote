<?php

namespace App\Entity;

use App\Repository\UserRepository;

use App\Utils\RegexPatterns;

use DateTimeImmutable;

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

    #[Assert\NotBlank(message: "Le login est obligatoire.")]
    #[Assert\Regex(RegexPatterns::LOGIN)]
    #[Assert\Length(min: 8, maxMessage: "Le login doit contenir au minimum 8 lettres et/ou chiffres.")]
    #[Assert\Length(max: 25, maxMessage: "Le login ne doit pas dépasser 25 lettres et/ou chiffres.")]
    #[ORM\Column(length: 25, unique: true)]
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
