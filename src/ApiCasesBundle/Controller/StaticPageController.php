<?php

namespace ApiCasesBundle\Controller;

use AppBundle\Entity\CasesStaticPage;
use Doctrine\ORM\NonUniqueResultException;
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
        try {
            $staticPage = $this->getDoctrine()->getRepository(CasesStaticPage::class)
                ->findStaticPageByDomainIdAndPageName($domainId, $typePage);

            $view = $this->view($staticPage, 200);
        } catch (NonUniqueResultException $e) {
            $view = $this->view('Non unique result.', 400);
        }

        return $this->handleView($view);
    }
}
