<?php

namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Entity(repositoryClass=VilleRepository::class)
 */
class Ville
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
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $longitude;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $latitude;

    /**
     * @ORM\ManyToOne(targetEntity=Pays::class, inversedBy="villes")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Pays $pays;

    /**
     * @ORM\OneToMany(targetEntity=Musee::class, mappedBy="ville")
     */
    private ArrayCollection $musees;

    #[Pure] public function __construct()
    {
        $this->musees = new ArrayCollection();
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

    public function getPays(): ?Pays
    {
        return $this->pays;
    }

    public function setPays(?Pays $pays): self
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * @return Collection|Musee[]
     */
    public function getMusees(): Collection
    {
        return $this->musees;
    }

    public function addMusee(Musee $musee): self
    {
        if (!$this->musees->contains($musee)) {
            $this->musees[] = $musee;
            $musee->setVille($this);
        }

        return $this;
    }

    public function removeMusee(Musee $musee): self
    {
        if ($this->musees->removeElement($musee)) {
            // set the owning side to null (unless already changed)
            if ($musee->getVille() === $this) {
                $musee->setVille(null);
            }
        }

        return $this;
    }
}
