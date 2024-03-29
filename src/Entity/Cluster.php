<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\ClusterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: ClusterRepository::class)]
#[ApiResource]
class Cluster
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['show_company', "list_company"])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['show_company', "list_company"])]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['show_company', "list_company"])]
    private $auth_code;

    #[ORM\OneToMany(targetEntity: Company::class, mappedBy: 'cluster')]
    private $company;

    public function __construct()
    {
        $this->company = new ArrayCollection();
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

    public function getAuthCode(): ?string
    {
        return $this->auth_code;
    }

    public function setAuthCode(string $auth_code): self
    {
        $this->auth_code = $auth_code;

        return $this;
    }

    /**
     * @return Collection<int, Company>
     */
    public function getCompany(): Collection
    {
        return $this->company;
    }

    public function addCompany(Company $company): self
    {
        if (!$this->company->contains($company)) {
            $this->company[] = $company;
            $company->setCluster($this);
        }

        return $this;
    }

    public function removeCompany(Company $company): self
    {
        if ($this->company->removeElement($company)) {
            // set the owning side to null (unless already changed)
            if ($company->getCluster() === $this) {
                $company->setCluster(null);
            }
        }

        return $this;
    }
}
