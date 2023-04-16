<?php

namespace App\Entity;

use App\Repository\PlayerRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PlayerRepository::class)]
class Player
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private string $givenName;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private string $familyName;

    #[ORM\ManyToOne(inversedBy: 'players')]
    private ?Team $team = null;

    #[ORM\Column(nullable: true)]
    private ?bool $captain = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGivenName(): ?string
    {
        return $this->givenName;
    }

    public function setGivenName(string $givenName): self
    {
        $this->givenName = $givenName;

        return $this;
    }

    public function getFamilyName(): ?string
    {
        return $this->familyName;
    }

    public function setFamilyName(string $familyName): self
    {
        $this->familyName = $familyName;

        return $this;
    }

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): self
    {
        $this->team = $team;

        return $this;
    }

    public function isCaptain(): ?bool
    {
        return $this->captain ?? false;
    }

    public function setCaptain(?bool $captain): self
    {
        $this->captain = $captain;

        return $this;
    }
}
