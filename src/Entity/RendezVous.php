<?php

namespace App\Entity;

use App\Repository\RendezVousRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: RendezVousRepository::class)]
#[Assert\Callback('validateDate')]
class RendezVous
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'rendezVouses')]
    #[Assert\NotBlank(message: "⚠️ Le champ Patient est obligatoire.")]
    private ?Patient $patient = null;

    #[ORM\ManyToOne(inversedBy: 'rendezVouses')]
    #[Assert\NotBlank(message: "⚠️ Le champ Médecin est obligatoire.")]
    private ?Medecin $medecin = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "⚠️ La date du rendez-vous est obligatoire.")]
    #[Assert\Type(type: \DateTimeInterface::class, message: "Veuillez entrer une date valide.")]
    private ?\DateTimeInterface $dateR = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "⚠️ Le champ Motif est obligatoire.")]
    private ?string $motif = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "⚠️ Le champ Statut est obligatoire.")]
    private ?string $statut = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMedecin(): ?Medecin
    {
        return $this->medecin;
    }

    public function setMedecin(?Medecin $medecin): static
    {
        $this->medecin = $medecin;
        return $this;
    }

    public function getDateR(): ?\DateTimeInterface
    {
        return $this->dateR;
    }

    public function setDateR(\DateTimeInterface $dateR): static
    {
        $this->dateR = $dateR;
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

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function validateDate(ExecutionContextInterface $context): void
    {
        if (!$this->dateR instanceof \DateTimeInterface) {
            return;
        }

        $today = new \DateTimeImmutable();
        $tomorrow = $today->modify('365 day');

        if ($this->dateR < $today || $this->dateR > $tomorrow) {
            $context->buildViolation("⚠️ Vous ne pouvez sélectionner qu'une date entre aujourd'hui et dans un an.")
                ->atPath('dateR')
                ->addViolation();
        }
    }
}
