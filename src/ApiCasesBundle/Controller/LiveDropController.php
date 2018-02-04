<?php

namespace ApiCasesBundle\Controller;

use AppBundle\Entity\Cases;
use AppBundle\Entity\CasesSkins;
use AppBundle\Entity\CasesCategory;
use AppBundle\Entity\CasesSkinsDropUser;
use AppBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LiveDropController extends FOSRestController
{
    /**
     * @param Request $request
     *
     * @Rest\Get("/live-drop")
     * @Rest\View()
     * @return Response
     */
    public function getListAction(Request $request)
    {
        $domainId = $request->headers->get('x-domain-id');
        $listSkins = $this->getDoctrine()->getRepository(CasesSkinsDropUser::class)
            ->findLastSkinsDrop($domainId);

        $listSkins = array_map(function ($item) {
            $item['skin_name'] = mb_strimwidth($item['skin_name'],  0, 15, '...', 'utf-8');
            return $item;
        }, $listSkins);

        $countSkinsDrop = $this->getDoctrine()->getRepository(CasesSkinsDropUser::class)
            ->getCountSkinsDrop($domainId);

        $countUser = $this->getDoctrine()->getRepository(User::class)
            ->getCountUserByDomainId($domainId);

        return $this->handleView($this->view(
            ['skins' => $listSkins, 'count_skins_drop' => $countSkinsDrop, 'count_user' => $countUser],
            200
        ));
    }
}