<?php

declare(strict_types=1);

namespace Light\App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

trait UuidIdentifierTrait
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'uuid', unique: true, nullable: false)]
    protected UuidInterface $uuid;

    public function getId(): UuidInterface
    {
        return $this->uuid;
    }

    public function setId(UuidInterface $id): static
    {
        $this->uuid = $id;

        return $this;
    }

    protected function initId(): void
    {
        $this->uuid = Uuid::uuid7();
    }
}
