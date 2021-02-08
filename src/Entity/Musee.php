<?php

namespace App\Entity;

use App\Repository\MuseeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MuseeRepository::class)
 */
class Musee
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private ?string $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $name;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $longitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $latitude;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $abstract;

    /**
     * @ORM\OneToOne(targetEntity=Directeur::class, mappedBy="musee")
     */
    private ?Directeur $directeur;

    /**
     * @ORM\ManyToOne(targetEntity=Ville::class, inversedBy="musees")
     */
    private ?Ville $ville;


    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): ?self
    {
        $this->id = $id;

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

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): self
    {
        $this->latitude = $latitude;

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

    public function getDirecteur(): ?Directeur
    {
        return $this->directeur;
    }

    public function setDirecteur(Directeur $directeur): self
    {
        // set the owning side of the relation if necessary
        if ($directeur->getMusee() !== $this) {
            $directeur->setMusee($this);
        }

        $this->directeur = $directeur;

        return $this;
    }

    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    public function setVille(?Ville $ville): self
    {
        $this->ville = $ville;

        return $this;
    }
}
