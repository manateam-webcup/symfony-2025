<?php

namespace App\Entity;

use App\Repository\EndingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EndingRepository::class)]
class Ending
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $title = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['sad', 'shocking', 'happy', 'angry'])]
    private ?string $emotion = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image1Path = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image2Path = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image3Path = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $audioPath = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $videoPath = null;

    #[ORM\ManyToOne(inversedBy: 'endings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(length: 255)]
    private ?string $status = 'pending';

    #[ORM\Column]
    private int $likes = 0;

    #[ORM\Column]
    private int $dislikes = 0;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $moderationReason = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $approvedBy = null;

    #[ORM\Column(nullable: true)]
    private ?float $moderationScore = null;

    #[ORM\OneToMany(mappedBy: 'ending', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->status = 'pending';
        $this->likes = 0;
        $this->dislikes = 0;
        $this->comments = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getEmotion(): ?string
    {
        return $this->emotion;
    }

    public function setEmotion(string $emotion): static
    {
        $this->emotion = $emotion;

        return $this;
    }

    public function getImage1Path(): ?string
    {
        return $this->image1Path;
    }

    public function setImage1Path(?string $image1Path): static
    {
        $this->image1Path = $image1Path;

        return $this;
    }

    public function getImage2Path(): ?string
    {
        return $this->image2Path;
    }

    public function setImage2Path(?string $image2Path): static
    {
        $this->image2Path = $image2Path;

        return $this;
    }

    public function getImage3Path(): ?string
    {
        return $this->image3Path;
    }

    public function setImage3Path(?string $image3Path): static
    {
        $this->image3Path = $image3Path;

        return $this;
    }

    public function getAudioPath(): ?string
    {
        return $this->audioPath;
    }

    public function setAudioPath(?string $audioPath): static
    {
        $this->audioPath = $audioPath;

        return $this;
    }

    public function getVideoPath(): ?string
    {
        return $this->videoPath;
    }

    public function setVideoPath(?string $videoPath): static
    {
        $this->videoPath = $videoPath;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

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

    public function getLikes(): int
    {
        return $this->likes;
    }

    public function setLikes(int $likes): static
    {
        $this->likes = $likes;

        return $this;
    }

    public function incrementLikes(): static
    {
        $this->likes++;

        return $this;
    }

    public function decrementLikes(): static
    {
        if ($this->likes > 0) {
            $this->likes--;
        }

        return $this;
    }

    public function getDislikes(): int
    {
        return $this->dislikes;
    }

    public function setDislikes(int $dislikes): static
    {
        $this->dislikes = $dislikes;

        return $this;
    }

    public function incrementDislikes(): static
    {
        $this->dislikes++;

        return $this;
    }

    public function decrementDislikes(): static
    {
        if ($this->dislikes > 0) {
            $this->dislikes--;
        }

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
            $comment->setEnding($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getEnding() === $this) {
                $comment->setEnding(null);
            }
        }

        return $this;
    }

    public function getModerationReason(): ?string
    {
        return $this->moderationReason;
    }

    public function setModerationReason(?string $moderationReason): static
    {
        $this->moderationReason = $moderationReason;

        return $this;
    }

    public function getApprovedBy(): ?string
    {
        return $this->approvedBy;
    }

    public function setApprovedBy(?string $approvedBy): static
    {
        $this->approvedBy = $approvedBy;

        return $this;
    }

    public function getModerationScore(): ?float
    {
        return $this->moderationScore;
    }

    public function setModerationScore(?float $moderationScore): static
    {
        $this->moderationScore = $moderationScore;

        return $this;
    }
}
