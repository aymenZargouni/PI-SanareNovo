<?php
namespace App\Entity;

use App\Repository\ClaimRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class Claim
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message:"Reclamation is required")]
    private $reclamation;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable(); 
    }

    #[ORM\ManyToOne(targetEntity: Equipment::class, inversedBy: 'claims')]
    #[ORM\JoinColumn(nullable: false)]
    private $equipment;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReclamation(): ?string
    {
        return $this->reclamation;
    }

    public function setReclamation(?string $reclamation): self
    {
        $this->reclamation = $reclamation;
        return $this;
    }

    public function getEquipment(): ?Equipment
    {
        return $this->equipment;
    }

    public function setEquipment(Equipment $equipment): self
    {
        $this->equipment = $equipment;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }
}
