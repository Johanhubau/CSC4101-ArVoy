<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RegionRepository")
*  @ApiResource(
 *     collectionOperations={
 *         "get"={"method"="GET", "normalization_context"={"groups"={"read"}}},
 *         "post"={"security"="is_granted('ROLE_MODERATOR')"},
 *     },
 *     itemOperations={
 *         "get"={"method"="GET", "normalization_context"={"groups"={"read"}}},
 *         "put"={"security"="is_granted('ROLE_MODERATOR')"},
 *         "delete"={"security"="is_granted('ROLE_MODERATOR')"}
 *     }
 * )
 * * @ApiFilter(SearchFilter::class, properties={"country": "exact", "name": "partial", "presentation": "partial", "rooms": "exact"})
 */
class Region
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
     * @Groups({"read"})
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"read"})
     */
    private $presentation;

    /**
     * @ORM\Column(type="string", length=2, nullable=true)
     * @Groups({"read"})
     */
    private $country;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Room", mappedBy="regions")
     * @ORM\JoinColumn(nullable=true)
     */
    private $rooms;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Document", cascade={"persist", "remove"})
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPresentation(): ?string
    {
        return $this->presentation;
    }

    public function setPresentation(?string $presentation): self
    {
        $this->presentation = $presentation;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
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
            $room->addRegion($this);
        }

        return $this;
    }

    public function removeRoom(Room $room): self
    {
        if ($this->rooms->contains($room)) {
            $this->rooms->removeElement($room);
            $room->removeRegion($this);
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
