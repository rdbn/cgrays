<?php

namespace ApiCasesBundle\Controller;

use AppBundle\Entity\CasesSkinsPickUpUser;
use AppBundle\Entity\User;
use AppBundle\Services\Helper\MbStrimWidthHelper;
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
        $casesSkinsRepository = $this->getDoctrine()->getRepository(CasesSkinsPickUpUser::class);

        $paginator = $this->get('knp_paginator');
        $paginations = $paginator->paginate(
            $casesSkinsRepository->queryPaginationByDomainIdAndUserId($domainId, $userId),
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 18)
        );

        $skins = [];
        foreach ($paginations as $pagination) {
            $pagination['skin_name'] = MbStrimWidthHelper::strimWidth($pagination['skin_name']);
            $skins[] = $pagination;
        }

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
