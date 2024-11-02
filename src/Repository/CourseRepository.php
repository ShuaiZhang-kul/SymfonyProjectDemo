<?php

namespace App\Repository;

use App\Entity\Course;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CourseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }

    public function findByDepartment(string $department)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.Department = :dept')
            ->setParameter('dept', $department)
            ->getQuery()
            ->getResult();
    }

    public function findByProfessor(int $professorId)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.professor = :profId')
            ->setParameter('profId', $professorId)
            ->getQuery()
            ->getResult();
    }
}