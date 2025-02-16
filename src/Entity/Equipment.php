<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Equipment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    #[Assert\NotBlank (message:"name is required")]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank (message:"model is required")]
    private ?string $model = null;

    #[ORM\Column(length: 50)]
    private ?string $status = 'reparé'; // Valeur par défaut

    #[ORM\Column(type: "date", nullable: true)]
    #[Assert\NotBlank(message: "La dateis required")]
    private ?\DateTimeInterface $dateAchat = null;
    

    #[ORM\Column(type: "decimal", precision: 10, scale: 2)]
    #[Assert\NotBlank(message: "Le prix is required")]
    #[Assert\Positive(message: "price must be positive")]
        private ?float $prix = null;

    #[ORM\Column(type: "date", nullable: true)]
    private ?\DateTimeInterface $dateReparation = null; // Peut être NULL


  

    // Getters & Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }
    
  
    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(?string $model): self
    {
        $this->model = $model;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getDateAchat(): ?\DateTimeInterface
    {
        return $this->dateAchat;
    }

    public function setDateAchat(?\DateTimeInterface $dateAchat): self
    {
        $this->dateAchat = $dateAchat;
        return $this;
    }
    

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(?float $prix): self
    {
        $this->prix = $prix;
        return $this;
    }

    public function getDateReparation(): ?\DateTimeInterface
    {
        return $this->dateReparation;
    }

    public function setDateReparation(?\DateTimeInterface $dateReparation): self
    {
        $this->dateReparation = $dateReparation;
        return $this;
    }

   

    

    #[ORM\OneToMany(mappedBy: 'equipment', targetEntity: Historique::class)]
    private $historiques;

    public function __construct()
    {
        $this->historiques = new ArrayCollection();
        $this->claims = new ArrayCollection();
    }

    public function getHistoriques(): Collection
    {
        return $this->historiques;
    }

    public function addHistorique(Historique $historique): static
    {
        if (!$this->historiques->contains($historique)) {
            $this->historiques[] = $historique;
            $historique->setEquipment($this);
        }
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Claim::class, mappedBy: 'equipment', cascade: ['persist', 'remove'])]
    private Collection $claims;  
 
    public function getClaims(): Collection
    {
        return $this->claims;
    }
    
    public function addClaim(Claim $claim): self
    {
        if (!$this->claims->contains($claim)) {
            $this->claims[] = $claim;
            $claim->setEquipment($this);  
        }
    
        return $this;
    }
    
    public function removeClaim(Claim $claim): self
    {
        $this->claims->removeElement($claim);
        return $this;
    }
    
}