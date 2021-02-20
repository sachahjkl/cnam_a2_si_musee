<?php

namespace App\Entity;

use App\Repository\DirecteurRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DirecteurRepository::class)
 */
class Directeur
{

    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private ?string $id;


    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $abstract = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $name = null;

    /**
     * @ORM\OneToOne(targetEntity=Musee::class, inversedBy="directeur")
     */
    private ?Musee $musee;

    public function __construct()
    {
        $this->musee = null;
    }


    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): ?self
    {
        $this->id = $id;

        return $this;
    }

    public function getAbstract(): ?string
    {
        return $this->abstract;
    }

    public function setAbstract(?string $abstract): self
    {
        $this->abstract = $abstract;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getMusee(): ?Musee
    {
        return $this->musee;
    }

    public function setMusee(?Musee $musee): self
    {
        $this->musee = $musee;

        return $this;
    }

}
