<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
class Service
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $chef_service = null;

    #[ORM\Column]
    private ?int $nbr_salle = null;

    #[ORM\Column]
    private ?int $capacite = null;

    #[ORM\Column]
    private ?bool $etat = null;

    /**
     * @var Collection<int, Consultation>
     */
    #[ORM\OneToMany(targetEntity: Consultation::class, mappedBy: 'nom_service')]
    private Collection $consultations;

    /**
     * @var Collection<int, Salle>
     */
    #[ORM\OneToMany(targetEntity: Salle::class, mappedBy: 'service')]
    private Collection $salles;

    public function __construct()
    {
        $this->consultations = new ArrayCollection();
        $this->salles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getChefService(): ?string
    {
        return $this->chef_service;
    }

    public function setChefService(string $chef_service): static
    {
        $this->chef_service = $chef_service;

        return $this;
    }

    public function getNbrSalle(): ?int
    {
        return $this->nbr_salle;
    }

    public function setNbrSalle(int $nbr_salle): static
    {
        $this->nbr_salle = $nbr_salle;

        return $this;
    }

    public function getCapacite(): ?int
    {
        return $this->capacite;
    }

    public function setCapacite(int $capacite): static
    {
        $this->capacite = $capacite;

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
    public function __toString(): string
    {
        return $this->nom;
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
            $consultation->setNomService($this);
        }

        return $this;
    }

    public function removeConsultation(Consultation $consultation): static
    {
        if ($this->consultations->removeElement($consultation)) {
            // set the owning side to null (unless already changed)
            if ($consultation->getNomService() === $this) {
                $consultation->setNomService(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Salle>
     */
    public function getSalles(): Collection
    {
        return $this->salles;
    }

    public function addSalle(Salle $salle): static
    {
        if (!$this->salles->contains($salle)) {
            $this->salles->add($salle);
            $salle->setService($this);
        }

        return $this;
    }

    public function removeSalle(Salle $salle): static
    {
        if ($this->salles->removeElement($salle)) {
            // set the owning side to null (unless already changed)
            if ($salle->getService() === $this) {
                $salle->setService(null);
            }
        }

        return $this;
    }
}
