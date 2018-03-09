<?php

namespace ApiCasesBundle\Controller;

use AppBundle\Entity\CasesSkinsDropUser;
use AppBundle\Entity\CasesSkinsPickUpUser;
use AppBundle\Entity\Currency;
use AppBundle\Entity\User;
use AppBundle\Entity\UserPickUpSkinsSteam;
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
            $pagination['price'] = round($pagination['price'], 2);

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
     * @param Request $request
     * @param $casesSkinsPickUpUserId
     * @return Response
     * @Rest\Post("/user/skins/steam/pick-up/{casesSkinsPickUpUserId}", requirements={"casesSkinsPickUpUserId": "\d+"})
     * @Rest\View()
     */
    public function postUserPickUpSkinsSteamAction(Request $request, $casesSkinsPickUpUserId)
    {
        $domainId = $request->headers->get('x-domain-id');

        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $casesSkinsPickUpUser = $em->getRepository(CasesSkinsPickUpUser::class)
            ->findOneSkinsByIdAndUserIdAndDomainId($casesSkinsPickUpUserId, $user, $domainId);

        if (!$casesSkinsPickUpUser) {
            $view = $this->view('Not found skins by user');
            return $this->handleView($view);
        }

        try {
            $userPickUpSkinsSteam = new UserPickUpSkinsSteam();
            $userPickUpSkinsSteam->setCasesDomain($casesSkinsPickUpUser->getCasesDomain());
            $userPickUpSkinsSteam->setUser($casesSkinsPickUpUser->getUser());
            $userPickUpSkinsSteam->setSkins($casesSkinsPickUpUser->getSkins());

            $em->remove($casesSkinsPickUpUser);
            $em->persist($userPickUpSkinsSteam);
            $em->flush();

            $view = $this->view('success');
        } catch (\Exception $e) {
            $view = $this->view('Bad request');
        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @param $casesSkinsPickUpUserId
     * @return Response
     * @Rest\Post("/user/skins/steam/sell/{casesSkinsPickUpUserId}", requirements={"casesSkinsPickUpUserId": "\d+"})
     * @Rest\View()
     */
    public function postUserSellSkinsAction(Request $request, $casesSkinsPickUpUserId)
    {
        $domainId = $request->headers->get('x-domain-id');

        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $casesSkinsPickUpUser = $em->getRepository(CasesSkinsPickUpUser::class)
            ->findOneSkinsByIdAndUserIdAndDomainId($casesSkinsPickUpUserId, $user, $domainId);

        if (!$casesSkinsPickUpUser) {
            $view = $this->view('Not found skins by user');
            return $this->handleView($view);
        }

        $currency = $em->getRepository(Currency::class)
            ->findOneBy(['code' => $request->headers->get('x-currency-code')]);

        if (!$currency) {
            $view = $this->view('Не верный код валюты.', 400);
            return $this->handleView($view);
        }

        try {
            $balance = $this->get('api_cases.service.user_pick_up_skins_steam')
                ->handler($casesSkinsPickUpUser->getSkins(), $user->getId(), $domainId, $currency->getId());

            $em->remove($casesSkinsPickUpUser);
            $em->flush();

            $view = $this->view(['balance' => $balance]);
        } catch (\Exception $e) {
            $view = $this->view('Bad request');
        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request21
     *
     * @Rest\Post("/user/add-href-trade")
     * @Rest\View(serializerGroups={"cases_user"})
     * @return Response
     */
    public function postAddHrefTradeAction(Request $request)
    {
        $domainId = $request->headers->get('x-domain-id');
        $hrefTrade = $request->request->get('href_trade');

        $userId = $this->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)
            ->findUserInformationByUserIdAndDomainId($userId, $domainId);

        $user->setHrefTrade($hrefTrade);
        $em->flush();

        try {
            $countDropSkins = $em->getRepository(CasesSkinsDropUser::class)
                ->getCountOpenCasesByDomainIdAndUserId($domainId, $userId);
        } catch (DBALException $e) {
            $this->get('logger')->error($e->getMessage());
            $countDropSkins = 0;
        }

        $view = $this->view(['profile' => $user, 'count_drop_skins' => $countDropSkins], 200);
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
