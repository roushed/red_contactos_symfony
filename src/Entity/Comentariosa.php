<?php

namespace App\Entity;

use App\Repository\ComentariosaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ComentariosaRepository::class)]
class Comentariosa
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'comentariosas')]
    private ?Actividades $actividad = null;

    #[ORM\ManyToOne(inversedBy: 'comentariospa')]
    private ?Usuarios $nick = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column(length: 255)]
    private ?string $texto = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getActividad(): ?Actividades
    {
        return $this->actividad;
    }

    public function setActividad(?Actividades $actividad): static
    {
        $this->actividad = $actividad;

        return $this;
    }

    public function getNick(): ?Usuarios
    {
        return $this->nick;
    }

    public function setNick(?Usuarios $nick): static
    {
        $this->nick = $nick;

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

    public function getTexto(): ?string
    {
        return $this->texto;
    }

    public function setTexto(string $texto): static
    {
        $this->texto = $texto;

        return $this;
    }
}
