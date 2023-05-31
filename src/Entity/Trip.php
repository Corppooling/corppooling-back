<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\DoctrineType\TripMissing;
use App\Repository\TripRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: TripRepository::class)]
#[ApiResource(normalizationContext: [AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true, 'groups' => ['show_trip', 'list_trip', 'list_company', 'list_department', 'list_user']], order: ['departure_time' => 'ASC'])]
#[ApiFilter(OrderFilter::class, properties: ['id', 'name', 'created_at', 'departure_time', 'price'], arguments: ['orderParameterName' => 'order'])]
#[ApiFilter(RangeFilter::class, properties: ['available_seats'])]
#[ApiFilter(DateFilter::class, strategy: DateFilter::EXCLUDE_NULL, properties: ['departure_time'])]
#[ApiFilter(SearchFilter::class, properties: ['id' => 'exact', 'price' => 'exact', 'type' => 'exact', 'departure_location' => 'partial', 'arrival_location' => 'partial', 'company.id' => 'exact', 'company.cluster.id' => 'exact'])]
class Trip
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['show_trip', 'list_trip'])]
    private $id;

    #[Groups(['show_timestamps'])]
    #[ORM\Column(type: 'datetime_immutable')]
    private $updated_at;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['show_timestamps'])]
    private $created_at;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'trips')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['show_trip', 'list_trip'])]
    private $company;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'trips')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['show_trip'])]
    private $announcer;

    #[ORM\Column(type: 'tripMissing', length: 20)]
    #[Groups(['show_trip', 'list_trip'])]
    private $type;


    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['show_trip', 'list_trip'])]
    private $departure_location;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['show_trip', 'list_trip'])]
    private $arrival_location;

    #[ORM\Column(type: 'integer', nullable: true)]
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

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'reservations')]
    #[Groups(['show_trip', 'list_trip'])]
    #[MaxDepth(2)]
    private $members;

    public function __construct()
    {
        $this->members = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
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

    public function getType(): ?TripMissing
    {
        return $this->type;
    }

    public function setType(TripMissing $type): self
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

    /**
     * @return Collection<int, User>
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(User $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
        }

        return $this;
    }

    public function removeMember(User $member): self
    {
        $this->members->removeElement($member);

        return $this;
    }

    #[ORM\PrePersist]
    public function onPrePersist()
    {
        $this->setCreatedAt(new DateTimeImmutable());
        $this->setUpdatedAt(new DateTimeImmutable());
    }


    #[ORM\PreUpdate]
    public function onPreUpdate()
    {
        $this->setUpdatedAt(new DateTimeImmutable());
    }
}
