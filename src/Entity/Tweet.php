<?php

namespace App\Entity;

use App\Repository\TweetRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: TweetRepository::class)]
class Tweet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\Column]
    private ?int $numberLikes = null;

    #[ORM\ManyToOne(targetEntity: UserAccount::class, inversedBy: 'tweets', cascade: ['persist'])]
    #[JoinColumn(name: 'id', referencedColumnName: 'id')]
    #[MaxDepth(1)]
    private ?UserAccount $user = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $atCreated = null;

    #[ORM\ManyToOne(inversedBy: 'tweet')]
    private ?Response $response = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNumberLikes(): ?int
    {
        return $this->numberLikes;
    }

    public function setNumberLikes(int $numberLikes): static
    {
        $this->numberLikes = $numberLikes;

        return $this;
    }

    public function getAtCreated(): ?\DateTimeInterface
    {
        return $this->atCreated;
    }

    public function setAtCreated(\DateTimeInterface $atCreated): static
    {
        $this->atCreated = $atCreated;

        return $this;
    }

    public function getUser(): ?UserAccount
    {
        return $this->user;
    }

    public function setUser(?UserAccount $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }

    public function setResponse(?Response $response): static
    {
        $this->response = $response;

        return $this;
    }


}
