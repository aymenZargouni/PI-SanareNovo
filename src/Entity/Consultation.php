<?php

namespace App\Entity;

use App\Repository\ConsultationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ConsultationRepository::class)]
class Consultation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "La date de consultation est obligatoire.")]
    #[Assert\Type(type: \DateTime::class, message: "Veuillez entrer une date valide.")]
    private ?\DateTime $date = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le champ motif est obligatoire.")]
    private ?string $motif = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le champ TypeConsultation est obligatoire.")]
    private ?string $typeconsultation = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le champ Status est obligatoire.")]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'consultations')]
    #[Assert\NotBlank(message: "Le champ DossierMedicale est obligatoire.")]
    private ?Dossiermedicale $dossiermedicale = null;

    #[ORM\ManyToOne(inversedBy: 'consultations')]
    private ?Service $nom_service = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(string $motif): static
    {
        $this->motif = $motif;

        return $this;
    }

    public function getTypeconsultation(): ?string
    {
        return $this->typeconsultation;
    }

    public function setTypeconsultation(string $typeconsultation): static
    {
        $this->typeconsultation = $typeconsultation;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getDossiermedicale(): ?Dossiermedicale
    {
        return $this->dossiermedicale;
    }

    public function setDossiermedicale(?Dossiermedicale $dossiermedicale): static
    {
        $this->dossiermedicale = $dossiermedicale;

        return $this;
    }

    public function getNomService(): ?Service
    {
        return $this->nom_service;
    }

    public function setNomService(?Service $nom_service): static
    {
        $this->nom_service = $nom_service;

        return $this;
    }
    
    
}
