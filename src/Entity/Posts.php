<?php

namespace App\Entity;

use App\Repository\PostsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostsRepository::class)]
class Posts
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 60, nullable: true)]
    private ?string $subject = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $texto = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $imagen = null;

    #[ORM\Column(nullable: true)]
    private ?bool $creador = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $fecha = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $precio = null;


    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorias $categoria = null;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Comentariosp::class)]
    private Collection $comentariosps;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuarios $nick = null;

    public function __construct()
    {
        $this->comentariosps = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getTexto(): ?string
    {
        return $this->texto;
    }

    public function setTexto(?string $texto): static
    {
        $this->texto = $texto;

        return $this;
    }

    public function getImagen(): ?string
    {
        return $this->imagen;
    }

    public function setImagen(?string $imagen): static
    {
        $this->imagen = $imagen;

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

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(?\DateTimeInterface $fecha): static
    {
        $this->fecha = $fecha;

        return $this;
    }


    public function getCategoria(): ?Categorias
    {
        return $this->categoria;
    }

    public function setCategoria(?Categorias $categoria): static
    {
        $this->categoria = $categoria;

        return $this;
    }

    /**
     * @return Collection<int, Comentariosp>
     */
    public function getComentariosps(): Collection
    {
        return $this->comentariosps;
    }

    public function addComentariosp(Comentariosp $comentariosp): static
    {
        if (!$this->comentariosps->contains($comentariosp)) {
            $this->comentariosps->add($comentariosp);
            $comentariosp->setPost($this);
        }

        return $this;
    }

    public function removeComentariosp(Comentariosp $comentariosp): static
    {
        if ($this->comentariosps->removeElement($comentariosp)) {
          
            if ($comentariosp->getPost() === $this) {
                $comentariosp->setPost(null);
            }
        }

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

    public function getPrecio(): ?string
    {
        return $this->precio;
    }

    public function setPrecio(?string $precio): static
    {
        $this->precio = $precio;

        return $this;
    }

    public function getCantidadComentarios(): int
    {
        return $this->comentariosps->count();
    }


}

