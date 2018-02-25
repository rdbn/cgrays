<?php

namespace ApiCasesBundle\Controller;

use AppBundle\Entity\CasesSkinsDropUser;
use AppBundle\Entity\CasesSkinsPickUpUser;
use AppBundle\Entity\User;
use AppBundle\Services\Helper\MbStrimWidthHelper;
use Doctrine\DBAL\DBALException;
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

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)
            ->findUserInformationByUserIdAndDomainId($this->getUser()->getId(), $domainId);

        try {
            $countDropSkins = $em->getRepository(CasesSkinsDropUser::class)
                ->getCountOpenCasesByDomainIdAndUserId($domainId, $this->getUser()->getId());
        } catch (DBALException $e) {
            $this->get('logger')->error($e->getMessage());
            $countDropSkins = 0;
        }

        $view = $this->view(['profile' => $user, 'count_drop_skins' => $countDropSkins], 200);
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
     * @param Request $request
     * @param $userId
     * @return Response
     * @Rest\Get("/user/{userId}", requirements={"userId": "\d+"})
     * @Rest\View()
     */
    public function getUserIdAction(Request $request, $userId)
    {
        $domainId = $request->headers->get('x-domain-id');

        try {
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository(User::class)
                ->findUsersByUserId($userId);

            $casesUserSkins = $em->getRepository(CasesSkinsPickUpUser::class)
                ->findSkinsByUserIdAndDomainId($userId, $domainId);

            $casesUserSkins = array_map(function ($item) {
                $item['skin_name'] = MbStrimWidthHelper::strimWidth($item['skin_name']);
                $item['steam_image'] = "/{$item['steam_image']}";

                return $item;
            }, $casesUserSkins);

            $view = $this->view(['user' => $user, 'user_skins' => $casesUserSkins], 200);
            return $this->handleView($view);
        } catch (DBALException $e) {
            $view = $this->view('Bad request', 400);
            return $this->handleView($view);
        }
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
