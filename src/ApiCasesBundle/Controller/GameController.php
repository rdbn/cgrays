<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 07.12.2017
 * Time: 11:03
 */

namespace ApiCasesBundle\Controller;

use ApiBundle\Validator\CurrencyConstraint;
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
        try {
            $case = $this->get('api_cases.service.case_open')
                ->handler($domainId, $casesId, $this->getUser()->getId());

            if (!count($case)) {
                throw new \Exception('Case is empty');
            }

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
        $currencyCode = $request->request->get('currency_code');
        $currencyConstraint = new CurrencyConstraint();
        $validator = $this->get('validator')->validate($currencyCode, $currencyConstraint);
        if (count($validator) > 0) {
            $view = $this->view($validator->get(0)->getMessage(), 404);
            return $this->handleView($view);
        }

        $userId = $this->getUser()->getId();
        $gameService = $this->get('api_cases.service.games');
        if (!$gameService->checkGameUserId($userId)) {
            $view = $this->view("Not found", 404);
            return $this->handleView($view);
        }

        try {
            $resultGame = json_decode($gameService->getGame($userId), 1);
            $this->get('api_cases.service.cases_user_sell_skins')
                ->handler($resultGame, (int) $this->getUser()->getId(), $domainId, $currencyCode);

            $gameService->clearGame($userId);
            $view = $this->view('success', 200);
        } catch (\Exception $e) {
            $this->get('logger')->error($e->getMessage());
            $view = $this->view($e->getMessage(), 400);
        }

        return $this->handleView($view);
    }

    /**
     * @Rest\Post("/game/pick-up/skins")
     * @Rest\View()
     * @return Response
     */
    public function postCasesUserPickUpSkinsAction()
    {
        $userId = $this->getUser()->getId();
        $gameService = $this->get('api_cases.service.games');
        if (!$gameService->checkGameUserId($userId)) {
            $view = $this->view("Not found", 404);
            return $this->handleView($view);
        }

        try {
            $resultGame = json_decode($gameService->getGame($userId), 1);
            $this->get('api_cases.service.cases_user_pick_up_skins')
                ->handler($resultGame, $userId);

            $gameService->clearGame($userId);
            $view = $this->view('success', 200);
        } catch (\Exception $e) {
            $this->get('logger')->error($e->getMessage());
            $view = $this->view($e->getMessage(), 400);
        }

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
        $currencyCode = $request->request->get('currency_code');
        $currencyConstraint = new CurrencyConstraint();
        $validator = $this->get('validator')->validate($domainId, $currencyConstraint);
        if (count($validator) > 0) {
            $view = $this->view($validator->get(0)->getMessage(), 404);
            return $this->handleView($view);
        }

        $ids = $request->request->get('ids');
        if (!$ids) {
            $view = $this->view("Not found", 404);
            return $this->handleView($view);
        }

        try {
            $this->get('api_cases.service.cases_contract')
                ->handler($ids, $domainId, $this->getUser()->getId(), $currencyCode);

            $view = $this->view('success', 200);
        } catch (\Exception $e) {
            $this->get('logger')->error($e->getMessage());
            $view = $this->view($e->getMessage(), 400);
        }

        return $this->handleView($view);
    }
}