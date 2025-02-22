<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    private ?string $description = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'team')]
    private Collection $users;

    /**
     * @var Collection<int, Predictive>
     */
    #[ORM\OneToMany(targetEntity: Predictive::class, mappedBy: 'team')]
    private Collection $predictives;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->predictives = new ArrayCollection();
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setTeam($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getTeam() === $this) {
                $user->setTeam(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Predictive>
     */
    public function getPredictives(): Collection
    {
        return $this->predictives;
    }

    public function addPredictive(Predictive $predictive): static
    {
        if (!$this->predictives->contains($predictive)) {
            $this->predictives->add($predictive);
            $predictive->setTeam($this);
        }

        return $this;
    }

    public function removePredictive(Predictive $predictive): static
    {
        if ($this->predictives->removeElement($predictive)) {
            // set the owning side to null (unless already changed)
            if ($predictive->getTeam() === $this) {
                $predictive->setTeam(null);
            }
        }

        return $this;
    }
}
