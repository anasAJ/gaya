<?php

namespace App\Entity;

use App\Repository\ProductionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductionRepository::class)]
class Production
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'productions')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'productions')]
    private ?Client $client = null;


    #[ORM\Column(nullable: true)]
    private ?float $app_fees = null;

    #[ORM\ManyToOne(inversedBy: 'productions', fetch:'EAGER')]
    private ?Signature $signature_provider = null;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\ManyToMany(targetEntity: Product::class, inversedBy: 'productions')]
    private Collection $product;

    #[ORM\Column(nullable: true)]
    private ?array $custom_fields = null;

    /**
     * @var Collection<int, Contract>
     */
    #[ORM\OneToMany(targetEntity: Contract::class, mappedBy: 'production')]
    private Collection $contracts;




    public function __construct()
    {
        $this->product = new ArrayCollection();
        $this->contracts = new ArrayCollection();
    }

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

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getAppFees(): ?float
    {
        return $this->app_fees;
    }

    public function setAppFees(?float $app_fees): static
    {
        $this->app_fees = $app_fees;

        return $this;
    }

    public function getSignatureProvider(): ?Signature
    {
        return $this->signature_provider;
    }

    public function setSignatureProvider(?Signature $signature_provider): static
    {
        $this->signature_provider = $signature_provider;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProduct(): Collection
    {
        return $this->product;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->product->contains($product)) {
            $this->product->add($product);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        $this->product->removeElement($product);

        return $this;
    }

    public function getCustomFields(): ?array
    {
        return $this->custom_fields;
    }

    public function setCustomFields(?array $custom_fields): static
    {
        $this->custom_fields = $custom_fields;

        return $this;
    }

    /**
     * @return Collection<int, Contract>
     */
    public function getContracts(): Collection
    {
        return $this->contracts;
    }

    public function addContract(Contract $contract): static
    {
        if (!$this->contracts->contains($contract)) {
            $this->contracts->add($contract);
            $contract->setProduction($this);
        }

        return $this;
    }

    public function removeContract(Contract $contract): static
    {
        if ($this->contracts->removeElement($contract)) {
            // set the owning side to null (unless already changed)
            if ($contract->getProduction() === $this) {
                $contract->setProduction(null);
            }
        }

        return $this;
    }


}
