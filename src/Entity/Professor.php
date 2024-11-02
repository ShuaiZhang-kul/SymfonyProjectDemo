<?php

namespace App\Entity;

use App\Repository\ProfessorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProfessorRepository::class)]
#[ORM\Table(name: 'Professors')]
class Professor
{
    #[ORM\Id]
    #[ORM\Column(name: 'ProfessorID', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[Groups(['student_read'])]
    private ?int $ProfessorID = null;

    #[ORM\Column(name: 'FirstName', length: 50, nullable: true)]
    #[Groups(['student_read'])]
    private ?string $FirstName = null;

    #[ORM\Column(name: 'LastName', length: 50, nullable: true)]
    #[Groups(['student_read'])]
    private ?string $LastName = null;

    #[ORM\Column(name: 'Email', length: 100, nullable: true)]
    #[Groups(['student_read'])]
    private ?string $Email = null;

    #[ORM\Column(name: 'Department', length: 50, nullable: true)]
    #[Groups(['student_read'])]
    private ?string $Department = null;

    #[ORM\Column(name: 'PasswordHash', length: 256, nullable: true)]
    #[Groups(['professor_read'])]
    private ?string $PasswordHash = null;

    #[ORM\OneToMany(targetEntity: Course::class, mappedBy: 'professor')]
    #[Groups(['professor_read'])]
    private Collection $courses;
    
    public function __construct()
    {
        $this->courses = new ArrayCollection();
    }
    
    public function getCourses(): Collection
    {
        return $this->courses;
    }
    
    public function addCourse(Course $course): static
    {
        if (!$this->courses->contains($course)) {
            $this->courses->add($course);
            $course->setProfessor($this);
        }
        return $this;
    }
    
    public function removeCourse(Course $course): static
    {
        if ($this->courses->removeElement($course)) {
            if ($course->getProfessor() === $this) {
                $course->setProfessor(null);
            }
        }
        return $this;
    }

    public function getProfessorID(): ?int
    {
        return $this->ProfessorID;
    }

    public function getFirstName(): ?string
    {
        return $this->FirstName;
    }

    public function setFirstName(?string $FirstName): static
    {
        $this->FirstName = $FirstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->LastName;
    }

    public function setLastName(?string $LastName): static
    {
        $this->LastName = $LastName;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(?string $Email): static
    {
        $this->Email = $Email;
        return $this;
    }

    public function getDepartment(): ?string
    {
        return $this->Department;
    }

    public function setDepartment(?string $Department): static
    {
        $this->Department = $Department;
        return $this;
    }

    public function getPasswordHash(): ?string
    {
        return $this->PasswordHash;
    }

    public function setPasswordHash(?string $PasswordHash): static
    {
        $this->PasswordHash = $PasswordHash;
        return $this;
    }
}