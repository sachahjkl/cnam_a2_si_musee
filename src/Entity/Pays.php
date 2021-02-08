<?php

namespace App\Entity;

use App\Repository\PaysRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Entity(repositoryClass=PaysRepository::class)
 */
class Pays
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private ?string $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $nom;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $latitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $longitude;

    /**
     * @ORM\OneToMany(targetEntity=Ville::class, mappedBy="pays")
     */
    private ArrayCollection $villes;

    #[Pure] public function __construct()
    {
        $this->villes = new ArrayCollection();
    }

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

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @return Collection|Ville[]
     */
    public function getVilles(): Collection
    {
        return $this->villes;
    }

    public function addVille(Ville $ville): self
    {
        if (!$this->villes->contains($ville)) {
            $this->villes[] = $ville;
            $ville->setPays($this);
        }

        return $this;
    }

    public function removeVille(Ville $ville): self
    {
        if ($this->villes->removeElement($ville)) {
            // set the owning side to null (unless already changed)
            if ($ville->getPays() === $this) {
                $ville->setPays(null);
            }
        }

        return $this;
    }
}
