<?php

namespace App\Controller;

use App\Entity\Course;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Annotation\Groups;
/**
 * Controller class for handling course-related operations
 */
class CourseController extends AbstractController
{
    /**
     * Default index action for course endpoint
     * 
     * @return JsonResponse Returns a JSON welcome message
     */
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
     #[Route('/haoren', name: 'get_courses', methods: ['GET','HEAD'])]
    public function index(): Response
    {
        // return $this->json([
        //     'message' => '您是好人, 人好是您🫡🫡🫡!',
        //     // 'path' => 'src/Controller/CourseController.php',
        // ]);
        return $this->render('test.html.twig', [
            'message' => "您是好人, 人好是您🫡🫡🫡! ",
        ]);
    }
    #[Route('/courses', name: 'get_all_courses', methods: ['GET'])]
    public function getAllCourses(SerializerInterface $serializer): JsonResponse
    {
        $courses = $this->entityManager
            ->getRepository(Course::class)
            ->findAll();

        $jsonData = $serializer->serialize($courses, 'json', [
            'groups' => ['student_read'],
            'circular_reference_handler' => function ($object) {
                if ($object instanceof Course) {
                    return $object->getCourseID();
                }
                if ($object instanceof Professor) {
                    return $object->getProfessorID();
                }
                if ($object instanceof Student) {
                    return $object->getStudentID();
                }
            }
        ]);

        return new JsonResponse($jsonData, 200, [], true);
    }
    #[Route('/course/{id}', name: 'get_course', methods: ['GET'])]
    public function getCourse(int $id, SerializerInterface $serializer): JsonResponse
    {
        $course = $this->entityManager
            ->getRepository(Course::class)
            ->find($id);

        if (!$course) {
            return new JsonResponse(['error' => 'Course not found'], 404);
        }
        $processedCourse = [
            'CourseID' => $course->getCourseID(),
            'CourseName' => $course->getCourseName(),
            'CourseCode' => $course->getCourseCode(),
            'Description' => $course->getDescription(),
            'Credits' => $course->getCredits(),
            'Department' => $course->getDepartment(),
            'studentCount' => $course->getStudents()->count(), // 仅返回数量
            'schedule' => $course->getSchedule()->getScheduleID(),
            'professorId' => $course->getProfessor()->getProfessorID() // 仅返回教授ID
        ];


        $jsonData = $serializer->serialize($processedCourse, 'json');

        return new JsonResponse($jsonData, 200, [], true);
    }           
}
