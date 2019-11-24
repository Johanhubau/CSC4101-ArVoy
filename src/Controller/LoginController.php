<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api")
 */
final class LoginController extends AbstractController
{
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @Route("/login", name="login_check", methods={"GET"})
     * @param AuthenticationUtils $authenticationUtils
     * @return JsonResponse
     */
    public function __invoke(AuthenticationUtils $authenticationUtils): JsonResponse
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $user = $this->getUser();
        $data = null;
        if (! empty($user)) {
            $userClone = clone $user;
            $userClone->setPassword('');
            $data = array(
                "username" => $user->getUsername(),
                "roles" => $user->getRoles(),
                "email" => $user->getEmail(),
                "displayName" => $user->getDisplayName()
            );
        }

        return new JsonResponse([
            'last_username' => $lastUsername,
            'user' => $data ?? $data,
            'is_authenticated' => !empty($this->getUser()),
            'error' => $error
        ], !empty($user) ? 200 : 401);
    }

    /**
     * @Route("/login/logout", name="login_logout", methods={"GET"})
     *
     * @throws \Exception
     */
    public function logout()
    {
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }
}