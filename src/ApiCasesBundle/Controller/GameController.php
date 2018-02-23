<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 07.12.2017
 * Time: 11:03
 */

namespace ApiCasesBundle\Controller;

use AppBundle\Entity\CasesSkinsPickUpUser;
use AppBundle\Entity\Currency;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GameController extends FOSRestController
{
    /**
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
            ->findOneBy(['code' => $request->headers->get('x-currency-code')]);

        if (!$currency) {
            $view = $this->view('Не верный код валюты.', 404);
            return $this->handleView($view);
        }

//        $userId = $this->getUser()->getId();
//        $gameService = $this->get('api_cases.service.games');
//        if ($gameService->checkGameUserId($userId)) {
//            $view = $this->view(['error' => 'Game is not end'], 400);
//            return $this->handleView($view);
//        }

        try {
            $case = $this->get('api_cases.service.case_open')
                ->handler($domainId, $casesId, $this->getUser()->getId(), $currency->getId());

            $view = $this->view($case, 200);
        } catch (\Exception $e) {
            $this->get('logger')->error($e->getMessage());
            $view = $this->view('Bad request', 400);
        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @Rest\Post("/game/sell/skins")
     * @Rest\View()
     * @return Response
     */
    public function postCasesUserSellSkinsAction(Request $request)
    {
        $domainId = $request->headers->get('x-domain-id');
        $currency = $this->getDoctrine()->getRepository(Currency::class)
            ->findOneBy(['code' => $request->headers->get('x-currency-code')]);

        if (!$currency) {
            $view = $this->view('Не верный код валюты.', 404);
            return $this->handleView($view);
        }

        $userId = $this->getUser()->getId();
        $gameService = $this->get('api_cases.service.games');
        if (!$gameService->checkGameUserId($userId)) {
            $view = $this->view("Not found", 404);
            return $this->handleView($view);
        }

        try {
            $userBalance = $this->get('api_cases.service.cases_user_sell_skins')
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
            $view = $this->view("Not found", 404);
            return $this->handleView($view);
        }

//        try {
            $this->get('api_cases.service.cases_user_pick_up_skins')
                ->handler(json_decode($gameService->getGame($userId), 1), $userId, $domainId);

            $gameService->clearGame($userId);
            $view = $this->view('success', 200);
//        } catch (\Exception $e) {
//            $this->get('logger')->error($e->getMessage());
//            $view = $this->view($e->getMessage(), 400);
//        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @Rest\Post("/game/contract")
     * @return Response
     */
    public function postCasesContractAction(Request $request)
    {
        $domainId = $request->headers->get('x-domain-id');
        $currency = $this->getDoctrine()->getRepository(Currency::class)
            ->findOneBy(['code' => $request->headers->get('x-currency-code')]);

        if (!$currency) {
            $view = $this->view('Не верный код валюты.', 404);
            return $this->handleView($view);
        }

        $ids = $request->request->get('ids');
        if (!$ids) {
            $view = $this->view("Not found", 404);
            return $this->handleView($view);
        }

        try {
            $this->get('api_cases.service.cases_contract')
                ->handler($ids, $domainId, $this->getUser()->getId(), $currency->getId());

            $view = $this->view('success', 200);
        } catch (\Exception $e) {
            $this->get('logger')->error($e->getMessage());
            $view = $this->view($e->getMessage(), 400);
        }

        return $this->handleView($view);
    }
}