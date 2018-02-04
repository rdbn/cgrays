<?php

namespace ApiBundle\Controller;

use AdminBundle\Form\CasesFormFilterType;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminCasesController extends FOSRestController
{
    /**
     * @param Request $request
     * @param $casesId
     *
     * @Rest\Get("/admin/cases/show-list-skins/{casesId}", requirements={"casesId": "\d+"}, defaults={"casesId": 0})
     * @Rest\View()
     * @return Response
     */
    public function getShowListSkinsAction(Request $request, $casesId = 0)
    {
        $form = $this->createForm(CasesFormFilterType::class);
        $form->handleRequest($request);

        $filters = [];
        foreach ($form->getIterator() as $name => $item) {
            $filters[$name] = $item->getData();
        }

        $listCasesSkins = $this->get('admin.service.cases_list')
            ->getList(
                $casesId,
                $request->query->getInt('offset', 0),
                $request->query->getInt('limit', 18),
                $filters
            );

        $view = $this->view($listCasesSkins, 200);
        return $this->handleView($view);
    }
}
