<?php

namespace ApiCasesBundle\Controller;

use AppBundle\Entity\Cases;
use AppBundle\Entity\CasesSkins;
use AppBundle\Entity\CasesCategory;
use AppBundle\Entity\CasesSkinsDropUser;
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
            ->findLastSkinsDrop();

        $view = $this->view($listSkins, 200);
        return $this->handleView($view);
    }
}
