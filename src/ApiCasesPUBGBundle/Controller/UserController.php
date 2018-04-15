<?php

namespace ApiCasesPUBGBundle\Controller;

use AppBundle\Entity\CasesSkinsDropUserPUBG;
use AppBundle\Entity\CasesSkinsPickUpUserPUBG;
use AppBundle\Entity\Currency;
use AppBundle\Entity\User;
use AppBundle\Entity\UserPickUpSkinsSteamPUBG;
use AppBundle\Services\Helper\MbStrimWidthHelper;
use Doctrine\DBAL\DBALException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends FOSRestController
{
    /**
     * @SWG\Parameter(
     *     name="x-domain-id",
     *     in="header",
     *     type="string",
     *     description="UIID - уникальный ключь для индентификации домена",
     *     required=true
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Возразает информацию о пользователе",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class, groups={"full"}))
     *     ),
     *     examples={
     *         "application/json": {
     *             "profile": {
     *                 "steam_id": "77777777777777777",
     *                 "href_trade": "https://steamcommunity.com/tradeoffer/new/?partner=11111111&token=cvg3Dl0s",
     *                 "username": "username",
     *                 "created_at": "2018-02-23T21:04:11+0000",
     *                 "cases_balance_user": {
     *                     {
     *                         "currency": {
     *                             "name": "руб.",
     *                             "code": "RUB"
     *                         },
     *                         "balance": 1529.3
     *                     }
     *                 }
     *             },
     *             "count_drop_skins": 16
     *         }
     *     }
     * )
     *
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
            $countDropSkins = $em->getRepository(CasesSkinsDropUserPUBG::class)
                ->getCountOpenCasesByDomainIdAndUserId($domainId, $this->getUser()->getId());
        } catch (DBALException $e) {
            $this->get('logger')->error($e->getMessage());
            $countDropSkins = 0;
        }

        $view = $this->view(['profile' => $user, 'count_drop_skins' => $countDropSkins], 200);
        return $this->handleView($view);
    }

    /**
     * @SWG\Parameter(
     *     name="x-domain-id",
     *     in="header",
     *     type="string",
     *     description="UIID - уникальный ключь для индентификации домена",
     *     required=true
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Возвращает списсок скинов, которые забрал пользователь после игры, сортировка по убыванию",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=CasesSkinsPickUpUserPUBG::class, groups={"full"}))
     *     ),
     *     examples={
     *         "application/json": {
     *             {
     *                 "id": 2,
     *                 "skin_name": "Padded Jacke...",
     *                 "steam_image": "image/34f73582a38cc7c89e2fcc3d3f239423.png",
     *                 "price": 6.33,
     *                 "rarity_id": 5
     *             },
     *             {
     *                 "id": 1,
     *                 "skin_name": "Padded Jacke...",
     *                 "steam_image": "image/34f73582a38cc7c89e2fcc3d3f239423.png",
     *                 "price": 6.33,
     *                 "rarity_id": 5
     *             }
     *         }
     *     }
     * )
     *
     * @param Request $request
     * @Rest\Get("/user/skins")
     * @Rest\View()
     * @return Response
     */
    public function getUserSkinAction(Request $request)
    {
        $domainId = $request->headers->get('x-domain-id');

        $userId = $this->getUser()->getId();
        $casesSkinsRepository = $this->getDoctrine()->getRepository(CasesSkinsPickUpUserPUBG::class);

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
     * @SWG\Parameter(
     *     name="x-domain-id",
     *     in="header",
     *     type="string",
     *     description="UIID - уникальный ключь для индентификации домена",
     *     required=true
     * )
     * @SWG\Parameter(
     *     name="userId",
     *     in="path",
     *     type="string",
     *     description="userId - id пользователя, \d+",
     *     required=true
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Получить информацию и список скинов пользователя по userId",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class, groups={"full"}))
     *     ),
     *     examples={
     *         "application/json": {
     *             "user": {
     *                 "username": "username",
     *                 "steam_id": "77777777777777777",
     *                 "count_drop_skins": "117"
     *             },
     *             "user_skins": {
     *                 {
     *                     "skin_name": "Padded Jacke...",
     *                     "steam_image": "/image/34f73582a38cc7c89e2fcc3d3f239423.png",
     *                     "price": "6.33",
     *                     "rarity_id": 5
     *                 },
     *                 {
     *                     "skin_name": "Padded Jacke...",
     *                     "steam_image": "/image/34f73582a38cc7c89e2fcc3d3f239423.png",
     *                     "price": "6.33",
     *                     "rarity_id": 5
     *                 }
     *             }
     *         }
     *     }
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Если что то пойдет не так то 400",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class, groups={"full"}))
     *     ),
     *     examples={
     *         "application/json": {
     *             "400": "Bad request"
     *         }
     *     }
     * )
     *
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

            $casesUserSkins = $em->getRepository(CasesSkinsPickUpUserPUBG::class)
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
     * @SWG\Post(
     *     description="Выводим в Steam скин, который решил оставить себе пользователь после игры",
     *     @SWG\Parameter(
     *         name="x-domain-id",
     *         in="header",
     *         type="string",
     *         description="UIID - уникальный ключь для индентификации домена",
     *         required=true
     *     ),
     *     @SWG\Parameter(
     *         name="casesSkinsPickUpUserId",
     *         in="path",
     *         type="string",
     *         description="casesSkinsPickUpUserId - id скина выпавшего пользователю, \d+",
     *         required=true
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Вслучае успеха success",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref=@Model(type=UserPickUpSkinsSteamPUBG::class, groups={"full"}))
     *         ),
     *         examples={
     *             "application/json": {
     *                 "success"
     *             }
     *         }
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Если скин не найден или что то пошло не так 400",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref=@Model(type=UserPickUpSkinsSteamPUBG::class, groups={"full"}))
     *         ),
     *         examples={
     *             "application/json": {
     *                 "400": "Bad request"
     *             }
     *         }
     *     )
     * )
     *
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
        $casesSkinsPickUpUser = $em->getRepository(CasesSkinsPickUpUserPUBG::class)
            ->findOneSkinsByIdAndUserIdAndDomainId($casesSkinsPickUpUserId, $user, $domainId);

        if (!$casesSkinsPickUpUser) {
            $view = $this->view('Not found skins by user');
            return $this->handleView($view);
        }

        try {
            $userPickUpSkinsSteam = new UserPickUpSkinsSteamPUBG();
            $userPickUpSkinsSteam->setCasesDomain($casesSkinsPickUpUser->getCasesDomain());
            $userPickUpSkinsSteam->setUser($casesSkinsPickUpUser->getUser());
            $userPickUpSkinsSteam->setSkins($casesSkinsPickUpUser->getSkins());

            $em->remove($casesSkinsPickUpUser);
            $em->persist($userPickUpSkinsSteam);
            $em->flush();

            $view = $this->view('success', 200);
        } catch (\Exception $e) {
            $view = $this->view('Bad request', 400);
        }

        return $this->handleView($view);
    }

    /**
     * @SWG\Post(
     *     description="Продаем скин системе, который решил оставить себе пользователь после игры",
     *     @SWG\Parameter(
     *         name="x-domain-id",
     *         in="header",
     *         type="string",
     *         description="UIID - уникальный ключь для индентификации домена",
     *         required=true
     *     ),
     *     @SWG\Parameter(
     *         name="x-currency-code",
     *         in="header",
     *         type="string",
     *         description="Код валюты - по дефолту RUB"
     *     ),
     *     @SWG\Parameter(
     *         name="casesSkinsPickUpUserId",
     *         in="path",
     *         type="string",
     *         description="casesSkinsPickUpUserId - id скина выпавшего пользователю, \d+",
     *         required=true
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="В случае успеха возращает баланс пользователя",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref=@Model(type=User::class, groups={"full"}))
     *         ),
     *         examples={
     *             "application/json": {
     *                 "balance": 186
     *             }
     *         }
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Если что то пойдет не так то 400 и описание ошибки",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref=@Model(type=User::class, groups={"full"}))
     *         ),
     *         examples={
     *             "application/json": {
     *                 "400": "Bad request"
     *             }
     *         }
     *     )
     * )
     *
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
        $casesSkinsPickUpUser = $em->getRepository(CasesSkinsPickUpUserPUBG::class)
            ->findOneSkinsByIdAndUserIdAndDomainId($casesSkinsPickUpUserId, $user, $domainId);

        if (!$casesSkinsPickUpUser) {
            $view = $this->view('Not found skins by user');
            return $this->handleView($view);
        }

        $currency = $em->getRepository(Currency::class)
            ->findOneBy(['code' => $request->headers->get('x-currency-code', 'RUB')]);

        if (!$currency) {
            $view = $this->view('Not valid currency code', 400);
            return $this->handleView($view);
        }

        try {
            $balance = $this->get('api_cases_pubg.service.user_pick_up_skins_steam')
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
     * @SWG\Parameter(
     *     name="x-domain-id",
     *     in="header",
     *     type="string",
     *     description="UIID - уникальный ключь для индентификации домена",
     *     required=true
     * )
     * @SWG\Parameter(
     *     name="href_trade",
     *     in="body",
     *     description="Значение ссылки для трейда",
     *     required=true,
     *     @SWG\Schema(ref=""),
     * )
     * @SWG\Response(
     *     response=200,
     *     description="В случае успеха возвращает обновленную информацию о пользователе",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class, groups={"full"}))
     *     ),
     *     examples={
     *         "application/json": {
     *             "profile": {
     *                 "steam_id": "77777777777777777",
     *                 "href_trade": "https://steamcommunity.com/tradeoffer/new/?partner=11111111&token=cvg3Dl0s",
     *                 "username": "username",
     *                 "created_at": "2018-02-23T21:04:11+0000",
     *                 "cases_balance_user": {
     *                     {
     *                         "currency": {
     *                             "name": "руб.",
     *                             "code": "RUB"
     *                         },
     *                         "balance": 1529.3
     *                     }
     *                 }
     *             },
     *             "count_drop_skins": 16
     *         }
     *     }
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Если href_trade пустой или что то пошло не так, то 400",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class, groups={"full"}))
     *     ),
     *     examples={
     *         "application/json": {
     *             "400": "Bad request"
     *         }
     *     }
     * )
     *
     * @param Request $request
     *
     * @Rest\Post("/user/add-href-trade")
     * @Rest\View(serializerGroups={"cases_user"})
     * @return Response
     */
    public function postAddHrefTradeAction(Request $request)
    {
        $domainId = $request->headers->get('x-domain-id');
        $hrefTrade = $request->request->get('href_trade');
        if (!$hrefTrade) {
            $view = $this->view('href_trade is empty', 400);
            return $this->handleView($view);
        }

        $userId = $this->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)
            ->findUserInformationByUserIdAndDomainId($userId, $domainId);

        $user->setHrefTrade($hrefTrade);
        $em->flush();

        try {
            $countDropSkins = $em->getRepository(CasesSkinsDropUserPUBG::class)
                ->getCountOpenCasesByDomainIdAndUserId($domainId, $userId);
        } catch (DBALException $e) {
            $this->get('logger')->error($e->getMessage());
            $countDropSkins = 0;
        }

        $view = $this->view(['profile' => $user, 'count_drop_skins' => $countDropSkins], 200);
        return $this->handleView($view);
    }

    /**
     * @SWG\Parameter(
     *     name="x-domain-id",
     *     in="header",
     *     type="string",
     *     description="UIID - уникальный ключь для индентификации домена",
     *     required=true
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Авторизация, используется только в примерах документации",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class, groups={"full"}))
     *     ),
     *     examples={
     *         "application/json": {
     *             "token": "Bearer {hash_key}"
     *         }
     *     }
     * )
     *
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
