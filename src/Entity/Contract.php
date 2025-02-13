<?php

namespace App\Entity;

use App\Repository\ContractRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContractRepository::class)]
class Contract
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'contracts')]
    private ?Product $product = null;

    #[ORM\Column(length: 255)]
    private ?string $pdf_file = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $signed_pdf_file = null;

    #[ORM\Column(nullable: true)]
    private ?array $metatdata = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $Signature_id = null;

    #[ORM\ManyToOne(inversedBy: 'contracts', fetch:'EAGER')]
    private ?Production $production = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPdfFile(): ?string
    {
        return $this->pdf_file;
    }

    public function setPdfFile(string $pdf_file): static
    {
        $this->pdf_file = $pdf_file;

        return $this;
    }

    public function getSignedPdfFile(): ?string
    {
        return $this->signed_pdf_file;
    }

    public function setSignedPdfFile(?string $signed_pdf_file): static
    {
        $this->signed_pdf_file = $signed_pdf_file;

        return $this;
    }

    public function getMetatdata(): ?array
    {
        return $this->metatdata;
    }

    public function setMetatdata(?array $metatdata): static
    {
        $this->metatdata = $metatdata;

        return $this;
    }

    public function getSignatureId(): ?string
    {
        return $this->Signature_id;
    }

    public function setSignatureId(?string $Signature_id): static
    {
        $this->Signature_id = $Signature_id;

        return $this;
    }

    public function getProduction(): ?Production
    {
        return $this->production;
    }

    public function setProduction(?Production $production): static
    {
        $this->production = $production;

        return $this;
    }
}
