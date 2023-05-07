<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\HasLifecycleCallbacks]
class BaseEntity
{
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
