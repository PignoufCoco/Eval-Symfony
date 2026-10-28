<?php

namespace App\Entity;

use App\Repository\OeuvreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OeuvreRepository::class)]
class Oeuvre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $titre = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateCreation = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateDestruction = null;

    /**
     * @var Collection<int, Artiste>
     */
    #[ORM\ManyToMany(targetEntity: Artiste::class, mappedBy: 'oeuvres')]
    private Collection $artistes;

    #[ORM\ManyToOne(inversedBy: 'oeuvres')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Lieu $lieux = null;

    /**
     * @var Collection<int, Technique>
     */
    #[ORM\ManyToMany(targetEntity: Technique::class, inversedBy: 'oeuvres')]
    private Collection $technique;

    /**
     * @var Collection<int, Photo>
     */
    #[ORM\OneToMany(targetEntity: Photo::class, mappedBy: 'oeuvre')]
    private Collection $photos;

    #[ORM\OneToOne(targetEntity: self::class, inversedBy: 'recouvertePar', cascade: ['persist', 'remove'])]
    private ?self $oeuvreRecouverte = null;

    #[ORM\OneToOne(targetEntity: self::class, mappedBy: 'oeuvreRecouverte', cascade: ['persist', 'remove'])]
    private ?self $recouvertePar = null;

    public function __construct()
    {
        $this->artistes = new ArrayCollection();
        $this->technique = new ArrayCollection();
        $this->photos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDateCreation(): ?\DateTime
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTime $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getDateDestruction(): ?\DateTime
    {
        return $this->dateDestruction;
    }

    public function setDateDestruction(\DateTime $dateDestruction): static
    {
        $this->dateDestruction = $dateDestruction;

        return $this;
    }

    /**
     * @return Collection<int, Artiste>
     */
    public function getArtistes(): Collection
    {
        return $this->artistes;
    }

    public function addArtiste(Artiste $artiste): static
    {
        if (!$this->artistes->contains($artiste)) {
            $this->artistes->add($artiste);
            $artiste->addOeuvre($this);
        }

        return $this;
    }

    public function removeArtiste(Artiste $artiste): static
    {
        if ($this->artistes->removeElement($artiste)) {
            $artiste->removeOeuvre($this);
        }

        return $this;
    }

    public function getLieux(): ?Lieu
    {
        return $this->lieux;
    }

    public function setLieux(?Lieu $lieux): static
    {
        $this->lieux = $lieux;

        return $this;
    }

    /**
     * @return Collection<int, Technique>
     */
    public function getTechnique(): Collection
    {
        return $this->technique;
    }

    public function addTechnique(Technique $technique): static
    {
        if (!$this->technique->contains($technique)) {
            $this->technique->add($technique);
        }

        return $this;
    }

    public function removeTechnique(Technique $technique): static
    {
        $this->technique->removeElement($technique);

        return $this;
    }

    /**
     * @return Collection<int, Photo>
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(Photo $photo): static
    {
        if (!$this->photos->contains($photo)) {
            $this->photos->add($photo);
            $photo->setOeuvre($this);
        }

        return $this;
    }

    public function removePhoto(Photo $photo): static
    {
        if ($this->photos->removeElement($photo)) {
            // set the owning side to null (unless already changed)
            if ($photo->getOeuvre() === $this) {
                $photo->setOeuvre(null);
            }
        }

        return $this;
    }

    public function getOeuvreRecouverte(): ?self
    {
        return $this->oeuvreRecouverte;
    }

    public function setOeuvreRecouverte(?self $oeuvreRecouverte): static
    {
        $this->oeuvreRecouverte = $oeuvreRecouverte;

        return $this;
    }

    public function getRecouvertePar(): ?self
    {
        return $this->recouvertePar;
    }

    public function setRecouvertePar(?self $recouvertePar): static
    {
        // unset the owning side of the relation if necessary
        if ($recouvertePar === null && $this->recouvertePar !== null) {
            $this->recouvertePar->setOeuvreRecouverte(null);
        }

        // set the owning side of the relation if necessary
        if ($recouvertePar !== null && $recouvertePar->getOeuvreRecouverte() !== $this) {
            $recouvertePar->setOeuvreRecouverte($this);
        }

        $this->recouvertePar = $recouvertePar;

        return $this;
    }
}
