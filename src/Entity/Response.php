<?php

namespace App\Entity;

use App\Repository\ResponseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResponseRepository::class)]
class Response
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $content = null;

    #[ORM\OneToMany(targetEntity: UserAccount::class, mappedBy: 'responses')]
    private Collection $userAccount;

    #[ORM\OneToMany(targetEntity: Tweet::class, mappedBy: 'response')]
    private Collection $tweet;

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

    /**
     * @return Collection<int, UserAccount>
     */
    public function getUserAccount(): Collection
    {
        return $this->userAccount;
    }

    public function addUserAccount(UserAccount $userAccount): static
    {
        if (!$this->userAccount->contains($userAccount)) {
            $this->userAccount->add($userAccount);
            $userAccount->setResponses($this);
        }

        return $this;
    }

    public function removeUserAccount(UserAccount $userAccount): static
    {
        if ($this->userAccount->removeElement($userAccount)) {
            // set the owning side to null (unless already changed)
            if ($userAccount->getResponses() === $this) {
                $userAccount->setResponses(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Tweet>
     */
    public function getTweet(): Collection
    {
        return $this->tweet;
    }

    public function addTweet(Tweet $tweet): static
    {
        if (!$this->tweet->contains($tweet)) {
            $this->tweet->add($tweet);
            $tweet->setResponse($this);
        }

        return $this;
    }

    public function removeTweet(Tweet $tweet): static
    {
        if ($this->tweet->removeElement($tweet)) {
            // set the owning side to null (unless already changed)
            if ($tweet->getResponse() === $this) {
                $tweet->setResponse(null);
            }
        }

        return $this;
    }

}
