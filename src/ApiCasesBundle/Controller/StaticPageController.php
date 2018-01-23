<?php

namespace ApiCasesBundle\Controller;

use AppBundle\Entity\CasesStaticPage;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StaticPageController extends FOSRestController
{
    /**
     * @param Request $request
     * @param string $typePage
     *
     * @Rest\Get("/static/{typePage}")
     * @Rest\View()
     * @return Response
     */
    public function getPageAction(Request $request, $typePage)
    {
        $domainId = $request->headers->get('x-domain-id');
        $staticPage = $this->getDoctrine()->getRepository(CasesStaticPage::class)
            ->findStaticPageByDomainIdAndPageName($domainId, $typePage);

        $view = $this->view(array_shift($staticPage), 200);
        return $this->handleView($view);
    }
}
