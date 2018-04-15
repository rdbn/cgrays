<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 07.12.2017
 * Time: 11:03
 */

namespace ApiCasesPUBGBundle\Controller;

use AppBundle\Entity\Currency;
use AppBundle\Entity\CasesSkinsPUBG;
use AppBundle\Entity\CasesSkinsDropUserPUBG;
use AppBundle\Entity\CasesSkinsPickUpUserPUBG;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GameController extends FOSRestController
{
    /**
     * @SWG\Parameter(
     *     name="x-domain-id",
     *     in="header",
     *     type="string",
     *     description="UIID - уникальный ключь для индентификации домена",
     *     required=true
     * )
     * @SWG\Parameter(
     *     name="x-currency-code",
     *     in="header",
     *     type="string",
     *     description="Код валюты - по дефолту RUB"
     * )
     * @SWG\Parameter(
     *     name="casesId",
     *     in="path",
     *     type="string",
     *     description="casesId - ID страницы, \d+",
     *     required=true
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Возвращает информацию и результате игры",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=CasesSkinsPUBG::class, groups={"full"}))
     *     ),
     *     examples={
     *         "application/json": {
     *             "skins_drop_id": 186,
     *             "name": "test_pubg_123",
     *             "skin_name": "6.00",
     *             "rarity": "image/d73f883da8d866fff307a91275cbb390.png",
     *             "rarity_id": "10",
     *             "username": "username",
     *             "user_id": "1",
     *             "cases_image": "image/d73f883da8d866fff307a91275cbb390.png",
     *             "steam_image": "image/d73f883da8d866fff307a91275cbb390.png",
     *             "price": "10.00",
     *             "balance": "1000"
     *         }
     *     }
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Если не передан код валюты или игра не прошла валидацию то ответ 400",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=CasesSkinsPUBG::class, groups={"full"}))
     *     ),
     *     examples={
     *         "application/json": {
     *             "400": "Bad request"
     *         }
     *     }
     * )
     *
     * @param Request $request
     * @param int $casesId
     * @Rest\Get("/game/{casesId}", requirements={"casesId": "\d+"})
     * @Rest\View()
     * @return Response
     */
    public function getCasesOpenAction(Request $request, $casesId)
    {
        $domainId = $request->headers->get('x-domain-id');
        $currency = $this->getDoctrine()->getRepository(Currency::class)
            ->findOneBy(['code' => $request->headers->get('x-currency-code', 'RUB')]);

        if (!$currency) {
            $view = $this->view('Not valid currency code', 400);
            return $this->handleView($view);
        }

        $userId = $this->getUser()->getId();
        $gameService = $this->get('api_cases.service.games');
        if ($gameService->checkGameUserId($userId)) {
            $view = $this->view("Game is not end", 400);
            return $this->handleView($view);
        }

        try {
            $case = $this->get('api_cases_pubg.service.case_open')
                ->handler($domainId, $casesId, $this->getUser(), $currency->getId());

            $view = $this->view($case, 200);
        } catch (\Exception $e) {
            $this->get('logger')->error($e->getMessage());
            $view = $this->view('Bad request', 400);
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
     *     name="x-currency-code",
     *     in="header",
     *     type="string",
     *     description="Код валюты - по дефолту RUB"
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Продать предмет, выигранный пользователем",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=CasesSkinsDropUserPUBG::class, groups={"full"}))
     *     ),
     *     examples={
     *         "application/json": {
     *             "balance": 186
     *         }
     *     }
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Если не передан код валюты или игра не прошла валидацию то ответ 400",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=CasesSkinsDropUserPUBG::class, groups={"full"}))
     *     ),
     *     examples={
     *         "application/json": {
     *             "400": "Bad request"
     *         }
     *     }
     * )
     *
     * @param Request $request
     * @Rest\Post("/game/sell/skins")
     * @Rest\View()
     * @return Response
     */
    public function postCasesUserSellSkinsAction(Request $request)
    {
        $domainId = $request->headers->get('x-domain-id');
        $currency = $this->getDoctrine()->getRepository(Currency::class)
            ->findOneBy(['code' => $request->headers->get('x-currency-code', 'RUB')]);

        if (!$currency) {
            $view = $this->view('Not valid currency code', 400);
            return $this->handleView($view);
        }

        $userId = $this->getUser()->getId();
        $gameService = $this->get('api_cases.service.games');
        if (!$gameService->checkGameUserId($userId)) {
            $view = $this->view("Not found game", 400);
            return $this->handleView($view);
        }

        try {
            $userBalance = $this->get('api_cases_pubg.service.cases_user_sell_skins')
                ->handler(
                    json_decode($gameService->getGame($userId), 1),
                    (int) $this->getUser()->getId(),
                    $domainId, $currency->getId()
                );

            $gameService->clearGame($userId);
            $view = $this->view(['balance' => $userBalance], 200);
        } catch (\Exception $e) {
            $this->get('logger')->error($e->getMessage());
            $view = $this->view($e->getMessage(), 400);
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
     * @SWG\Response(
     *     response=200,
     *     description="Забрать Продать предмет, выигранный пользователем",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=CasesSkinsPickUpUserPUBG::class, groups={"full"}))
     *     ),
     *     examples={
     *         "application/json": {
     *             "success"
     *         }
     *     }
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Если не передан код валюты или игра не прошла валидацию то ответ 400",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=CasesSkinsPickUpUserPUBG::class, groups={"full"}))
     *     ),
     *     examples={
     *         "application/json": {
     *             "400": "Bad request"
     *         }
     *     }
     * )
     *
     * @Rest\Post("/game/pick-up/skins")
     * @Rest\View()
     * @param Request $request
     * @return Response
     */
    public function postCasesUserPickUpSkinsAction(Request $request)
    {
        $domainId = $request->headers->get('x-domain-id');

        $userId = $this->getUser()->getId();
        $gameService = $this->get('api_cases.service.games');
        if (!$gameService->checkGameUserId($userId)) {
            $view = $this->view("Not found game", 400);
            return $this->handleView($view);
        }

        try {
            $this->get('api_cases_pubg.service.cases_user_pick_up_skins')
                ->handler(json_decode($gameService->getGame($userId), 1), $userId, $domainId);

            $gameService->clearGame($userId);
            $view = $this->view('success', 200);
        } catch (\Exception $e) {
            $this->get('logger')->error($e->getMessage());
            $view = $this->view($e->getMessage(), 400);
        }

        return $this->handleView($view);
    }
}