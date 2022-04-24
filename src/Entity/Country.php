<?php

namespace App\Entity;

use App\Repository\CountryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
class Country
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 100)]
    private $name;

    #[ORM\OneToMany(mappedBy: 'country', targetEntity: GolfCourse::class)]
    private $golfCourses;

    public function __construct()
    {
        $this->golfCourses = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getName();
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

    /**
     * @return Collection<int, GolfCourse>
     */
    public function getGolfCourses(): Collection
    {
        return $this->golfCourses;
    }

    public function addGolfCourse(GolfCourse $golfCourse): self
    {
        if (!$this->golfCourses->contains($golfCourse)) {
            $this->golfCourses[] = $golfCourse;
            $golfCourse->setCountry($this);
        }

        return $this;
    }

    public function removeGolfCourse(GolfCourse $golfCourse): self
    {
        if ($this->golfCourses->removeElement($golfCourse)) {
            // set the owning side to null (unless already changed)
            if ($golfCourse->getCountry() === $this) {
                $golfCourse->setCountry(null);
            }
        }

        return $this;
    }
}
