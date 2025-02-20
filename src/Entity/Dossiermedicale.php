<?php

namespace App\Entity;

use App\Repository\DossiermedicaleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: DossiermedicaleRepository::class)]
#[Assert\Callback('validateDate')]
class Dossiermedicale
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "⚠️ L'IMC est obligatoire.")]
    #[Assert\Type(type: 'numeric', message: "⚠️ L'IMC doit être un nombre valide.")]
    private ?float $imc = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: "⚠️ La date est obligatoire.")]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "⚠️ Les observations sont obligatoires.")]
    private ?string $observations = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "⚠️ L'ordonnance est obligatoire.")]
    private ?string $ordonnance = null;

    /**
     * @var Collection<int, Consultation>
     */
    #[ORM\OneToMany(targetEntity: Consultation::class, mappedBy: 'dossiermedicale')]
    private Collection $consultations;

    public function __construct()
    {
        $this->consultations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImc(): ?float
    {
        return $this->imc;
    }

    public function setImc(float $imc): static
    {
        $this->imc = $imc;

        return $this;
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

    public function getObservations(): ?string
    {
        return $this->observations;
    }

    public function setObservations(string $observations): static
    {
        $this->observations = $observations;

        return $this;
    }

    public function getOrdonnance(): ?string
    {
        return $this->ordonnance;
    }

    public function setOrdonnance(string $ordonnance): static
    {
        $this->ordonnance = $ordonnance;

        return $this;
    }

    /**
     * @return Collection<int, Consultation>
     */
    public function getConsultations(): Collection
    {
        return $this->consultations;
    }

    public function addConsultation(Consultation $consultation): static
    {
        if (!$this->consultations->contains($consultation)) {
            $this->consultations->add($consultation);
            $consultation->setDossiermedicale($this);
        }

        return $this;
    }

    public function removeConsultation(Consultation $consultation): static
    {
        if ($this->consultations->removeElement($consultation)) {
            // set the owning side to null (unless already changed)
            if ($consultation->getDossiermedicale() === $this) {
                $consultation->setDossiermedicale(null);
            }
        }

        return $this;
    }
    public function validateDate(ExecutionContextInterface $context): void
    {
        if (!$this->date instanceof \DateTimeInterface) {
            return; // Évite les erreurs si la date est null ou invalide
        }
    
        $today = new \DateTimeImmutable(); // Utilisation de DateTimeImmutable
        
        $tomorrow = $today->modify('+365 day'); // Définir demain
    
        if ($this->date < $today || $this->date > $tomorrow) {
            $context->buildViolation("⚠️ Vous ne pouvez sélectionner que la date d'aujourd'hui ou de demain.")
                ->atPath('date')
                ->addViolation();
        }
    }
}
