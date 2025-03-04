<?php

namespace App\Entity;

use App\Repository\BlogRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BlogRepository::class)]
#[Vich\Uploadable]
class Blog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['blogs'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['blogs'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['blogs'])]
    private ?string $content = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\File(
        maxSize: "2M",
        mimeTypes: ["image/jpeg", "image/png"],
        mimeTypesMessage: "Please upload a valid image (JPEG or PNG)." 
    )]

    #[Groups(['blogs'])]
    private ?string $image = null;

    #[Vich\UploadableField(mapping: "blog_images", fileNameProperty: "image")]
    #[Groups(['blogs'])]
    private ?File $imageFile = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Groups(['blogs'])]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'blogs')]
    private Collection $Category;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'blog')]
    private Collection $comments;

    /**
     * @var Collection<int, Rating>
     */
    #[ORM\OneToMany(targetEntity: Rating::class, mappedBy: 'blog')]
    private Collection $ratings;

    public function __construct()
    {
        $this->Category = new ArrayCollection(); // ✅ Initialisation correcte
        $this->comments = new ArrayCollection();
        $this->ratings = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;
        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;
        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if ($imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategory(): Collection
    {
        return $this->Category;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->Category->contains($category)) {
            $this->Category->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->Category->removeElement($category);

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setBlog($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getBlog() === $this) {
                $comment->setBlog(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Rating>
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    public function addRating(Rating $rating): static
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings->add($rating);
            $rating->setBlog($this);
        }

        return $this;
    }

    public function removeRating(Rating $rating): static
    {
        if ($this->ratings->removeElement($rating)) {
            // set the owning side to null (unless already changed)
            if ($rating->getBlog() === $this) {
                $rating->setBlog(null);
            }
        }

        return $this;
    }

    public function getAverageRating(): ?float
    {
        if ($this->ratings->isEmpty()) {
            return null; // Pas encore de notes
        }

        $total = array_reduce($this->ratings->toArray(), function ($sum, Rating $rating) {
            return $sum + $rating->getScore();
        }, 0);

        return round($total / count($this->ratings), 1); // Note moyenne arrondie à 1 décimale
    }
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]    
    private ?\DateTimeInterface $createdAt = null;
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

}