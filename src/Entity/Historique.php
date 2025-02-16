<?php

namespace App\Entity;

use App\Repository\HistoriqueRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoriqueRepository::class)]
class Historique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Equipment::class, inversedBy: 'historiques')]
    #[ORM\JoinColumn(nullable: false)]
    private $equipment;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateReparation = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $rapportDetaille = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getEquipment(): ?Equipment
    {
        return $this->equipment;
    }

    public function setEquipment(?Equipment $equipment): self
    {
        $this->equipment = $equipment;

        return $this;
    }


    public function getDateReparation(): ?\DateTimeInterface
    {
        return $this->dateReparation;
    }

    public function setDateReparation(\DateTimeInterface $dateReparation): static
    {
        $this->dateReparation = $dateReparation;

        return $this;
    }

    public function getRapportDetaille(): ?string
    {
        return $this->rapportDetaille;
    }

    public function setRapportDetaille(string $rapportDetaille): static
    {
        $this->rapportDetaille = $rapportDetaille;

        return $this;
    }
}