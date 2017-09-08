<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Role\SwitchUserRole;

class SecurityController extends Controller
{
    /**
     * @param Request $request
     * @Route("/login", name="login")
     * @return Response
     */
    public function loginAction(Request $request)
    {
        $authenticationUtils = $this->get('security.authentication_utils');

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('default/login.html.twig', [
            'error' => $error,
            'last_username' => $lastUsername,
        ]);
    }

    /**
     * @param Request $request
     * @param string $username
     * @Route("/switch/user/{username}", name="app.security.switch_user")
     * @return RedirectResponse
     */
    public function switchUserAction(Request $request, $username)
    {
        $authChecker = $this->get('security.authorization_checker');
        if ($authChecker->isGranted('ROLE_ADMIN')) {
            $tokenStorage = $this->get('security.token_storage');

            $user = $this->getDoctrine()->getRepository(User::class)
                ->findOneBy(['username' => $username]);

            $role = $this->getDoctrine()->getRepository(Role::class)
                ->findOneBy(['role' => 'ROLE_ADMIN']);

            $user->addRole($role);

            $token = new UsernamePasswordToken($user, '', 'frontend', $user->getRoles());
            $tokenStorage->setToken($token);
        }

        return $this->redirectToRoute('app.skins.main');
    }
}
