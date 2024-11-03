<?php

namespace App\Security;

use App\Entity\Student;
use App\Entity\Professor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(
        private EntityManagerInterface $entityManager,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');
        $password = $request->request->get('password', '');
        $userType = $request->request->get('_user_type') === 'professor' ? 'professor' : 'student';
        $csrfToken = $request->request->get('_csrf_token');

        error_log("Login attempt - Email: $email, UserType: $userType");

        try {
            return new Passport(
                new UserBadge($email, function($userIdentifier) use ($userType) {
                    if ($userType === 'professor') {
                        return $this->entityManager->getRepository(Professor::class)
                            ->findOneBy(['Email' => $userIdentifier]);
                    } else {
                        return $this->entityManager->getRepository(Student::class)
                            ->findOneBy(['Email' => $userIdentifier]);
                    }
                }),
                new PasswordCredentials($password),
                [
                    new CsrfTokenBadge('authenticate', $csrfToken),
                    new RememberMeBadge(),
                ]
            );
        } catch (\Exception $e) {
            error_log("Authentication error: " . $e->getMessage());
            throw $e;
        }
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $user = $token->getUser();
        
        // 根据用户类型重定向到不同的页面
        if ($user instanceof Student) {
            return new RedirectResponse($this->urlGenerator->generate('student_dashboard'));
        } else {
            return new RedirectResponse($this->urlGenerator->generate('professor_dashboard'));
        }
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
} 