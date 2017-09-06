<?php

namespace AppBundle\Controller;

use AppBundle\Entity\StaticPage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class StaticPageController extends Controller
{
    public function listNamePageAction()
    {
        $listNames = $this->getDoctrine()->getRepository(StaticPage::class)
            ->findAll();

        $namePages = [];
        foreach ($listNames as $listName) {
            /* @var StaticPage $listName */
            $key = $listName->getTypePage();
            $namePages[$key] = StaticPage::TYPE_PAGE[$key];
        }

        return $this->render(':default/static_page_name:static_name_list.html.twig', [
            'namePages' => $namePages,
        ]);
    }

    /**
     * @param string $name
     *
     * @Route("/page/{name}", name="app.static_page.page")
     * @return Response
     */
    public function pageAction($name)
    {
        $page = $this->getDoctrine()->getRepository(StaticPage::class)
            ->findOneBy(['typePage' => $name]);

        return $this->render(':default:page.html.twig', [
            'page' => $page
        ]);
    }
}
