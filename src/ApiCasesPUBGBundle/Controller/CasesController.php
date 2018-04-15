<?php

namespace ApiCasesPUBGBundle\Controller;

use AppBundle\Entity\Cases;
use AppBundle\Entity\CasesSkinsPUBG;
use AppBundle\Entity\CasesCategory;
use AppBundle\Services\Helper\MbStrimWidthHelper;
use Doctrine\DBAL\DBALException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CasesController extends FOSRestController
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
     *     description="Возвращает список кесов и категорий.",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Cases::class, groups={"full"}))
     *     ),
     *     examples={
     *         "application/json": {
     *             "category": {
     *                 {
     *                     "id": 1,
     *                     "name": "Test5a90579b91c00",
     *                     "sort_number": 1
     *                 }
     *             },
     *             "cases": {
     *                 {
     *                     "id": 12,
     *                     "name": "Test5a90579c9...",
     *                     "image": "image/1b02da96951e3f12e4c76159adf46d72.png",
     *                     "price": 17,
     *                     "promotion_price": 5,
     *                     "created_at": "2018-02-23 21:04:12+00",
     *                     "category_id": 1
     *                 }
     *             }
     *         }
     *     }
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Если список кейсов пуст то ответ 400",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Cases::class, groups={"full"}))
     *     ),
     *     examples={
     *         "application/json": {
     *             "400": "Cases list empty"
     *         }
     *     }
     * )
     *
     * @param Request $request
     *
     * @Rest\Get("/list")
     * @Rest\View()
     * @return Response
     */
    public function getListAction(Request $request)
    {
        $domainId = $request->headers->get('x-domain-id');
        $em = $this->getDoctrine()->getManager();
        $cases = $em->getRepository(Cases::class)
            ->findCasesByCategoryId($domainId);

        if (!count($cases)) {
            $view = $this->view('Cases list empty', 400);
            return $this->handleView($view);
        }

        $listCases = [];
        $listCases['category'] = $em->getRepository(CasesCategory::class)
            ->findAllCasesDomain($domainId);

        $listCases['cases'] = array_map(function ($item) {
            $item['name'] = mb_strimwidth($item['name'], 0, 16, '...', 'utf-8');
            $item['price'] = round($item['price'], 0);
            $item['promotion_price'] = round($item['promotion_price'], 0);

            return $item;
        }, $cases);

        $view = $this->view($listCases, 200);
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
     *     name="casesId",
     *     in="path",
     *     type="string",
     *     description="casesId - ID страницы, \d+",
     *     required=true
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Возвращает информацию по кейс и список скинов",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Cases::class, groups={"full"}))
     *     ),
     *     examples={
     *         "application/json": {
     *             "id": 186,
     *             "name": "test_pubg_123",
     *             "price": "6.00",
     *             "image": "image/d73f883da8d866fff307a91275cbb390.png",
     *             "created_at": "2018-04-14 14:31:35+00",
     *             "skins": {
     *                 {
     *                     "id": 62,
     *                     "skin_name": "Camo Combat ...",
     *                     "steam_image": "/image/1b02da96951e3f12e4c76159adf46d72.png",
     *                     "rarity": 7
     *                 },
     *                 {
     *                     "id": 63,
     *                     "skin_name": "Fingerless G...",
     *                     "steam_image": "/image/317e0184bcc510d9e3f593802bdd4c37.png",
     *                     "rarity": 6
     *                 }
     *             }
     *         }
     *     }
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Если кейс не найден или список скинов пуст то ответ 400",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=Cases::class, groups={"full"}))
     *     ),
     *     examples={
     *         "application/json": {
     *             "400": "Case not found"
     *         }
     *     }
     * )
     *
     * @param Request $request
     * @param int $casesId
     *
     * @Rest\Get("/skins/{casesId}", requirements={"casesId": "\d+"})
     * @Rest\View()
     * @return Response
     */
    public function getCaseAction(Request $request, $casesId)
    {
        $domainId = $request->headers->get('x-domain-id');
        $em = $this->getDoctrine()->getManager();
        try {
            $case = $em->getRepository(Cases::class)
                ->findCasesSkinsByDomainIdAndCasesId($domainId, $casesId);
        } catch (DBALException $e) {
            $this->get('logger')->error($e->getMessage());
            $case = false;
        }

        if (!$case) {
            $view = $this->view("Case not found", 400);
            return $this->handleView($view);
        }

        try {
            $case['skins'] = $em->getRepository(CasesSkinsPUBG::class)
                ->findAllSkinsByCasesId($domainId, $casesId);
        } catch (DBALException $e) {
            $this->get('logger')->error($e->getMessage());
            $view = $this->view("Case not found", 400);
            return $this->handleView($view);
        }

        $case['skins'] = array_map(function ($item) {
            return [
                'id' => $item['id'],
                'skin_name' => MbStrimWidthHelper::strimWidth($item['name']),
                'steam_image' => "/{$item['icon_url']}",
                'rarity' => $item['rarity_id'],
            ];
        }, $case['skins']);

        $user = $this->getUser();
        if ($user) {
            $this->get('api_cases.service.metrics_event_sender')->sender([
                'user_id' => $user->getId(),
                'cases_id' => $casesId,
                'cases_domain_id' => $case['cases_domain_id'],
                'cases_category_id' => $case['cases_category_id'],
                'event_type' => 'hits',
            ]);
        }

        unset($case['cases_domain_id'], $case['cases_category_id']);
        $view = $this->view($case, 200);
        return $this->handleView($view);
    }
}
