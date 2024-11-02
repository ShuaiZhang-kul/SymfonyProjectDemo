<?php

namespace App\Entity;

use App\Repository\StudentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;  // 添加这行
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
#[ORM\Table(name: 'Students')]
class Student implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(name: 'StudentID', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[Groups(['student_read'])]
    private ?int $StudentID = null;

    #[ORM\Column(name: 'FirstName', length: 50, nullable: true)]
    #[Groups(['student_read'])]
    private ?string $FirstName = null;

    #[ORM\Column(name: 'LastName', length: 50, nullable: true)]
    #[Groups(['student_read'])]
    private ?string $LastName = null;

    #[ORM\Column(name: 'Email', length: 100, nullable: true)]
    #[Groups(['student_read'])]
    private ?string $Email = null;

    #[ORM\Column(name: 'DateOfBirth', type: Types::DATE_MUTABLE, nullable: true)]
    #[Groups(['student_read'])]
    private ?\DateTimeInterface $DateOfBirth = null;

    #[ORM\Column(name: 'EnrollmentYear', nullable: true)]
    #[Groups(['student_read'])]
    private ?int $EnrollmentYear = null;

    #[ORM\Column(name: 'PasswordHash', length: 256, nullable: true)]
    #[Groups(['student_private'])]
    private ?string $PasswordHash = null;
    
    #[ORM\ManyToMany(targetEntity: Course::class, inversedBy: 'students')]
    #[ORM\JoinTable(name: 'CourseSelections',
        joinColumns: [
            new ORM\JoinColumn(name: 'StudentID', referencedColumnName: 'StudentID')
        ],
        inverseJoinColumns: [
            new ORM\JoinColumn(name: 'CourseID', referencedColumnName: 'CourseID')
        ]
    )]
    #[Groups(['student_read'])]
    private Collection $courses;

    private array $roles = ['ROLE_STUDENT'];

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
            $course->addStudent($this);
        }
        return $this;
    }
    
    public function removeCourse(Course $course): static
    {
        if ($this->courses->removeElement($course)) {
            $course->removeStudent($this);
        }
        return $this;
    }
    
   
    public function getStudentID(): ?int
    {
        return $this->StudentID;
    }

    public function setStudentID(int $StudentID): static
    {
        $this->StudentID = $StudentID;
        return $this;
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

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->DateOfBirth;
    }

    public function setDateOfBirth(?\DateTimeInterface $DateOfBirth): static
    {
        $this->DateOfBirth = $DateOfBirth;
        return $this;
    }

    public function getEnrollmentYear(): ?int
    {
        return $this->EnrollmentYear;
    }

    public function setEnrollmentYear(?int $EnrollmentYear): static
    {
        $this->EnrollmentYear = $EnrollmentYear;
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

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function eraseCredentials(): void
    {
        // 如果存储了任何临时的、敏感的数据，在这里清除
    }

    public function getUserIdentifier(): string
    {
        return $this->Email;
    }

    public function getPassword(): string
    {
        return $this->PasswordHash;
    }

    public function setPassword(string $password): self
    {
        $this->PasswordHash = $password;
        return $this;
    }
}