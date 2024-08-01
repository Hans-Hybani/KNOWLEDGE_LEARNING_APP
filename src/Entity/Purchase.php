<?php

namespace App\Entity;

use App\Repository\PurchaseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PurchaseRepository::class)]
class Purchase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'purchases')]
    private ?User $userPurchase = null;

    #[ORM\ManyToOne(inversedBy: 'purchases')]
    private ?Lesson $lesson = null;

    #[ORM\ManyToOne(inversedBy: 'purchases')]
    private ?Cursus $cursus = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $purchaseDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserPurchase(): ?User
    {
        return $this->userPurchase;
    }

    public function setUserPurchase(?User $userPurchase): static
    {
        $this->userPurchase = $userPurchase;

        return $this;
    }

    public function getLesson(): ?Lesson
    {
        return $this->lesson;
    }

    public function setLesson(?Lesson $lesson): static
    {
        $this->lesson = $lesson;

        return $this;
    }

    public function getCursus(): ?Cursus
    {
        return $this->cursus;
    }

    public function setCursus(?Cursus $cursus): static
    {
        $this->cursus = $cursus;

        return $this;
    }

    public function getPurchaseDate(): ?\DateTimeInterface
    {
        return $this->purchaseDate;
    }

    public function setPurchaseDate(\DateTimeInterface $purchaseDate): static
    {
        $this->purchaseDate = $purchaseDate;

        return $this;
    }
}
