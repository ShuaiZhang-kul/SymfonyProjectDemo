<?php

namespace App\Entity;

use App\Repository\ScheduleRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
#[ORM\Entity(repositoryClass: ScheduleRepository::class)]
#[ORM\Table(name: 'Schedules')]
class Schedule
{
    #[ORM\Id]
    #[ORM\Column(name: 'ScheduleID', type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[Groups(['student_read'])]
    private ?int $ScheduleID = null;

    #[ORM\OneToOne(targetEntity: Course::class, inversedBy: 'schedule')]
    #[ORM\JoinColumn(name: 'CourseID', referencedColumnName: 'CourseID')]
    #[Groups(['student_read'])]
    private ?Course $course = null;

    #[ORM\Column(name: 'DayOfWeek', length: 20, nullable: true)]
    #[Groups(['student_read'])]
    private ?string $DayOfWeek = null;

    #[ORM\Column(name: 'StartTime', type: Types::STRING, nullable: true)]
    #[Groups(['student_read'])]
    private ?string $StartTime = null;

    #[ORM\Column(name: 'EndTime', type: Types::STRING, nullable: true)]
    #[Groups(['student_read'])]
    private ?string $EndTime = null;

    #[ORM\Column(name: 'Location', length: 50, nullable: true)]
    #[Groups(['student_read'])]
    private ?string $Room = null;

    // Getters and Setters
    public function getScheduleID(): ?int
    {
        return $this->ScheduleID;
    }

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): static
    {
        $this->course = $course;
        return $this;
    }

    // ... 其他 getter 和 setter 方法
    public function getDayOfWeek(): ?string
    {
        return $this->DayOfWeek;
    }

    public function setDayOfWeek(?string $DayOfWeek): static
    {
        $this->DayOfWeek = $DayOfWeek;
        return $this;
    }

    public function getStartTime(): ?string
    {
        return $this->StartTime;
    }

    public function setStartTime(?string $StartTime): static
    {
        $this->StartTime = $StartTime;
        return $this;
    }

    public function getEndTime(): ?string
    {
        return $this->EndTime;
    }

    public function setEndTime(?string $EndTime): static
    {
        $this->EndTime = $EndTime;
        return $this;
    }

    public function getRoom(): ?string
    {
        return $this->Room;
    }

    public function setRoom(?string $Room): static
    {
        $this->Room = $Room;
        return $this;
    }
 
}