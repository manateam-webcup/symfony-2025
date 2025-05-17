<?php

namespace App\Entity;

use App\Repository\EndingRepository;
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

    public function __construct()
    {
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
}