<?php

namespace App\Entity;

use App\Repository\TechnicienRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TechnicienRepository::class)]
class Technicien
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?User $user = null;

    /**
     * @var Collection<int, Claim>
     */
    #[ORM\OneToMany(targetEntity: Claim::class, mappedBy: 'technicien')]
    private Collection $claims;

    #[ORM\Column(length: 15)]
    private ?string $phoneNumber = null;

    public function __construct()
    {
        $this->claims = new ArrayCollection();
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


    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Claim>
     */
    public function getClaims(): Collection
    {
        return $this->claims;
    }

    public function addClaim(Claim $claim): static
    {
        if (!$this->claims->contains($claim)) {
            $this->claims->add($claim);
            $claim->setTechnicien($this);
        }

        return $this;
    }

    public function removeClaim(Claim $claim): static
    {
        if ($this->claims->removeElement($claim)) {
            // set the owning side to null (unless already changed)
            if ($claim->getTechnicien() === $this) {
                $claim->setTechnicien(null);
            }
        }

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }
}
