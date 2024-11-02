<?php

namespace App\Entity;

use App\Repository\CourseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CourseRepository::class)]
#[ORM\Table(name: 'Courses')]
class Course
{
    #[ORM\Id]   
    #[ORM\Column(name: 'CourseID', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[Groups(['student_read'])]
    private ?int $CourseID = null;

    #[ORM\Column(name: 'CourseName', length: 100, nullable: true)]
    #[Groups(['student_read'])]
    private ?string $CourseName = null;

    #[ORM\Column(name: 'CourseCode', length: 20, nullable: true)]
    #[Groups(['student_read'])]
    private ?string $CourseCode = null;

    #[ORM\Column(name: 'Description', type: Types::TEXT, nullable: true)]
    #[Groups(['student_read'])]
    private ?string $Description = null;

    #[ORM\Column(name: 'Credits', nullable: true)]
    #[Groups(['student_read'])]
    private ?int $Credits = null;

    #[ORM\Column(name: 'Department', length: 50, nullable: true)]
    #[Groups(['student_read'])]
    private ?string $Department = null;

    #[ORM\ManyToMany(targetEntity: Student::class, mappedBy: 'courses')]
    #[Groups(['student_read'])]
    private Collection $students;

    #[ORM\OneToOne(targetEntity: Schedule::class, mappedBy: 'course')]
    #[Groups(['student_read'])]
    private ?Schedule $schedule = null;

    #[ORM\ManyToOne(targetEntity: Professor::class, inversedBy: 'courses')]
    #[ORM\JoinColumn(name: 'ProfessorID', referencedColumnName: 'ProfessorID')] 
    #[Groups(['student_read'])]
    private ?Professor $professor = null;

    public function __construct()
    {
        $this->students = new ArrayCollection();
    }

    // Getters and Setters...
    public function getProfessor(): ?Professor
    {
        return $this->professor;
    }
    public function setProfessor(?Professor $professor): static
{
    $this->professor = $professor;
    return $this;
}

    public function getCourseID(): ?int 
    {
        return $this->CourseID;
    }   
    public function getCourseName(): ?string
    {
        return $this->CourseName;
    }

    public function setCourseName(?string $CourseName): static
    {
        $this->CourseName = $CourseName;
        return $this;
    }

    public function getCourseCode(): ?string
    {
        return $this->CourseCode;
    }

    public function setCourseCode(?string $CourseCode): static
    {
        $this->CourseCode = $CourseCode;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(?string $Description): static
    {
        $this->Description = $Description;
        return $this;
    }

    public function getCredits(): ?int
    {
        return $this->Credits;
    }

    public function setCredits(?int $Credits): static
    {
        $this->Credits = $Credits;
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

    public function getSchedule(): ?Schedule
    {
        return $this->schedule;
    }
    public function setSchedule(?Schedule $schedule): static
    {
    if ($schedule === null && $this->schedule !== null) {
        $this->schedule->setCourse(null);
    }
    if ($schedule !== null && $schedule->getCourse() !== $this) {
        $schedule->setCourse($this);
    }
    $this->schedule = $schedule;
    return $this;
    }
  

    public function getStudents(): Collection
    {
        return $this->students;
    }

    public function addStudent(Student $student): static
    {
        if (!$this->students->contains($student)) {
            $this->students->add($student);
            $student->addCourse($this);
        }
        return $this;
    }

    public function removeStudent(Student $student): static
    {
        if ($this->students->removeElement($student)) {
            $student->removeCourse($this);
        }
        return $this;
    }

}
