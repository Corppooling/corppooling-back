<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\TripRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TripRepository::class)]
#[ApiResource]
class Trip
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['show_trip', 'list_trip'])]
    private $id;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'trips')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['show_trip', 'list_trip'])]
    private $company;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'trips')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['show_trip', 'list_trip'])]
    private $announcer;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['show_trip', 'list_trip'])]
    private $type;


    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['show_trip', 'list_trip'])]
    private $departure_location;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['show_trip', 'list_trip'])]
    private $arrival_location;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['show_trip', 'list_trip'])]
    private $available_seats;

    #[Groups(['show_trip', 'list_trip'])]
    #[ORM\Column(type: 'datetime')]
    private $departure_time;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['show_trip', 'list_trip'])]
    private $message;

    #[ORM\Column(type: 'float', length: 255, nullable: true)]
    #[Groups(['show_trip', 'list_trip'])]
    private $price;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['show_trip', 'list_trip'])]

    private $car_model;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['show_trip', 'list_trip'])]

    private $car_color;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['show_trip', 'list_trip'])]

    private $updated_at;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['show_trip', 'list_trip'])]


    private $created_at;

    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'trip')]
    #[Groups(['show_trip', 'list_trip'])]

    private $reservations;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getAnnouncer(): ?User
    {
        return $this->announcer;
    }

    public function setAnnouncer(?User $announcer): self
    {
        $this->announcer = $announcer;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDepartureLocation(): ?string
    {
        return $this->departure_location;
    }

    public function setDepartureLocation(string $departure_location): self
    {
        $this->departure_location = $departure_location;

        return $this;
    }

    public function getArrivalLocation(): ?string
    {
        return $this->arrival_location;
    }

    public function setArrivalLocation(string $arrival_location): self
    {
        $this->arrival_location = $arrival_location;

        return $this;
    }

    public function getAvailableSeats(): ?int
    {
        return $this->available_seats;
    }

    public function setAvailableSeats(?int $available_seats): self
    {
        $this->available_seats = $available_seats;

        return $this;
    }

    public function getDepartureTime(): ?\DateTimeInterface
    {
        return $this->departure_time;
    }

    public function setDepartureTime(\DateTimeInterface $departure_time): self
    {
        $this->departure_time = $departure_time;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCarModel(): ?string
    {
        return $this->car_model;
    }

    public function setCarModel(?string $car_model): self
    {
        $this->car_model = $car_model;

        return $this;
    }

    public function getCarColor(): ?string
    {
        return $this->car_color;
    }

    public function setCarColor(?string $car_color): self
    {
        $this->car_color = $car_color;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setTrip($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getTrip() === $this) {
                $reservation->setTrip(null);
            }
        }

        return $this;
    }
}
