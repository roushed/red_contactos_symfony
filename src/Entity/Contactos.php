<?php
namespace App\Entity;

use App\Repository\ContactosRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ContactosRepository::class)]
class Contactos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Usuarios::class, inversedBy: 'contactos')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuarios $usuario = null;

    #[ORM\ManyToOne(targetEntity: Usuarios::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuarios $contacto = null;

    #[ORM\Column(type: 'boolean')]
    private bool $bloqueado = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsuario(): ?Usuarios
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuarios $usuario): self
    {
        $this->usuario = $usuario;
        return $this;
    }

    public function getContacto(): ?Usuarios
    {
        return $this->contacto;
    }

    public function setContacto(?Usuarios $contacto): self
    {
        $this->contacto = $contacto;
        return $this;
    }

    public function isBloqueado(): bool
    {
        return $this->bloqueado;
    }

    public function setBloqueado(bool $bloqueado): self
    {
        $this->bloqueado = $bloqueado;
        return $this;
    }
}
