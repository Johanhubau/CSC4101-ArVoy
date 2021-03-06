<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OwnerRepository")
 *  @ApiResource(
 *     collectionOperations={
 *         "get"={"security"="is_granted('ROLE_MODERATOR')"},
 *         "post"={"security"="is_granted('ROLE_MODERATOR') or object.getUser() == user"},
 *     },
 *     itemOperations={
 *         "get"={"method"="GET", "normalization_context"={"groups"={"read"}}},
 *         "put"={"security"="is_granted('ROLE_MODERATOR') or object.getUser() == user"},
 *         "delete"={"security"="is_granted('ROLE_MODERATOR') or object.getUser() == user"}
 *     }
 * )
 */
class Owner
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups({"read"})
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     * @Groups({"read"})
     */
    private $lastname;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank()
     * @Groups({"read"})
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=2)
     * @Assert\Country()
     * @Groups({"read"})
     */
    private $country;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Room", mappedBy="owner", orphanRemoval=true)
     * @ORM\JoinColumn(nullable=true)
     */
    private $rooms;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex("/^\d{10}$/")
     * @Groups({"read"})
     */
    private $telephone;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"read"})
     * @Groups({"read"})
     */
    private $validated;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", inversedBy="owner", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"read"})
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\DateTime()
     * @Groups({"read"})
     */
    private $birthdate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Document")
     * @Groups({"read"})
     */
    private $image;

    public function __construct()
    {
        $this->rooms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return Collection|Room[]
     */
    public function getRooms(): Collection
    {
        return $this->rooms;
    }

    public function addRoom(Room $room): self
    {
        if (!$this->rooms->contains($room)) {
            $this->rooms[] = $room;
            $room->setOwner($this);
        }

        return $this;
    }

    public function removeRoom(Room $room): self
    {
        if ($this->rooms->contains($room)) {
            $this->rooms->removeElement($room);
            // set the owning side to null (unless already changed)
            if ($room->getOwner() === $this) {
                $room->setOwner(null);
            }
        }

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getValidated(): ?bool
    {
        return $this->validated;
    }

    public function setValidated(bool $validated): self
    {
        $this->validated = $validated;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(\DateTimeInterface $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getImage(): ?Document
    {
        return $this->image;
    }

    public function setImage(?Document $image): self
    {
        $this->image = $image;

        return $this;
    }
}
