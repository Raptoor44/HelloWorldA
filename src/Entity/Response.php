<?php

namespace App\Entity;

use App\Repository\ResponseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: ResponseRepository::class)]
class Response
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\Column(nullable: false)]
    private int $numberLikes = 0;

    #[ORM\ManyToOne(inversedBy: 'responses')]
    #[ORM\JoinColumn(nullable: false)]
    #[MaxDepth(1)]
    private ?UserAccount $userAccount = null;

    #[ORM\ManyToOne(inversedBy: 'responses')]
    #[MaxDepth(1)]
    private ?Tweet $tweet = null;



    public function __construct()
    {
        $this->userAccount = new ArrayCollection();
        $this->tweet = new ArrayCollection();
    }

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

    public function getUserAccount(): ?UserAccount
    {
        return $this->userAccount;
    }

    public function setUserAccount(?UserAccount $userAccount): static
    {
        $this->userAccount = $userAccount;

        return $this;
    }

    public function getTweet(): ?Tweet
    {
        return $this->tweet;
    }

    public function setTweet(?Tweet $tweet): static
    {
        $this->tweet = $tweet;

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


}
