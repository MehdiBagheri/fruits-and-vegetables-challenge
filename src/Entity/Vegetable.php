<?php

namespace App\Entity;

use App\Repository\VegetableRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'vegetables')]
#[ORM\Entity(repositoryClass: VegetableRepository::class)]
final class Vegetable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\NotNull]
    private int $id;

    #[ORM\Column(type: Types::STRING)]
    #[Assert\NotNull]
    private string $name;

    #[ORM\Column(name: 'quantity_in_gram', type: Types::INTEGER)]
    #[Assert\NotNull]
    private int $quantity;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }
}
