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

class SecurityController extends Controller
{
    const SWITCH_USER = 'uuid_switch_user';

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
     * @param string $steamId
     * @Route("/switch/user/{steamId}", name="app.security.switch_user")
     * @return RedirectResponse
     */
    public function switchUserAction(Request $request, $steamId)
    {
        $tokenStorage = $this->get('security.token_storage');
        $authChecker = $this->get('security.authorization_checker');

        if ($authChecker->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
            $user = $this->get('doctrine.orm.entity_manager')->getRepository(User::class)
                ->findOneBy(['steamId' => $steamId]);

            $request->getSession()->set(self::SWITCH_USER, $tokenStorage->getToken()->getUser()->getSteamId());

            $token = new UsernamePasswordToken($user, '', 'frontend', $user->getRoles());
            $tokenStorage->setToken($token);
        }

        return $this->redirectToRoute('app.skins.main');
    }

    /**
     * @param Request $request
     * @Route("/switch/exit", name="app.security.switch_exit")
     * @return RedirectResponse
     */
    public function exitSwitchUserAction(Request $request)
    {
        $session = $request->getSession();
        if ($session->has(self::SWITCH_USER)) {
            $steamId = $session->get(self::SWITCH_USER);
            $session->remove(self::SWITCH_USER);

            $user = $this->get('doctrine.orm.entity_manager')->getRepository(User::class)
                ->findOneBy(['steamId' => $steamId]);

            $token = new UsernamePasswordToken($user, '', 'frontend', $user->getRoles());
            $this->get('security.token_storage')->setToken($token);
        }

        return $this->redirectToRoute('app.skins.main');
    }
}
