<?php

namespace ApiCasesBundle\Controller;

use AppBundle\Entity\CasesSkinsPickUpUser;
use AppBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends FOSRestController
{
    /**
     * @param Request $request
     * @Rest\Get("/user")
     * @Rest\View(serializerGroups={"cases_user"})
     * @return Response
     */
    public function getUserAction(Request $request)
    {
        $domainId = $request->headers->get('x-domain-id');

        $user = $this->getDoctrine()->getRepository(User::class)
            ->findUserInformationByUserIdAndDomainId($this->getUser()->getId(), $domainId);

        $view = $this->view($user, 200);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @Rest\Get("/user/skins")
     * @Rest\View()
     * @return Response
     */
    public function getUserSkinAction(Request $request)
    {
        $domainId = $request->headers->get('x-domain-id');

        $userId = $this->getUser()->getId();
        $skins = $this->getDoctrine()->getRepository(CasesSkinsPickUpUser::class)
            ->findSkinsByUserIdAndDomainId($userId, $domainId);

        $skins = array_map(function ($item) {
            $item['skin_name'] = mb_strimwidth($item['skin_name'], 0, 15, '...', 'utf-8');
            return $item;
        }, $skins);

        $view = $this->view($skins, 200);
        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/user/tops")
     * @Rest\View(serializerGroups={"cases_user"})
     * @return Response
     */
    public function getUserTopsAction()
    {
        $user = $this->getUser();

        $view = $this->view($user, 200);
        return $this->handleView($view);
    }
}
