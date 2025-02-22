<?php

namespace App\Entity;

use App\Repository\SourceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SourceRepository::class)]
class Source
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'sources')]
    private Collection $category;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\ManyToMany(targetEntity: Product::class, inversedBy: 'sources')]
    private Collection $Product;

    #[ORM\Column(length: 255)]
    private ?string $added_date = null;

    #[ORM\Column(length: 255)]
    private ?string $added_time = null;

    #[ORM\Column(length: 255)]
    private ?string $token = null;

    /**
     * @var Collection<int, Client>
     */
    #[ORM\OneToMany(targetEntity: Client::class, mappedBy: 'Source')]
    private Collection $clients;

    /**
     * @var Collection<int, Predictive>
     */
    #[ORM\OneToMany(targetEntity: Predictive::class, mappedBy: 'source')]
    private Collection $predictives;

    public function __construct()
    {
        $this->category = new ArrayCollection();
        $this->Product = new ArrayCollection();
        $this->clients = new ArrayCollection();
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

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): static
    {
        $this->url = $url;

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

    /**
     * @return Collection<int, Category>
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->category->contains($category)) {
            $this->category->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        $this->category->removeElement($category);

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProduct(): Collection
    {
        return $this->Product;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->Product->contains($product)) {
            $this->Product->add($product);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        $this->Product->removeElement($product);

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

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    /**
     * @return Collection<int, Client>
     */
    public function getClients(): Collection
    {
        return $this->clients;
    }

    public function addClient(Client $client): static
    {
        if (!$this->clients->contains($client)) {
            $this->clients->add($client);
            $client->setSource($this);
        }

        return $this;
    }

    public function removeClient(Client $client): static
    {
        if ($this->clients->removeElement($client)) {
            // set the owning side to null (unless already changed)
            if ($client->getSource() === $this) {
                $client->setSource(null);
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
            $predictive->setSource($this);
        }

        return $this;
    }

    public function removePredictive(Predictive $predictive): static
    {
        if ($this->predictives->removeElement($predictive)) {
            // set the owning side to null (unless already changed)
            if ($predictive->getSource() === $this) {
                $predictive->setSource(null);
            }
        }

        return $this;
    }
}
