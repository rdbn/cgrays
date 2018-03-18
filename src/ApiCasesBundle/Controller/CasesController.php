<?php

namespace ApiCasesBundle\Controller;

use AppBundle\Entity\Cases;
use AppBundle\Entity\CasesSkins;
use AppBundle\Entity\CasesCategory;
use AppBundle\Services\Helper\MbStrimWidthHelper;
use Doctrine\DBAL\DBALException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CasesController extends FOSRestController
{
    /**
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
            $view = $this->view("Not found", 404);
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
            $view = $this->view("Not found", 404);
            return $this->handleView($view);
        }

        $case['skins'] = $em->getRepository(CasesSkins::class)
            ->findSkinsByCasesId($domainId, $casesId);

        $case['skins'] = array_map(function ($item) {
            return [
                'id' => $item['id'],
                'skin_name' => MbStrimWidthHelper::strimWidth($item['name']),
                'steam_image' => "/{$item['icon_url']}",
                'weapon_name' => $item['weapon'],
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
