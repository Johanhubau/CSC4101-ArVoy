<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RoomRepository")
 *  @ApiResource(
 *     collectionOperations={
 *         "get"={"method"="GET", "normalization_context"={"groups"={"read"}}},
 *         "post"={"security"="is_granted('ROLE_MODERATOR') or object.getOwner() == user.getOwner()"},
 *     },
 *     itemOperations={
 *         "get"={"method"="GET", "normalization_context"={"groups"={"read"}}},
 *         "put"={"security"="is_granted('ROLE_MODERATOR') or object.getOwner() == user.getOwner()"},
 *         "delete"={"security"="is_granted('ROLE_MODERATOR') or object.getOwner() == user.getOwner()"}
 *     }
 * )
 * @ApiFilter(SearchFilter::class, properties={"regions": "exact", "summary": "partial", "description": "partial"})
 * @ApiFilter(RangeFilter::class, properties={"capacity", "superficy", "price"})
 */
class Room
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read"})
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Groups({"read"})
     */
    private $summary;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"read"})
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"read"})
     */
    private $capacity;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"read"})
     */
    private $superficy;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"read"})
     */
    private $price;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"read"})
     */
    private $address;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Owner", inversedBy="rooms")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"read"})
     */
    private $owner;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Region", inversedBy="rooms")
     * @Groups({"read"})
     */
    private $regions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UnavailablePeriod", mappedBy="room", orphanRemoval=true)
     * @ORM\JoinColumn(nullable=true)
     */
    private $unavailablePeriods;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Reservation", mappedBy="room", orphanRemoval=true)
     * @ORM\JoinColumn(nullable=true)
     */
    private $reservations;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Document", cascade={"persist", "remove"})
     * @Groups({"read"})
     */
    private $image;

    public function __construct()
    {
        $this->regions = new ArrayCollection();
        $this->unavailablePeriods = new ArrayCollection();
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getSuperficy(): ?int
    {
        return $this->superficy;
    }

    public function setSuperficy(int $superficy): self
    {
        $this->superficy = $superficy;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getOwner(): ?Owner
    {
        return $this->owner;
    }

    public function setOwner(?Owner $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection|Region[]
     */
    public function getRegions(): Collection
    {
        return $this->regions;
    }

    public function addRegion(Region $region): self
    {
        if (!$this->regions->contains($region)) {
            $this->regions[] = $region;
        }

        return $this;
    }

    public function removeRegion(Region $region): self
    {
        if ($this->regions->contains($region)) {
            $this->regions->removeElement($region);
        }

        return $this;
    }

    /**
     * @return Collection|UnavailablePeriod[]
     */
    public function getUnavailablePeriods(): Collection
    {
        return $this->unavailablePeriods;
    }

    public function addUnavailablePeriod(UnavailablePeriod $unavailablePeriod): self
    {
        if (!$this->unavailablePeriods->contains($unavailablePeriod)) {
            $this->unavailablePeriods[] = $unavailablePeriod;
            $unavailablePeriod->setRoom($this);
        }

        return $this;
    }

    public function removeUnavailablePeriod(UnavailablePeriod $unavailablePeriod): self
    {
        if ($this->unavailablePeriods->contains($unavailablePeriod)) {
            $this->unavailablePeriods->removeElement($unavailablePeriod);
            // set the owning side to null (unless already changed)
            if ($unavailablePeriod->getRoom() === $this) {
                $unavailablePeriod->setRoom(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Reservation[]
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setRoom($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->contains($reservation)) {
            $this->reservations->removeElement($reservation);
            // set the owning side to null (unless already changed)
            if ($reservation->getRoom() === $this) {
                $reservation->setRoom(null);
            }
        }

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
