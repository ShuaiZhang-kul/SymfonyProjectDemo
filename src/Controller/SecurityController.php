<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\RegistrationFormType;
use App\Entity\Student;
use App\Entity\Professor;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\FormFactoryInterface;

class SecurityController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // 如果用户已登录，根据用户类型重定向
        if ($this->getUser()) {
            if ($this->getUser() instanceof Student) {
                return $this->redirectToRoute('student_dashboard');
            }
            return $this->redirectToRoute('professor_dashboard');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // 这个方法可以为空，退出由防火墙处理
    }

    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher
    ): Response {
        // 如果已经登录，重定向到首页
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // 获取用户类型（从请求中或默认为student）
        $userType = $request->request->get('_user_type') === 'professor' ? 'professor' : 'student';
        
        // 根据用户类型创建对应实体
        $user = $userType === 'professor' ? new Professor() : new Student();
        
        $form = $this->formFactory->create(RegistrationFormType::class, $user, [
            'data_class' => $userType === 'professor' ? Professor::class : Student::class,
            'user_type' => $userType
        ]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 设置密码
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            // 设置其他字段
            $user->setFirstName($form->get('FirstName')->getData());
            $user->setLastName($form->get('LastName')->getData());
            $user->setEmail($form->get('Email')->getData());


            $this->entityManager->persist($user);
            $this->entityManager->flush();

            // 添加成功消息
            $this->addFlash('success', 'Please login to continue');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
            'user_type' => $userType
        ]);
    }
    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function forgotPassword(): Response
    {
        return $this->render('security/forgot_password.html.twig');
    } 
} 