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

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Product $product = null;

    #[ORM\Column(nullable: true)]
    private ?float $app_fees = null;

    #[ORM\ManyToOne(inversedBy: 'productions')]
    private ?Signature $signature_provider = null;




    public function __construct()
    {
        $this->products = new ArrayCollection();
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

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

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


}
