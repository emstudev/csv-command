<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: App\Repository\StockItemRepository::class)]
#[ORM\Table(name: "stock_item")]
class StockItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:"integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 100, nullable: true)]
    private ?string $ean = null;

    #[ORM\Column(type: "string", length: 100, nullable: true)]
    private ?string $mpn = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $producerName = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $externalId = null;

    #[ORM\Column(type: "decimal", precision: 20, scale: 2)]
    private float $price = 0.0;

    #[ORM\Column(type: "integer")]
    private int $quantity = 0;

    public function getId(): ?int { return $this->id; }

    public function getEan(): ?string { return $this->ean; }
    public function setEan(?string $ean): void
    {
        $this->ean = $ean;
    }

    public function getMpn(): ?string { return $this->mpn; }
    public function setMpn(?string $mpn): self { $this->mpn = $mpn; return $this; }

    public function getProducerName(): ?string { return $this->producerName; }
    public function setProducerName(?string $producerName): self
    {
        $this->producerName = $producerName;
        return $this;
    }

    public function getExternalId(): ?string { return $this->externalId; }
    public function setExternalId(?string $externalId): self { $this->externalId = $externalId; return $this; }

    public function getPrice(): float { return (float)$this->price; }
    public function setPrice(float $price): self { $this->price = $price; return $this; }

    public function getQuantity(): int { return $this->quantity; }
    public function setQuantity(int $quantity): self { $this->quantity = $quantity; return $this; }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'ean' => $this->ean,
            'mpn' => $this->mpn,
            'producerName' => $this->producerName,
            'externalId' => $this->externalId,
            'price' => (float)$this->price,
            'quantity' => $this->quantity,
        ];
    }
}
