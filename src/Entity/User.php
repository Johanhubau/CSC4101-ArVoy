<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Scalar\String_;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 *  @ApiResource(
 *     collectionOperations={
 *         "get"={"security"="is_granted('ROLE_MODERATOR')"},
 *         "post"={"security"="is_granted('ROLE_MODERATOR')"},
 *     },
 *     itemOperations={
 *         "get"={"security"="object == user"},
 *         "put"={"security"="is_granted('ROLE_MODERATOR') or object == user"},
 *         "delete"={"security"="is_granted('ROLE_MODERATOR')"}
 *     }
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotCompromisedPassword()
     */
    private $password;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $emailVerified;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Owner", mappedBy="user",  cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $owner;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Staff", mappedBy="user",  cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $staff;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Client", mappedBy="user", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $client;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        if (!in_array("ROLE_USER", $roles)) $roles[] = 'ROLE_USER';
        if ($this->client != null && !in_array("ROLE_CLIENT", $roles)) $roles[] = "ROLE_CLIENT";

        return $roles;
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getEmailVerified(): ?\DateTimeInterface
    {
        return $this->emailVerified;
    }

    public function setEmailVerified(?\DateTimeInterface $emailVerified): self
    {
        $this->emailVerified = $emailVerified;

        return $this;
    }

    public function getOwner(): ?Owner
    {
        return $this->owner;
    }

    public function setOwner(Owner $owner): self
    {
        $this->owner = $owner;

        // set the owning side of the relation if necessary
        if ($this !== $owner->getUser()) {
            $owner->setUser($this);
        }

        return $this;
    }

    public function getStaff(): ?Staff
    {
        return $this->staff;
    }

    public function setStaff(Staff $staff): self
    {
        $this->staff = $staff;

        // set the owning side of the relation if necessary
        if ($this !== $staff->getUser()) {
            $staff->setUser($this);
        }

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;

        // set the owning side of the relation if necessary
        if ($this !== $client->getUser()) {
            $client->setUser($this);
        }
        $this->setRoles($this->roles + ["ROLE_CLIENT"]);

        return $this;
    }

    public function getDisplayName(): String
    {
        $displayName = "";
        if ($this->getClient()) {
            $displayName = $this->getClient()->getFirstname() . " " . $this->getClient()->getLastname();
        }
        else if ($this->getOwner()) {
            $displayName = $this->getOwner()->getFirstname() . " " . $this->getOwner()->getLastname();
        }
        else if ($this->getStaff()) {
            $displayName = $this->getStaff()->getFirstname() . " " . $this->getStaff()->getLastname();
        } else {
            $displayName = "Inconnu";
        }

        return $displayName;
    }

    public function getInformation() : array
    {
        $infoarr = array();
        $info = null;
        if ($this->getClient()) {
            $info = $this->getClient();
        }
        else if ($this->getOwner()) {
            $info = $this->getOwner();
        }

        if ($info != null) {
            $infoarr += array(
                "user_id" => $this->getId(),
                "client_id" => $this->getClient() != null ? $this->getClient()->getId() : null,
                "owner_id" => $this->getOwner() != null ? $this->getOwner()->getId() : null,
                "staff_id" => $this->getStaff() != null ? $this->getStaff()->getId() : null,
                "firstname" => $info->getFirstname(),
                "lastname" => $info->getLastname(),
                "address" => $info->getAddress(),
                "telephone" => $info->getTelephone(),
                "email" => $this->getEmail(),
                "country" => $info->getCountry(),
                "birthdate" => $info->getBirthdate()->format('d/m/Y')
            );
        }
        return $infoarr;
    }
}
