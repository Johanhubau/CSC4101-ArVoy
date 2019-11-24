<?php

namespace App\Controller;



use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;


/**
 * @Route("/api")
 */
class SecurityController extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    private $entityManager;
    private $router;
    private $passwordEncoder;

    public function __construct(
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/login", name="login", methods={"POST"})
     */
    public function supports(Request $request)
    {
        return 'login' === $request->attributes->get('_route');
    }

    public function getCredentials(Request $request)
    {
        if ($request->getContentType() !== 'json' && $request->getContentType() !== "jsonld") {
            throw new BadRequestHttpException();
        }

        $data = json_decode($request->getContent(), true);

        $credentials = [
            'username' => $data['username'],
            'password' => $data['password']
        ];

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['username']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['username']]);

        if (!$user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('Username not found.');
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new JsonResponse($exception->getMessage());
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' =>
            $request->getSession()->get(Security::LAST_USERNAME)]);
        $data = null;
        if (! empty($user)) {
            $data = array(
                "username" => $user->getUsername(),
                "roles" => $user->getRoles(),
                "email" => $user->getEmail(),
                "displayName" => $user->getDisplayName()
            );
        }

        return new JsonResponse([
            'user' => $data ?? $data,
            'is_authenticated' => true
        ]);
    }

    /**
     * Return the URL to the login page.
     *
     * @return string
     */
    protected function getLoginUrl()
    {
        return "/api/login";
    }
}