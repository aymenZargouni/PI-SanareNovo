<?php

namespace App\Entity;

use App\Repository\ConsultationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: ConsultationRepository::class)]
#[Assert\Callback('validateDate')]

class Consultation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "⚠️ La date de consultation est obligatoire.")]
    #[Assert\Type(type: \DateTimeInterface::class, message: "Veuillez entrer une date valide.")]
    
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "⚠️ Le champ motif est obligatoire.")]
    private ?string $motif = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "⚠️ Le champ TypeConsultation est obligatoire.")]
    private ?string $typeconsultation = null;

    #[ORM\Column(length: 255,)]
    
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'consultations')]
    #[Assert\NotBlank(message: "⚠️ Le champ DossierMedicale est obligatoire.")]
    private ?Dossiermedicale $dossiermedicale = null;

    #[ORM\ManyToOne(inversedBy: 'consultations')]
    #[Assert\NotBlank(message: "⚠️ Le champ Service est obligatoire.",)]
    private ?Service $nom_service = null;

    #[ORM\ManyToOne(inversedBy: 'consultation')]
    #[Assert\NotBlank(message: "⚠️ Le champ Fullname Patient est obligatoire.")]
    private ?Patient $patient = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): static
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

    public function validateDate(ExecutionContextInterface $context): void
{
    if (!$this->date instanceof \DateTimeInterface) {
        return; 
    }

    $today = new \DateTimeImmutable(); 
    
    $tomorrow = $today->modify('365 day'); 

    if ($this->date < $today || $this->date > $tomorrow) {
        $context->buildViolation("⚠️ Vous ne pouvez sélectionner que la date d'aujourd'hui ou de demain.")
            ->atPath('date')
            ->addViolation();
    }
}

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(?Patient $patient): static
    {
        $this->patient = $patient;

        return $this;
    }

}
