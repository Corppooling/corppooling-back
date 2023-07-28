<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\DepartmentRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;

#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: DepartmentRepository::class)]
#[ApiResource]
#[Post(security: "is_granted('ROLE_USER')")]
#[Delete(security: "is_granted('ROLE_USER')")]
#[Get]
#[Put(security: "is_granted('ROLE_USER')")]
#[GetCollection]
#[ApiFilter(SearchFilter::class, properties: ['company.id' => 'exact'])]
class Department
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['show_department', 'list_department'])]

    private $id;

    #[Groups(['show_timestamps'])]
    #[ORM\Column(type: 'datetime_immutable')]
    private $updated_at;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['show_timestamps'])]
    private $created_at;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['show_department', 'list_department'])]
    private $name;

    #[ORM\OneToMany(mappedBy: 'department', targetEntity: User::class)]
    private $users;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'departments')]
    #[Groups(['show_department', 'list_department'])]
    private $company;

    public function __construct()
    {
        $this->users = new ArrayCollection();
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

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setDepartment($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getDepartment() === $this) {
                $user->setDepartment(null);
            }
        }

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

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }
}
