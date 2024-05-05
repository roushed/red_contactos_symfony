<?php

namespace App\Entity;

use App\Repository\PerfilAficionesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PerfilAficionesRepository::class)]
class PerfilAficiones
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'perfilAficiones')]
    private ?Perfiles $perfil = null;

    #[ORM\ManyToOne(inversedBy: 'perfilAficiones')]
    private ?Aficiones $aficion = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPerfil(): ?Perfiles
    {
        return $this->perfil;
    }

    public function setPerfil(?Perfiles $perfil): static
    {
        $this->perfil = $perfil;

        return $this;
    }

    public function getAficion(): ?Aficiones
    {
        return $this->aficion;
    }

    public function setAficion(?Aficiones $aficion): static
    {
        $this->aficion = $aficion;

        return $this;
    }
}
