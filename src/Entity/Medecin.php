<?php

namespace App\Entity;

use App\Repository\MedecinRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MedecinRepository::class)]
class Medecin
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom complet est obligatoire.")]
    #[Assert\Length(
        min: 3,
        max: 20,
        minMessage: "Le nom complet doit contenir au moins 3 caractères.",
        maxMessage: "Le nom complet ne peut pas dépasser 20 caractères."
    )]
    private ?string $fullname = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\GreaterThanOrEqual(
        value: "today",
        message: "La date d'embauche doit être aujourd'hui ou dans le futur."
    )]
    private ?\DateTimeInterface $dateEmbauche = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "La spécialité est obligatoire.")]
    private ?string $specilite = null;

    #[ORM\OneToOne(inversedBy: 'medecin', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): static
    {
        $this->fullname = $fullname;

        return $this;
    }

    public function getDateEmbauche(): ?\DateTimeInterface
    {
        return $this->dateEmbauche;
    }

    public function setDateEmbauche(\DateTimeInterface $dateEmbauche): static
    {
        $this->dateEmbauche = $dateEmbauche;

        return $this;
    }

    public function getSpecilite(): ?string
    {
        return $this->specilite;
    }

    public function setSpecilite(string $specilite): static
    {
        $this->specilite = $specilite;

        return $this;
    }

    public function getUser(): ?user
    {
        return $this->user;
    }

    public function setUser(?user $user): static
    {
        $this->user = $user;

        return $this;
    }
}
