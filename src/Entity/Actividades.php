<?php

namespace App\Entity;

use App\Repository\ActividadesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ActividadesRepository::class)]
class Actividades
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nombre = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $hora = null;

    #[ORM\Column(length: 60)]
    private ?string $direccion = null;

    #[ORM\Column(length: 30)]
    private ?string $municipio = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $img = null;

    #[ORM\OneToMany(mappedBy: 'id_actividad', targetEntity: ActividadesUsuarios::class)]
    private Collection $actividadesUsuarios;

    #[ORM\OneToMany(mappedBy: 'actividad', targetEntity: Comentariosa::class, cascade: ['remove'])]
    private Collection $comentariosas;

    #[ORM\Column]
    private ?int $npersonas = null;

    public function __construct()
    {
        $this->actividadesUsuarios = new ArrayCollection();
        $this->comentariosas = new ArrayCollection();
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

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): static
    {
        $this->descripcion = $descripcion;

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

    public function getHora(): ?\DateTimeInterface
    {
        return $this->hora;
    }

    public function setHora(?\DateTimeInterface $hora): static
    {
        $this->hora = $hora;

        return $this;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(string $direccion): static
    {
        $this->direccion = $direccion;

        return $this;
    }

    public function getMunicipio(): ?string
    {
        return $this->municipio;
    }

    public function setMunicipio(string $municipio): static
    {
        $this->municipio = $municipio;

        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(?string $img): static
    {
        $this->img = $img;

        return $this;
    }

    /**
     * @return Collection<int, ActividadesUsuarios>
     */
    public function getActividadesUsuarios(): Collection
    {
        return $this->actividadesUsuarios;
    }

    public function addActividadesUsuario(ActividadesUsuarios $actividadesUsuario): static
    {
        if (!$this->actividadesUsuarios->contains($actividadesUsuario)) {
            $this->actividadesUsuarios->add($actividadesUsuario);
            $actividadesUsuario->setIdActividad($this);
        }

        return $this;
    }

    public function removeActividadesUsuario(ActividadesUsuarios $actividadesUsuario): static
    {
        if ($this->actividadesUsuarios->removeElement($actividadesUsuario)) {
            
            if ($actividadesUsuario->getIdActividad() === $this) {
                $actividadesUsuario->setIdActividad(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comentariosa>
     */
    public function getComentariosas(): Collection
    {
        return $this->comentariosas;
    }

    public function addComentariosa(Comentariosa $comentariosa): static
    {
        if (!$this->comentariosas->contains($comentariosa)) {
            $this->comentariosas->add($comentariosa);
            $comentariosa->setActividad($this);
        }

        return $this;
    }

    public function removeComentariosa(Comentariosa $comentariosa): static
    {
        if ($this->comentariosas->removeElement($comentariosa)) {
           
            if ($comentariosa->getActividad() === $this) {
                $comentariosa->setActividad(null);
            }
        }

        return $this;
    }

    public function getNpersonas(): ?int
    {
        return $this->npersonas;
    }

    public function setNpersonas(int $npersonas): static
    {
        $this->npersonas = $npersonas;

        return $this;
    }


    
}
