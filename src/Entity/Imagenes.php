<?php

namespace App\Entity;
use App\Entity\Posts;
use App\Repository\ImagenesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImagenesRepository::class)]
class Imagenes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nombre = null;

 
    #[ORM\ManyToOne(inversedBy: 'imagenes')]
    private ?Posts $post = null;

    public function __construct()
    {
        $this->post =null;
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

    
    public function getPost(): Collection
    {
        return $this->post;
    }

    public function addPost(Posts $post): static
    {
        if (!$this->post->contains($post)) {
            $this->post->add($post);
            $post->setImagenes($this);
        }

        return $this;
    }

    public function removePost(Posts $post): static
    {
        if ($this->post->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getImagenes() === $this) {
                $post->setImagenes(null);
            }
        }

        return $this;
    }

    public function setPost(?Posts $post): static
    {
        $this->post = $post;

        return $this;
    }
}
