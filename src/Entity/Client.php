<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $first_name = null;

    #[ORM\Column(length: 255)]
    private ?string $last_name = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $phone = null;

    #[ORM\ManyToOne(inversedBy: 'clients')]
    private ?User $user = null;

    #[ORM\Column(length: 255)]
    private ?string $added_date = null;

    #[ORM\Column(length: 255)]
    private ?string $added_time = null;

    #[ORM\ManyToOne(inversedBy: 'clients')]
    private ?Status $status = null;

    #[ORM\ManyToOne(inversedBy: 'clients')]
    private ?Phase $phase = null;

    /**
     * @var Collection<int, Adresses>
     */
    #[ORM\OneToMany(targetEntity: Adresses::class, mappedBy: 'client')]
    private Collection $adresses;

    #[ORM\ManyToOne(inversedBy: 'clients')]
    private ?Source $Source = null;

    /**
     * @var Collection<int, Production>
     */
    #[ORM\OneToMany(targetEntity: Production::class, mappedBy: 'client')]
    private Collection $productions;

    #[ORM\Column(length: 10)]
    private ?string $indicative = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $Comment = null;

    #[ORM\Column]
    private ?bool $is_predictive = null;

    public function __toString(): string
{
    return $this->getFirstName() . ' ' . $this->getLastName();
}

    public function __construct()
    {
        $this->adresses = new ArrayCollection();
        $this->productions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->first_name;
    }

    public function setFirstName(string $first_name): static
    {
        $this->first_name = $first_name;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->last_name;
    }

    public function setLastName(string $last_name): static
    {
        $this->last_name = $last_name;

        return $this;
    }

    public function getFullName(){
        return $this->getFirstName().' '.$this->getLastName();
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

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

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getPhase(): ?Phase
    {
        return $this->phase;
    }

    public function setPhase(?Phase $phase): static
    {
        $this->phase = $phase;

        return $this;
    }

    /**
     * @return Collection<int, Adresses>
     */
    public function getAdresses(): Collection
    {
        return $this->adresses;
    }

    public function addAdress(Adresses $adress): static
    {
        if (!$this->adresses->contains($adress)) {
            $this->adresses->add($adress);
            $adress->setClient($this);
        }

        return $this;
    }

    public function removeAdress(Adresses $adress): static
    {
        if ($this->adresses->removeElement($adress)) {
            // set the owning side to null (unless already changed)
            if ($adress->getClient() === $this) {
                $adress->setClient(null);
            }
        }

        return $this;
    }

    public function getSource(): ?Source
    {
        return $this->Source;
    }

    public function setSource(?Source $Source): static
    {
        $this->Source = $Source;

        return $this;
    }

    /**
     * @return Collection<int, Production>
     */
    public function getProductions(): Collection
    {
        return $this->productions;
    }

    public function addProduction(Production $production): static
    {
        if (!$this->productions->contains($production)) {
            $this->productions->add($production);
            $production->setClient($this);
        }

        return $this;
    }

    public function removeProduction(Production $production): static
    {
        if ($this->productions->removeElement($production)) {
            // set the owning side to null (unless already changed)
            if ($production->getClient() === $this) {
                $production->setClient(null);
            }
        }

        return $this;
    }

    public function getIndicative(): ?string
    {
        return $this->indicative;
    }

    public function setIndicative(string $indicative): static
    {
        $this->indicative = $indicative;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->Comment;
    }

    public function setComment(?string $Comment): static
    {
        $this->Comment = $Comment;

        return $this;
    }

    public function isPredictive(): ?bool
    {
        return $this->is_predictive;
    }

    public function setPredictive(bool $is_predictive): static
    {
        $this->is_predictive = $is_predictive;

        return $this;
    }
}
