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
        $authChecker = $this->get('security.authorization_checker');
        $session = $request->getSession();
        if ($authChecker->isGranted('ROLE_ALLOWED_TO_SWITCH') || $session->has('uuid_switch_user')) {
            if ($session->has('uuid_switch_user')) {
                $steamId = $session->get('uuid_switch_user');
            } else {
                $session->set('uuid_switch_user', $this->getUser()->getSteamId());
            }

            $em = $this->get('doctrine.orm.entity_manager');
            $user = $em->getRepository(User::class)
                ->findOneBy(['steamId' => $steamId]);

            $token = new UsernamePasswordToken($user, '', 'frontend', $user->getRoles());
            $this->get('security.token_storage')->setToken($token);
        }

        return $this->redirectToRoute('app.skins.main');
    }
}
