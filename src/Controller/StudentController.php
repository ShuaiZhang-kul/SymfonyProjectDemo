<?php

namespace App\Controller;


use App\Entity\Student;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Annotation\Groups;


class StudentController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/student', name: 'app_student')]
    public function index(): Response
    {
        return $this->render('student/index.html.twig', [
            'controller_name' => 'StudentController',
        ]);
    }

    #[Route('/student/{id}', name: 'get_student', methods: ['GET'])]
    public function getStudent(int $id, StudentRepository $studentRepository, SerializerInterface $serializer): JsonResponse
    {
        // 从数据库中获取 Student 实体
        $student = $studentRepository->find($id);

        if (!$student) {
            return new JsonResponse(['error' => 'Student not found'], 404);
        }

        // 序列化 Student 实体
        $data = $serializer->serialize($student, 'json', ['groups' => 'student_read']);

        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/students', name: 'app_students', methods: ['GET'])]
    public function getAllStudents(SerializerInterface $serializer): JsonResponse
    {
        $students = $this->entityManager
            ->getRepository(Student::class)
            ->findAll();

    
        // $jsonData = $serializer->serialize($students, 'json', [
        //     'groups' => ['student_read'],
        //     'circular_reference_handler' => function ($object) {
        //         if ($object instanceof Course) {
        //             return $object->getCourseID();
        //         }
        //         if ($object instanceof Professor) {
        //             return $object->getProfessorID();
        //         }
        //         if ($object instanceof Student) {
        //             return $object->getStudentID();
        //         }
        //     }
        // ]);
        $processedStudents = array_map(function($student) {
            return [
                'StudentID' => $student->getStudentID(),
                'FirstName' => $student->getFirstName(),
                'LastName' => $student->getLastName(),
                'Email' => $student->getEmail(),
                'DateOfBirth' => $student->getDateOfBirth(),
                'EnrollmentYear' => $student->getEnrollmentYear(),
                'courseIds' => $student->getCourses()->map(fn($course) => $course->getCourseID())->toArray()
            ];
        }, $students);
        $jsonData = $serializer->serialize($processedStudents, 'json');
        return new JsonResponse($jsonData, 200, [], true);
    }
    
}
