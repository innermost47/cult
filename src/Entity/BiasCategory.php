<?php

namespace App\Entity;

use App\Repository\BiasCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BiasCategoryRepository::class)
 */
class BiasCategory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\OneToMany(targetEntity=Bias::class, mappedBy="biasCategory", orphanRemoval=true)
     */
    private $bias;

    public function __construct()
    {
        $this->bias = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, Bias>
     */
    public function getBias(): Collection
    {
        return $this->bias;
    }

    public function addBia(Bias $bia): self
    {
        if (!$this->bias->contains($bia)) {
            $this->bias[] = $bia;
            $bia->setBiasCategory($this);
        }

        return $this;
    }

    public function removeBia(Bias $bia): self
    {
        if ($this->bias->removeElement($bia)) {
            // set the owning side to null (unless already changed)
            if ($bia->getBiasCategory() === $this) {
                $bia->setBiasCategory(null);
            }
        }

        return $this;
    }
}
