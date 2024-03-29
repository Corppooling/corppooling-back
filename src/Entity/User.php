<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(normalizationContext: ['groups' => ['show_user', 'list_trip', 'list_department', 'list_company', 'show_timestamps'], AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true])]
#[ApiFilter(SearchFilter::class, properties: ['company.id' => 'exact'])]
#[ApiResource]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['show_user', 'list_user'])]
    private $id;

    #[ORM\Column(type: 'string', unique: true, length: 180)]
    #[Groups(['show_user', 'list_user'])]
    private $email;

    #[ORM\Column(type: 'json')]
    #[Groups(['show_user', 'list_user'])]
    private $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\Column(type: 'string', length: 50)]
    #[Groups(['show_user', 'list_user'])]
    private $firstname;

    #[ORM\Column(type: 'string', length: 50)]
    #[Groups(['show_user', 'list_user'])]
    private $lastname;

    #[ORM\OneToMany(targetEntity: Trip::class, mappedBy: 'announcer')]
    #[Groups(['show_user', 'list_user'])]
    private $trips;

    #[ORM\ManyToMany(targetEntity: Trip::class, mappedBy: 'members')]
    #[Groups(['show_user'])]
    #[MaxDepth(2)]
    private $reservations;


    #[ORM\ManyToOne(targetEntity: Department::class, inversedBy: 'users')]
    #[Groups(['show_user', 'list_user'])]
    private $department;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['show_user', 'list_user'])]
    private $company;

    #[ORM\Column(type: 'string', length: 20)]
    #[Groups(['show_user', 'list_user'])]
    private $phone;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['show_user', 'list_user'])]
    private $profile_image;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['show_user', 'list_user'])]
    private $presentation;

    #[Groups(['show_timestamps'])]
    #[ORM\Column(type: 'datetime_immutable')]
    private $updated_at;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['show_timestamps'])]
    private $created_at;

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

    public function __construct()
    {
        $this->trips = new ArrayCollection();
        $this->reservations = new ArrayCollection();
    }

    public function getAll(): ?object
    {
        $user = new \stdClass();
        $user->email = $this->email;
        $user->firstname = $this->firstname;
        $user->lastname = $this->lastname;
        return $user;
    }

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
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
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
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
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

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
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

    /**
     * @return Collection<int, Trip>
     */
    public function getTrips(): Collection
    {
        return $this->trips;
    }

    public function addTrip(Trip $trip): self
    {
        if (!$this->trips->contains($trip)) {
            $this->trips[] = $trip;
            $trip->setAnnouncer($this);
        }

        return $this;
    }

    public function removeTrip(Trip $trip): self
    {
        if ($this->trips->removeElement($trip)) {
            // set the owning side to null (unless already changed)
            if ($trip->getAnnouncer() === $this) {
                $trip->setAnnouncer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Trip>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Trip $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->addMember($this);
        }

        return $this;
    }

    public function removeReservation(Trip $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            $reservation->removeMember($this);
        }

        return $this;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): self
    {
        $this->department = $department;

        return $this;
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getProfileImage(): ?string
    {
        return $this->profile_image;
    }

    public function setProfileImage(?string $profile_image): self
    {
        $this->profile_image = $profile_image;

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
