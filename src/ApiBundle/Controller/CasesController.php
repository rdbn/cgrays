<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\Cases;
use AppBundle\Entity\CasesSkins;
use AppBundle\Entity\CasesCategory;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CasesController extends FOSRestController
{
    /**
     * @Rest\Get("/cases/category")
     * @Rest\View(serializerGroups={"cases_category"})
     * @return Response
     */
    public function getCategoryCasesAction()
    {
        $categoryCases = $this->getDoctrine()->getRepository(CasesCategory::class)
            ->findAll();

        if (!count($categoryCases)) {
            $view = $this->view("Not found", 404);
            return $this->handleView($view);
        }

        $view = $this->view($categoryCases, 200);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @param int $categoryId
     *
     * @Rest\Get("/cases/{categoryId}", requirements={"categoryId": "\d+"})
     * @Rest\View(serializerGroups={"cases_list"})
     * @return Response
     */
    public function getCasesCategoryAction(Request $request, $categoryId)
    {
        $domainId = $request->headers->get('x-domain-id');
        $cases = $this->getDoctrine()->getRepository(Cases::class)
            ->findCasesByCategoryId($domainId, $categoryId);

        if (!count($cases)) {
            $view = $this->view("Not found", 404);
            return $this->handleView($view);
        }

        $listCases = [];
        foreach ($cases as $case) {
            /* @var Cases $case */
            $listEmptyCases = [];
            $countCasesSkins = $case->getCasesSkins()->count();
            foreach ($case->getCasesSkins() as $casesSkin) {
                /* @var CasesSkins $casesSkin */
                if ($casesSkin->getCountDrop() == $casesSkin->getCount()) {
                    $listEmptyCases[] = true;
                }
            }

            $listCases[] = [
                'id' => $case->getId(),
                'name' => $case->getName(),
                'image' => $case->getImage(),
                'price' => $case->getPrice(),
                'is_empty' => $countCasesSkins == count($listEmptyCases) ? true : false,
                'create_at' => $case->getCreatedAt()->format('Y-m-d H:i:s'),
            ];
        }

        $view = $this->view($listCases, 200);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @param int $casesId
     *
     * @Rest\Get("/cases/skins/{casesId}", requirements={"casesId": "\d+"})
     * @Rest\View()
     * @return Response
     */
    public function getCasesAction(Request $request, $casesId)
    {
        $domainId = $request->headers->get('x-domain-id');
        /* @var Cases $case */
        $case = $this->getDoctrine()->getRepository(Cases::class)
            ->findCasesSkinsByDomainIdAndCasesId($domainId, $casesId);

        if (!$case) {
            $view = $this->view("Not found", 404);
            return $this->handleView($view);
        }

        $skins = [
            'id' => $case->getId(),
            'name' => $case->getName(),
            'price' => $case->getPrice(),
            'image' => $case->getImage(),
            'created_at' => $case->getCreatedAt(),
        ];

        foreach ($case->getCasesSkins() as $key => $casesSkin) {
            /* @var CasesSkins $casesSkin */
            $skins['skins'][$key] = [
                'id' => $casesSkin->getSkins()->getId(),
                'name' => $casesSkin->getSkins()->getName(),
                'icon_url' => $casesSkin->getSkins()->getIconUrl(),
            ];

            $skins['skins'][$key]['is_empty'] = true;
            if ($casesSkin->getCount() > $casesSkin->getCountDrop()) {
                $skins['skins'][$key]['is_empty'] = false;
            }
        }

        $view = $this->view($skins, 200);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @param int $id
     * @Rest\Get("/cases/open/{id}", requirements={"id": "\d+"})
     * @Rest\View()
     * @return Response
     */
    public function getCasesOpenAction(Request $request, $id)
    {
        $domainId = $request->headers->get('x-domain-id');
        if (!$domainId) {
            $view = $this->view("Not found", 404);
            return $this->handleView($view);
        }

        try {
            $case = $this->get('api.service.case_open')
                ->handler($domainId, $id, $this->getUser()->getId());

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
     * @Rest\Post("/cases/user/sell/skins")
     * @Rest\View()
     * @return Response
     */
    public function postCasesUserSellSkinsAction(Request $request)
    {
        $domainId = $request->headers->get('x-domain-id');
        $skinsId = $request->request->get('skins_id');
        $currencyCode = $request->request->get('currency_code');
        if (!$domainId || !$currencyCode || !$skinsId) {
            $view = $this->view("Not found", 404);
            return $this->handleView($view);
        }

        // try {
            $this->get('api.service.cases_user_sell_skins')
                ->handler((int) $skinsId, (int) $this->getUser()->getId(), $domainId, $currencyCode);

            $view = $this->view('success', 200);
//        } catch (\Exception $e) {
//            $this->get('logger')->error($e->getMessage());
//            $view = $this->view('Bad request', 400);
//        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @Rest\Post("/cases/user/pick-up/skins")
     * @Rest\View()
     * @return Response
     */
    public function postCasesUserPickUpSkinsAction(Request $request)
    {
        $domainId = $request->headers->get('x-domain-id');
        $skinsId = $request->request->get('skins_id');
        if (!$domainId || !$skinsId) {
            $view = $this->view("Not found", 404);
            return $this->handleView($view);
        }

        try {
            $this->get('api.service.cases_user_pick_up_skins')
                ->handler($skinsId, $this->getUser()->getId());

            $view = $this->view('success', 200);
        } catch (\Exception $e) {
            $this->get('logger')->error($e->getMessage());
            $view = $this->view('Bad Request', 400);
        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @Rest\Post("/cases/contract")
     * @return Response
     */
    public function postCasesContractAction(Request $request)
    {
        $domainId = $request->headers->get('x-domain-id');
        $ids = $request->request->get('ids');
        $currencyCode = $request->request->get('currency_code');
        if (!$domainId || !$ids || !$currencyCode) {
            $view = $this->view("Not found", 404);
            return $this->handleView($view);
        }

        try {
            $this->get('api.service.cases_contract')
                ->handler($ids, $domainId, $this->getUser()->getId(), $currencyCode);

            $view = $this->view('success', 200);
        } catch (\Exception $e) {
            $view = $this->view('Bad request', 400);
        }

        return $this->handleView($view);
    }
}
