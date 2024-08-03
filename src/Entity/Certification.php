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

    #[ORM\ManyToOne(inversedBy: 'certifications')]
    private ?Cursus $cursus = null;

    #[ORM\Column(length: 255)]
    private ?string $certificationDoc = null;

    #[ORM\ManyToOne(inversedBy: 'certifications')]
    private ?Lesson $lesson = null;

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

    public function getCursus(): ?Cursus
    {
        return $this->cursus;
    }

    public function setCursus(?Cursus $cursus): static
    {
        $this->cursus = $cursus;

        return $this;
    }

    public function getCertificationDoc(): ?string
    {
        return $this->certificationDoc;
    }

    public function setCertificationDoc(string $certificationDoc): static
    {
        $this->certificationDoc = $certificationDoc;

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
}
