<?php

namespace App\Entity;

use App\Repository\ActividadesUsuariosRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActividadesUsuariosRepository::class)]
class ActividadesUsuarios
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'actividadesUsuarios')]
    private ?Actividades $id_actividad = null;

    #[ORM\ManyToOne(inversedBy: 'actividadesUsuarios')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuarios $nick = null;

    #[ORM\Column(nullable: true)]
    private ?bool $creador = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdActividad(): ?Actividades
    {
        return $this->id_actividad;
    }

    public function setIdActividad(?Actividades $id_actividad): static
    {
        $this->id_actividad = $id_actividad;

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

    public function isCreador(): ?bool
    {
        return $this->creador;
    }

    public function setCreador(?bool $creador): static
    {
        $this->creador = $creador;

        return $this;
    }
}
