<?php

namespace App\Entity;

use App\Repository\LikesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LikesRepository::class)]
class Likes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Posts::class, inversedBy: 'likes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Posts $post = null;

    #[ORM\ManyToOne(targetEntity: Usuarios::class, inversedBy: 'likes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Usuarios $nick = null;
    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPost(): ?Posts
    {
        return $this->post;
    }

    public function setPost(?Posts $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function getNick(): ?Usuarios
    {
        return $this->nick;
    }

    public function setNick(?Usuarios $nick): self
    {
        $this->nick = $nick;

        return $this;
    }

    public function addPost(Posts $post): self
    {
        $this->post = $post;

        return $this;
    }

    public function addNick(Usuarios $nick): self
    {
        $this->nick = $nick;

        return $this;
    }
}