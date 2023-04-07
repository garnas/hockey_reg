<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $turnierform = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nationalitaet = null;

    #[ORM\Column]
    private ?bool $bezahlt = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private array $emails = [];

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $passwort = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTurnierform(): ?string
    {
        return $this->turnierform;
    }

    public function setTurnierform(?string $turnierform): self
    {
        $this->turnierform = $turnierform;

        return $this;
    }

    public function getNationalitaet(): ?string
    {
        return $this->nationalitaet;
    }

    public function setNationalitaet(?string $nationalitaet): self
    {
        $this->nationalitaet = $nationalitaet;

        return $this;
    }

    public function isBezahlt(): ?bool
    {
        return $this->bezahlt;
    }

    public function setBezahlt(bool $bezahlt): self
    {
        $this->bezahlt = $bezahlt;

        return $this;
    }

    public function getEmails(): array
    {
        return $this->emails;
    }

    public function setEmails(?array $emails): self
    {
        $this->emails = $emails;

        return $this;
    }

    public function getPasswort(): ?string
    {
        return $this->passwort;
    }

    public function setPasswort(?string $passwort): self
    {
        $this->passwort = $passwort;

        return $this;
    }
}
