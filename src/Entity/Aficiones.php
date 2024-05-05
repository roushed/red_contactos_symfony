<?php

namespace App\Entity;

use App\Repository\AficionesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AficionesRepository::class)]
class Aficiones
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 40)]
    private ?string $nombre = null;

    #[ORM\OneToMany(mappedBy: 'aficion', targetEntity: PerfilAficiones::class)]
    private Collection $perfilAficiones;

    public function __construct()
    {
        $this->perfilAficiones = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * @return Collection<int, PerfilAficiones>
     */
    public function getPerfilAficiones(): Collection
    {
        return $this->perfilAficiones;
    }

    public function addPerfilAficione(PerfilAficiones $perfilAficione): static
    {
        if (!$this->perfilAficiones->contains($perfilAficione)) {
            $this->perfilAficiones->add($perfilAficione);
            $perfilAficione->setAficion($this);
        }

        return $this;
    }

    public function removePerfilAficione(PerfilAficiones $perfilAficione): static
    {
        if ($this->perfilAficiones->removeElement($perfilAficione)) {
            
            if ($perfilAficione->getAficion() === $this) {
                $perfilAficione->setAficion(null);
            }
        }

        return $this;
    }
}
