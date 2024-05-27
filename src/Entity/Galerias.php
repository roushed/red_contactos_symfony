<?php

namespace App\Entity;

use App\Repository\GaleriasRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GaleriasRepository::class)]
class Galerias
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $nombre = null;

    #[ORM\ManyToOne(inversedBy: 'galerias')]
    private ?Perfiles $perfil = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
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
}
