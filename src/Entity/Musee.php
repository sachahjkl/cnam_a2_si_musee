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
    private ?float $longitude = null;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private ?float $latitude = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $abstract = null;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $thumbnailUri = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $website = null;


    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $wikilink = null;

    /**
     * @ORM\OneToOne(targetEntity=Directeur::class, mappedBy="musee")
     */
    private ?Directeur $directeur;

    /**
     * @ORM\ManyToOne(targetEntity=Emplacement::class, inversedBy="musees")
     */
    private ?Emplacement $location;

    public function __construct()
    {
        $this->directeur = null;
        $this->location = null;
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


    public function getThumbnailUri(): ?string
    {
        return $this->thumbnailUri;
    }

    public function setThumbnailUri(?string $thumbnailUri): self
    {
        $this->thumbnailUri = $thumbnailUri;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): self
    {
        $this->website = $website;

        return $this;
    }


    public function getWikilink(): ?string
    {
        return $this->wikilink;
    }

    public function setWikilink(?string $wikilink): self
    {
        $this->wikilink = $wikilink;

        return $this;
    }

    public function getDirecteur(): ?Directeur
    {
        return $this->directeur;
    }

    public function setDirecteur(?Directeur $directeur): self
    {
        // unset the owning side of the relation if necessary
        if ($directeur === null && $this->directeur !== null) {
            $this->directeur->setMusee(null);
        }

        // set the owning side of the relation if necessary
        if ($directeur !== null && $directeur->getMusee() !== $this) {
            $directeur->setMusee($this);
        }

        $this->directeur = $directeur;

        return $this;
    }

    public function getLocation(): ?Emplacement
    {
        return $this->location;
    }

    public function setLocation(?Emplacement $location): self
    {
        $this->location = $location;

        return $this;
    }
}
