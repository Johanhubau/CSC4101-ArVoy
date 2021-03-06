<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use http\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReservationRepository")
 * @ApiResource
 * @ORM\HasLifecycleCallbacks()
 *  @ApiResource(
 *     collectionOperations={
 *         "get",
 *         "post"={"security"="is_granted('ROLE_CLIENT')"},
 *     },
 *     itemOperations={
 *         "get",
 *         "put"={"security"="is_granted('ROLE_MODERATOR') or object.getClient() == user.getClient()"},
 *         "delete"={"security"="is_granted('ROLE_MODERATOR') or object.getClient() == user.getClient()"}
 *     }
 * )
 */
class Reservation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Client", inversedBy="reservations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Client", inversedBy="reservations")
     */
    private $occupants;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Room", inversedBy="reservations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $room;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\DateTime()
     */
    private $start;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date()
     */
    private $until;

    /**
     * @ORM\Column(type="boolean")
     */
    private $validated;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(max=255)
     */
    private $message;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="reservation",orphanRemoval=true)
     * @ORM\JoinColumn(nullable=true)
     */
    private $comments;

    public function __construct()
    {
        $this->occupants = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function checkIfFree()
    {
        $free = true;
        foreach ($this->room->getReservations() as $reservation) {
            if (!($this->until <= $reservation->start || $this->start >= $reservation->until)) {
                $free = false;
                break;
            }
        }

        if ($free) {
            foreach ($this->room->getUnavailablePeriods() as $period) {
                if (!($this->until <= $period->getStart() || $this->start >= $period->getUntil())) {
                    $free = false;
                    break;
                }
            }
        }

        if (!$free) {
            throw new \ApiPlatform\Core\Exception\InvalidArgumentException("The room is not available during this period");
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Collection|Client[]
     */
    public function getOccupants(): Collection
    {
        return $this->occupants;
    }

    public function addOccupant(Client $occupant): self
    {
        if (!$this->occupants->contains($occupant)) {
            $this->occupants[] = $occupant;
        }

        return $this;
    }

    public function removeOccupant(Client $occupant): self
    {
        if ($this->occupants->contains($occupant)) {
            $this->occupants->removeElement($occupant);
        }

        return $this;
    }

    public function getRoom(): ?Room
    {
        return $this->room;
    }

    public function setRoom(?Room $room): self
    {
        $this->room = $room;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getUntil(): ?\DateTimeInterface
    {
        return $this->until;
    }

    public function setUntil(\DateTimeInterface $until): self
    {
        $this->until = $until;

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

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setReservation($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getReservation() === $this) {
                $comment->setReservation(null);
            }
        }

        return $this;
    }
}
