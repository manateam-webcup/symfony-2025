<?php

namespace App\Entity;

use App\Repository\UserEndingInteractionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserEndingInteractionRepository::class)]
#[ORM\UniqueConstraint(name: 'user_ending_unique', fields: ['user', 'ending'])]
class UserEndingInteraction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ending $ending = null;

    #[ORM\Column(length: 10)]
    private ?string $type = null; // 'like' or 'dislike'

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEnding(): ?Ending
    {
        return $this->ending;
    }

    public function setEnding(?Ending $ending): static
    {
        $this->ending = $ending;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        if (!in_array($type, ['like', 'dislike'])) {
            throw new \InvalidArgumentException('Invalid interaction type');
        }
        
        $this->type = $type;

        return $this;
    }
}