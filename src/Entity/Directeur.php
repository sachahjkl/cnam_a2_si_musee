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
     * @ORM\Column(type="string", length=255)
     */
    private ?string $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $prenom;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\OneToOne(targetEntity=Musee::class, inversedBy="directeur")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Musee $musee;


    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): ?string
    {
        $this->id = $id;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getMusee(): ?Musee
    {
        return $this->musee;
    }

    public function setMusee(Musee $musee): self
    {
        $this->musee = $musee;

        return $this;
    }
}
