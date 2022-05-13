<?php
namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use App\Entity\Product;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: product::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $product;

    #[ORM\ManyToOne(targetEntity: user::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $customer;

    #[ORM\Column(type: 'decimal', precision: 7, scale: 2)]
    private $quantity;

    #[ORM\Column(type: 'string', length: 255)]
    private $full_address;

    #[ORM\Column(type: 'date', nullable: true)]
    private $shipping_date;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $delivered_at;

    #[ORM\Column(type: 'string', length: 1)]
    private $status;

    #[ORM\Column(type: 'datetime')]
    private $created_at;

    #[ORM\Column(type: 'datetime')]
    private $updated_at;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private $total_price;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getCustomer(): ?User
    {
        return $this->customer;
    }

    public function setCustomer(User $customer): self
    {
        $this->customer = $customer;

        return $this;
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(string $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getFullAddress(): ?string
    {
        return $this->full_address;
    }

    public function setFullAddress(string $full_address): self
    {
        $this->full_address = $full_address;

        return $this;
    }

    public function getShippingDate(): ?\DateTimeInterface
    {
        return $this->shipping_date;
    }

    public function setShippingDate(?\DateTimeInterface $shipping_date): self
    {
        $this->shipping_date = $shipping_date;

        return $this;
    }

    public function getDeliveredAt(): ?\DateTimeInterface
    {
        return $this->delivered_at;
    }

    public function setDeliveredAt(?\DateTimeInterface $delivered_at): self
    {
        $this->delivered_at = $delivered_at;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getTotalPrice(): ?string
    {
        return $this->total_price;
    }

    public function setTotalPrice(string $total_price): self
    {
        $this->total_price = $total_price;

        return $this;
    }
}
