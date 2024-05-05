<?php

namespace App\Entity;

use App\Repository\UsuariosRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsuariosRepository::class)]
class Usuarios
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nick = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\OneToMany(mappedBy: 'nick', targetEntity: ActividadesUsuarios::class)]
    private Collection $actividadesUsuarios;

    #[ORM\OneToMany(mappedBy: 'nick', targetEntity: Perfiles::class)]
    private Collection $perfiles;

    #[ORM\OneToMany(mappedBy: 'nickrecibo', targetEntity: Mensajes::class)]
    private Collection $mensajes;

    #[ORM\OneToMany(mappedBy: 'nick', targetEntity: Posts::class)]
    private Collection $posts;
    
    #[ORM\OneToOne(targetEntity: Perfiles::class, mappedBy: 'nick', cascade: ['persist', 'remove'])]
    private ?Perfiles $perfil = null;

    #[ORM\Column]
    private ?bool $online = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $fecha = null;

    public function __construct()
    {
        $this->actividadesUsuarios = new ArrayCollection();
        $this->perfiles = new ArrayCollection();
        $this->mensajes = new ArrayCollection();
        $this->posts = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNick(): ?string
    {
        return $this->nick;
    }

    public function setNick(string $nick): static
    {
        $this->nick = $nick;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

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
            $actividadesUsuario->setNick($this);
        }

        return $this;
    }

    public function removeActividadesUsuario(ActividadesUsuarios $actividadesUsuario): static
    {
        if ($this->actividadesUsuarios->removeElement($actividadesUsuario)) {
            
            if ($actividadesUsuario->getNick() === $this) {
                $actividadesUsuario->setNick(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Perfiles>
     */
    public function getPerfiles(): Collection
    {
        return $this->perfiles;
    }

    public function addPerfile(Perfiles $perfile): static
    {
        if (!$this->perfiles->contains($perfile)) {
            $this->perfiles->add($perfile);
            $perfile->setNick($this);
        }

        return $this;
    }

    public function removePerfile(Perfiles $perfile): static
    {
        if ($this->perfiles->removeElement($perfile)) {
           
            if ($perfile->getNick() === $this) {
                $perfile->setNick(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Mensajes>
     */
    public function getMensajes(): Collection
    {
        return $this->mensajes;
    }

    public function addMensaje(Mensajes $mensaje): static
    {
        if (!$this->mensajes->contains($mensaje)) {
            $this->mensajes->add($mensaje);
            $mensaje->setNickrecibo($this);
        }

        return $this;
    }

    public function removeMensaje(Mensajes $mensaje): static
    {
        if ($this->mensajes->removeElement($mensaje)) {
            
            if ($mensaje->getNickrecibo() === $this) {
                $mensaje->setNickrecibo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Posts>
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Posts $post): static
    {
        if (!$this->posts->contains($post)) {
            $this->posts->add($post);
            $post->setNick($this);
        }

        return $this;
    }

    public function removePost(Posts $post): static
    {
        if ($this->posts->removeElement($post)) {
            
            if ($post->getNick() === $this) {
                $post->setNick(null);
            }
        }

        return $this;
    }


    public function getPerfil(): ?Perfiles
    {
        return $this->perfil;
    }

    /**
     * @return  self
     */ 
    public function setPerfil(?Perfiles $perfil): self
    {
        $this->perfil = $perfil;

        return $this;
    }

    public function isOnline(): ?bool
    {
        return $this->online;
    }

    public function setOnline(bool $online): static
    {
        $this->online = $online;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(?\DateTimeInterface $fecha): static
    {
        $this->fecha = $fecha;

        return $this;
    }


}
