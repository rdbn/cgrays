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
     *
     * @Rest\Get("/admin/cases/show-list-skins")
     * @Rest\View()
     * @return Response
     */
    public function getShowListSkinsAction(Request $request)
    {
        $form = $this->createForm(CasesFormFilterType::class);
        $form->handleRequest($request);

        $filters = [];
        foreach ($form->getIterator() as $name => $item) {
            $filters[$name] = $item->getData();
        }

        $listCasesSkins = $this->get('admin.service.cases_list')
            ->getList(
                $request->query->getInt('casesId', null),
                $request->query->getInt('offset', 0),
                $request->query->getInt('limit', 18),
                $filters
            );

        $view = $this->view($listCasesSkins, 200);
        return $this->handleView($view);
    }
}
