<?php

namespace App\Entity;

use App\Repository\MensajesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MensajesRepository::class)]
class Mensajes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'mensajes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuarios $nickrecibo = null;

    #[ORM\ManyToOne(inversedBy: 'mensajes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuarios $nickenvia = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $texto = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column]
    private ?bool $leido = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNickrecibo(): ?Usuarios
    {
        return $this->nickrecibo;
    }

    public function setNickrecibo(?Usuarios $nickrecibo): static
    {
        $this->nickrecibo = $nickrecibo;

        return $this;
    }

    public function getNickenvia(): ?Usuarios
    {
        return $this->nickenvia;
    }

    public function setNickenvia(?Usuarios $nickenvia): static
    {
        $this->nickenvia = $nickenvia;

        return $this;
    }

    public function getTexto(): ?string
    {
        return $this->texto;
    }

    public function setTexto(string $texto): static
    {
        $this->texto = $texto;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): static
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function isLeido(): ?bool
    {
        return $this->leido;
    }

    public function setLeido(bool $leido): static
    {
        $this->leido = $leido;

        return $this;
    }
}
