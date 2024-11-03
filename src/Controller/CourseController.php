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
use Symfony\Component\HttpFoundation\Request;
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
    #[Route('/course/courses', name: 'get_all_courses', methods: ['GET'])]
    public function getAllCourses(SerializerInterface $serializer): JsonResponse
    {
        $courses = $this->entityManager
            ->getRepository(Course::class)
            ->findAll();

            $processedCourses = array_map(function($course) {
                return [
                    'CourseID' => $course->getCourseID(),
                    'CourseName' => $course->getCourseName(), 
                    'CourseCode' => $course->getCourseCode(),
                    'Description' => $course->getDescription(),
                    'Credits' => $course->getCredits(),
                    'Department' => $course->getDepartment(),
                    'studentCount' => $course->getStudents()->count(), // 仅返回数量
                    'professorName' => $course->getProfessor()->getFirstName() . ' ' .  
                    $course->getProfessor()->getLastName() // 仅返回教授姓名
                ];
            }, $courses);
    
            $jsonData = $serializer->serialize($processedCourses, 'json');
    
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
    /**
     * @Route("/api/course/enroll", name="course_enroll", methods={"POST"})
     */
    #[Route('/course/enroll', name: 'course_enroll', methods: ['POST'])]
    public function enroll(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $courseId = $data['courseId'];
        
        try {
            // 获取当前登录学生
            $student = $this->getUser();
            
            // 获取课程
            $course = $this->entityManager
            ->getRepository(Course::class)
            ->find($courseId);
            
            if (!$course) {
                throw new \Exception('Course not found');
            }
            
            // 添加选课逻辑
            $student->addCourse($course);
            $this->entityManager->persist($student);
            $this->entityManager->flush();
            
            return new JsonResponse(['message' => 'Enrollment successful'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
    #[Route('/course/unenroll', name: 'course_unenroll', methods: ['POST'])]
    public function unenroll(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $courseId = $data['courseId'];
        
        try {
            // Get current logged in student
            $student = $this->getUser();
            
            // Get the course
            $course = $this->entityManager
                ->getRepository(Course::class)
                ->find($courseId);
            
            if (!$course) {
                throw new \Exception('Course not found');
            }
            
            // Remove course from student's courses
            $student->removeCourse($course);
            $this->entityManager->persist($student);
            $this->entityManager->flush();
            
            return new JsonResponse(['message' => 'Successfully unenrolled from course'], Response::HTTP_OK);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
    #[Route('/course/new', name: 'create_course', methods: ['POST'])]
    public function createCourse(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            // Create new course entity
            $course = new Course();
            $course->setCourseName($data['courseName']);
            $course->setCourseCode($data['courseCode']);
            $course->setDescription($data['description']);
            $course->setCredits($data['credits']);

            $professor = $this->getUser();
            if (!$professor) {
                throw new \Exception('Professor not found - user not authenticated');
            }
            $course->setDepartment($professor->getDepartment());
            $course->setProfessor($professor);
            // Save to database
            $this->entityManager->persist($course);
            $this->entityManager->flush();
            
            return new JsonResponse([
                'message' => 'Course created successfully',
                'courseId' => $course->getCourseID()
            ], Response::HTTP_CREATED);
            
        } catch (\Exception $e) {
            return new JsonResponse([
                'error' => 'Failed to create course: ' . $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }
    }
}
