<?php

namespace App\Entity;

use App\Repository\UserAccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use OpenApi\Attributes as OA;

#[ORM\Entity(repositoryClass: UserAccountRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class UserAccount implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[OA\Property(description: 'The unique identifier of the user.')]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[OA\Property(type: 'string', maxLength: 255)]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    #[OA\Property(type: 'string', maxLength: 255)]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[OA\Property(type: 'string', maxLength: 255)]
    private ?string $password = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[OA\Property(type: 'Date', maxLength: 255)]
    private ?\DateTimeInterface $atCreated = null;

    #[ORM\Column(length: 20)]
    #[OA\Property(type: 'string', maxLength: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 20)]
    #[OA\Property(type: 'string', maxLength: 255)]
    private ?string $lastName = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Tweet::class, cascade: ['persist'])]
    #[MaxDepth(1)]
    private ?Collection $tweets;

    #[ORM\OneToMany(targetEntity: Response::class, mappedBy: 'userAccount')]
    #[MaxDepth(1)]
    private Collection $responses;

    #[ORM\OneToMany(targetEntity: Log::class, mappedBy: 'user')]
    #[MaxDepth(1)]
    private Collection $logs;

    public function __construct()
    {
        $this->tweets = new ArrayCollection();
        $this->responses = new ArrayCollection();
        $this->logs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return Collection<int, Tweet>
     */
    public function getTweets(): Collection
    {
        return $this->tweets;
    }

    public function addTweet(Tweet $tweet): static
    {
        if (!$this->tweets->contains($tweet)) {
            $this->tweets->add($tweet);
            $tweet->setUser($this);
        }

        return $this;
    }

    public function removeTweet(Tweet $tweet): static
    {
        if ($this->tweets->removeElement($tweet)) {
            // set the owning side to null (unless already changed)
            if ($tweet->getUser() === $this) {
                $tweet->setUser(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection<int, Response>
     */
    public function getResponses(): Collection
    {
        return $this->responses;
    }

    public function addResponse(Response $response): static
    {
        if (!$this->responses->contains($response)) {
            $this->responses->add($response);
            $response->setUserAccount($this);
        }

        return $this;
    }

    public function removeResponse(Response $response): static
    {
        if ($this->responses->removeElement($response)) {
            // set the owning side to null (unless already changed)
            if ($response->getUserAccount() === $this) {
                $response->setUserAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Log>
     */
    public function getLogs(): Collection
    {
        return $this->logs;
    }

    public function addLog(Log $log): static
    {
        if (!$this->logs->contains($log)) {
            $this->logs->add($log);
            $log->setIdUser($this);
        }

        return $this;
    }

    public function removeLog(Log $log): static
    {
        if ($this->logs->removeElement($log)) {
            // set the owning side to null (unless already changed)
            if ($log->getIdUser() === $this) {
                $log->setIdUser(null);
            }
        }

        return $this;
    }

}
