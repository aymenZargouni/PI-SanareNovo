<?php

namespace App\Entity;

use App\Repository\SalleRepository;
use Doctrine\ORM\Mapping as ORM;


use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SalleRepository::class)]
class Salle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom de chef service  complet est obligatoire.")]
    #[Assert\Length(
        min: 3,
        max: 20,
        minMessage: "Le nom chef service complet doit contenir au moins 3 caractères.",
        maxMessage: "Le nom chef service complet ne peut pas dépasser 20 caractères."
    )]
    private ?string $type = null;

    #[ORM\Column]
    private ?bool $etat = null;

    #[ORM\ManyToOne(inversedBy: 'salles')]
    private ?Service $service = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function isEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(bool $etat): static
    {
        $this->etat = $etat;

        return $this;
    }
    public function isOccupee(): bool
{
    return $this->etat == 1;
}

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): static
    {
        $this->service = $service;

        return $this;
    }
}
