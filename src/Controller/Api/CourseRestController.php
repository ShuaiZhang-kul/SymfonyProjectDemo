<?php

namespace App\Controller\Api;

use App\Entity\Course;
use App\Entity\Professor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/courses', name: 'api_courses_')]
class CourseRestController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    // Get all courses
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $courses = $this->entityManager
            ->getRepository(Course::class)
            ->findAll();

        $data = [];
        foreach ($courses as $course) {
            $data[] = [
                'id' => $course->getCourseID(),
                'name' => $course->getCourseName(),
                'code' => $course->getCourseCode(),
                'description' => $course->getDescription()
            ];
        }

        return $this->json($data);
    }

    // Get single course
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $course = $this->entityManager
            ->getRepository(Course::class)
            ->find($id);

        if (!$course) {
            return $this->json(['message' => 'Course not found'], Response::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $course->getCourseID(),
            'name' => $course->getCourseName(),
            'code' => $course->getCourseCode(),
            'description' => $course->getDescription()
        ];

        return $this->json($data);
    }

    // Create course
    #[Route('/', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $course = new Course();
        $course->setCourseName($data['courseName']);
        $course->setCourseCode($data['courseCode']);
        $course->setDescription($data['description']);
        $course->setDepartment($data['department']);
        $course->setCredits($data['credits']);
        $professor = $this->getUser();
        if (!$professor) {
            $defaultProfessorId = 1;
            $professor = $this->entityManager->getRepository(Professor::class)->find($defaultProfessorId);
            if (!$professor) {
                throw new \Exception('Default professor not found');
            }
        }
        $course->setProfessor($professor);


        $this->entityManager->persist($course);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Course created successfully',
            'course' => [
                'id' => $course->getCourseID(),
                'name' => $course->getCourseName(),
                'code' => $course->getCourseCode(),
                'description' => $course->getDescription(),
                'department' => $course->getDepartment(),
                'credits' => $course->getCredits(),
                'professor' => $professor->getFirstName() . ' ' . $professor->getLastName()
            ]
        ], Response::HTTP_CREATED);
    }

    // Update course
    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        $course = $this->entityManager
            ->getRepository(Course::class)
            ->find($id);

        if (!$course) {
            return $this->json(['message' => 'Course not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (isset($data['courseName'])) {
            $course->setCourseName($data['courseName']);
        }
        if (isset($data['courseCode'])) {
            $course->setCourseCode($data['courseCode']);
        }
        if (isset($data['description'])) {
            $course->setDescription($data['description']);
        }
        if (isset($data['credits'])) {
            $course->setCredits($data['credits']);
        }
        if (isset($data['department'])) {
            $course->setDepartment($data['department']);
        }

        $this->entityManager->flush();

        return $this->json([
            'message' => 'Course updated successfully',
            'course' => [
                'id' => $course->getCourseID(),
                'name' => $course->getCourseName(),
                'code' => $course->getCourseCode(),
                'description' => $course->getDescription(),
                'credits' => $course->getCredits(),
                'department' => $course->getDepartment(),
                'professor' => $course->getProfessor()->getFirstName() . ' ' . $course->getProfessor()->getLastName()
            ]
        ]);
    }

    // Delete course
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $course = $this->entityManager
            ->getRepository(Course::class)
            ->find($id);

        if (!$course) {
            return $this->json(['message' => 'Course not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($course);
        $this->entityManager->flush();

        return $this->json(['message' => 'Course deleted successfully']);
    }
} 