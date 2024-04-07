<?php

namespace App\Entity;

use App\Repository\LogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: LogRepository::class)]
class Log
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;
    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\ManyToOne(targetEntity: UserAccount::class, inversedBy: 'logs')]
    #[ORM\JoinColumn(name: "id_user", referencedColumnName: "id")]
    #[MaxDepth(1)]
    private ?UserAccount $user = null;

    #[ORM\Column(length: 255)]
    private ?string $methodLibelle = null;

    #[ORM\Column(length: 255)]
    private ?string $controllerLibelle = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getMethodLibelle(): ?string
    {
        return $this->methodLibelle;
    }

    public function setMethodLibelle(string $methodLibelle): static
    {
        $this->methodLibelle = $methodLibelle;

        return $this;
    }

    public function getControllerLibelle(): ?string
    {
        return $this->controllerLibelle;
    }

    public function setControllerLibelle(string $controllerLibelle): static
    {
        $this->controllerLibelle = $controllerLibelle;

        return $this;
    }

    public function getIdUser(): ?UserAccount
    {
        return $this->user;
    }

    public function setIdUser(?UserAccount $user): static
    {
        $this->user = $user;

        return $this;
    }
}
