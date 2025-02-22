<?php

namespace App\Entity;

use App\Repository\PredictiveRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PredictiveRepository::class)]
class Predictive
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $added_date = null;

    #[ORM\Column(length: 255)]
    private ?string $added_time = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column]
    private ?float $status_percent = null;

    #[ORM\ManyToOne(inversedBy: 'predictives')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'predictives')]
    private ?Team $team = null;

    #[ORM\ManyToOne(inversedBy: 'predictives')]
    private ?Source $source = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAddedDate(): ?string
    {
        return $this->added_date;
    }

    public function setAddedDate(string $added_date): static
    {
        $this->added_date = $added_date;

        return $this;
    }

    public function getAddedTime(): ?string
    {
        return $this->added_time;
    }

    public function setAddedTime(string $added_time): static
    {
        $this->added_time = $added_time;

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

    public function getStatusPercent(): ?float
    {
        return $this->status_percent;
    }

    public function setStatusPercent(float $status_percent): static
    {
        $this->status_percent = $status_percent;

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

    public function getTeam(): ?Team
    {
        return $this->team;
    }

    public function setTeam(?Team $team): static
    {
        $this->team = $team;

        return $this;
    }

    public function getSource(): ?Source
    {
        return $this->source;
    }

    public function setSource(?Source $source): static
    {
        $this->source = $source;

        return $this;
    }
}
