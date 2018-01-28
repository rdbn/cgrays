<?php

namespace ApiCasesBundle\Controller;

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

        $listCases['cases'] = $cases;

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
                'skin_name' => mb_strimwidth($casesSkin->getSkins()->getName(), 0, 15, '...', 'utf-8'),
                'steam_image' => "/{$casesSkin->getSkins()->getIconUrl()}",
                'weapon_name' => $casesSkin->getSkins()->getWeapon()->getLocalizedTagName(),
                'rarity' => $casesSkin->getSkins()->getRarity()->getLocalizedTagName(),
            ];

            $skins['skins'][$key]['is_empty'] = true;
            if ($casesSkin->getCount() > $casesSkin->getCountDrop()) {
                $skins['skins'][$key]['is_empty'] = false;
            }
        }

        $view = $this->view($skins, 200);
        return $this->handleView($view);
    }
}
