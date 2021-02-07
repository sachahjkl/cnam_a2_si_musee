<?php

namespace App\Entity;

use App\Repository\MuseeRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MuseeRepository::class)
 */
class Musee
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $nom;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $longitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $latitude;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private ?DateTimeInterface $date_construction;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $description;

    /**
     * @ORM\OneToOne(targetEntity=Directeur::class, mappedBy="musee")
     */
    private ?Directeur $directeur;

    /**
     * @ORM\ManyToOne(targetEntity=Ville::class, inversedBy="musees")
     */
    private ?Ville $ville;


    public function getId(): ?int
    {
        return $this->id;
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

    public function getDateConstruction(): ?DateTimeInterface
    {
        return $this->date_construction;
    }

    public function setDateConstruction(?DateTimeInterface $date_construction): self
    {
        $this->date_construction = $date_construction;

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
