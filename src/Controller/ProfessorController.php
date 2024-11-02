<?php

namespace App\Controller;

use App\Entity\Professor;
use App\Entity\Course;
use App\Entity\Student;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Controller class for handling professor-related operations
 */
class ProfessorController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/professors', name: 'get_all_professors', methods: ['GET'])]
    public function getAllProfessors(SerializerInterface $serializer): JsonResponse
    {
        $professors = $this->entityManager
            ->getRepository(Professor::class)
            ->findAll();

        $jsonData = $serializer->serialize($professors, 'json', [
            'groups' => ['student_read'],
            'circular_reference_handler' => function ($object) {
                if ($object instanceof Professor) {
                    return $object->getProfessorID();
                }
                if ($object instanceof Course) {
                    return $object->getCourseID();
                }
                if ($object instanceof Student) {
                    return $object->getStudentID();
                }
            }
        ]);

        return new JsonResponse($jsonData, 200, [], true);
    }

    #[Route('/professor/{id}', name: 'get_professor', methods: ['GET'])]
    public function getProfessor(int $id, SerializerInterface $serializer): JsonResponse
    {
        $professor = $this->entityManager
            ->getRepository(Professor::class)
            ->find($id);

        if (!$professor) {
            return new JsonResponse(['error' => 'Professor not found'], 404);
        }

        $jsonData = $serializer->serialize($professor, 'json', [
            'groups' => ['student_read'],
            'circular_reference_handler' => function ($object) {
                if ($object instanceof Professor) {
                    return $object->getProfessorID();
                }
                if ($object instanceof Course) {
                    return $object->getCourseID();
                }
                if ($object instanceof Student) {
                    return $object->getStudentID();
                }
            }
        ]);

        return new JsonResponse($jsonData, 200, [], true);
    }
}