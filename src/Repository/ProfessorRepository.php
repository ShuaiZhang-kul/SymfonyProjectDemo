<?php

namespace App\Repository;

use App\Entity\Professor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProfessorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Professor::class);
    }

    /**
     * 通过部门查找教授
     */
    public function findByDepartment(string $department)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.Department = :dept')
            ->setParameter('dept', $department)
            ->getQuery()
            ->getResult();
    }
    public function findAllProfessors()
    {
        return $this->findAll();
    }
}
