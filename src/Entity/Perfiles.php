<?php

namespace App\Entity;

use App\Repository\PerfilesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: PerfilesRepository::class)]
class Perfiles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'perfiles')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Usuarios $nick = null;

    #[ORM\Column(length: 40, nullable: true)]
    private ?string $nombre = null;

    #[ORM\Column(length: 60, nullable: true)]
    private ?string $apellidos = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $edad = null;

    #[ORM\Column(length: 1, nullable: true)]
    private ?string $genero = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $ciudad = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $foto = null;

    #[ORM\OneToMany(mappedBy: 'perfil', targetEntity: PerfilAficiones::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $perfilAficiones;

    #[ORM\Column(length: 60)]
    private ?string $email = null;

    /**
     * @var Collection<int, Galerias>
     */
    #[ORM\OneToMany(mappedBy: 'perfil', targetEntity: Galerias::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $galerias;

    public function __construct()
    {
        $this->perfilAficiones = new ArrayCollection();
        $this->galerias = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(?string $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getApellidos(): ?string
    {
        return $this->apellidos;
    }

    public function setApellidos(?string $apellidos): static
    {
        $this->apellidos = $apellidos;

        return $this;
    }

    public function getEdad(): ?\DateTimeInterface 
    {
        return $this->edad;
    }
    
    public function setEdad(?\DateTimeInterface $edad): static 
    {
        $this->edad = $edad;
    
        return $this;
    }

    public function getGenero(): ?string
    {
        return $this->genero;
    }

    public function setGenero(?string $genero): static
    {
        $this->genero = $genero;

        return $this;
    }

    public function getCiudad(): ?string
    {
        return $this->ciudad;
    }

    public function setCiudad(?string $ciudad): static
    {
        $this->ciudad = $ciudad;

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

    public function getFoto(): ?string
    {
        return $this->foto;
    }

    public function setFoto(?string $foto): static
    {
        $this->foto = $foto;

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
            $perfilAficione->setPerfil($this);
        }

        return $this;
    }

    public function removePerfilAficione(PerfilAficiones $perfilAficione): static
    {
        if ($this->perfilAficiones->removeElement($perfilAficione)) {
            
            if ($perfilAficione->getPerfil() === $this) {
                $perfilAficione->setPerfil(null);
            }
        }

        return $this;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection<int, Galerias>
     */
    public function getGalerias(): Collection
    {
        return $this->galerias;
    }

    public function addGaleria(Galerias $galeria): static
    {
        if (!$this->galerias->contains($galeria)) {
            $this->galerias->add($galeria);
            $galeria->setPerfil($this);
        }

        return $this;
    }

    public function removeGaleria(Galerias $galeria): static
    {
        if ($this->galerias->removeElement($galeria)) {
            // set the owning side to null (unless already changed)
            if ($galeria->getPerfil() === $this) {
                $galeria->setPerfil(null);
            }
        }

        return $this;
    }
}
