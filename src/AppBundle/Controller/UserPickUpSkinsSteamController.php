<?php

namespace AppBundle\Controller;

use AppBundle\Entity\UserPickUpSkinsSteam;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserPickUpSkinsSteamController extends Controller
{
    /**
     * @param Request $request
     *
     * @Route("/cases/user/pick-up/skins/steam", name="app_cases.user_pick_up_skins_steam.list")
     * @return Response
     */
    public function listAction(Request $request)
    {
        $domainId = $request->headers->get('x-domain-id');

        $em = $this->getDoctrine()->getManager();
        $listPickUpSkins = $em->getRepository(UserPickUpSkinsSteam::class)
            ->findListPickUpSkinsByDomainId($domainId);

        return $this->render(':default:list_pick_up_skins.html.twig', [
            'listPickUpSkins' => $listPickUpSkins,
        ]);
    }
}
