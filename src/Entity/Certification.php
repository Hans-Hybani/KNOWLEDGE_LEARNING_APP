<?php

namespace App\Entity;

use App\Repository\CertificationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CertificationRepository::class)]
class Certification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'certifications')]
    private ?User $userCertification = null;

    #[ORM\ManyToOne(inversedBy: 'certifications')]
    private ?Theme $themeCertification = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $certificationDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserCertification(): ?User
    {
        return $this->userCertification;
    }

    public function setUserCertification(?User $userCertification): static
    {
        $this->userCertification = $userCertification;

        return $this;
    }

    public function getThemeCertification(): ?Theme
    {
        return $this->themeCertification;
    }

    public function setThemeCertification(?Theme $themeCertification): static
    {
        $this->themeCertification = $themeCertification;

        return $this;
    }

    public function getCertificationDate(): ?\DateTimeInterface
    {
        return $this->certificationDate;
    }

    public function setCertificationDate(\DateTimeInterface $certificationDate): static
    {
        $this->certificationDate = $certificationDate;

        return $this;
    }
}
