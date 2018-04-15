<?php

namespace ApiCasesPUBGBundle\Controller;

use AppBundle\Entity\CasesStaticPage;
use Doctrine\ORM\NonUniqueResultException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class StaticPageController extends FOSRestController
{
    /**
     * @SWG\Parameter(
     *     name="x-domain-id",
     *     in="header",
     *     type="string",
     *     description="UIID - уникальный ключь для индентификации домена",
     *     required=true
     * )
     * @SWG\Parameter(
     *     name="typePage",
     *     in="path",
     *     type="string",
     *     description="typePage - название типа страницы, vacancies|information|faq|termsOfUse",
     *     required=true
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Получение страницы с доп. информацией",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=CasesStaticPage::class, groups={"full"}))
     *     ),
     *     examples={
     *         "application/json": {
     *             "skins": {
     *                 "typePage": "faq",
     *                 "text": "Правила покупки на сайте...."
     *             }
     *         }
     *     }
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Если страницы нет то вернет 400",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=CasesStaticPage::class, groups={"full"}))
     *     ),
     *     examples={
     *         "application/json": {
     *             "400": "Static page not found"
     *         }
     *     }
     * )
     *
     * @param Request $request
     * @param string $typePage
     *
     * @Rest\Get("/static/{typePage}", requirements={"typePage": "vacancies|information|faq|termsOfUse"})
     * @Rest\View()
     * @return Response
     */
    public function getPageAction(Request $request, $typePage)
    {
        $domainId = $request->headers->get('x-domain-id');
        try {
            $staticPage = $this->getDoctrine()->getRepository(CasesStaticPage::class)
                ->findStaticPageByDomainIdAndPageName($domainId, $typePage);

            if (is_array($staticPage)) {
                $view = $this->view($staticPage, 200);
            } else {
                $view = $this->view('Static page not found', 400);
            }
        } catch (NonUniqueResultException $e) {
            $view = $this->view('Static page not found.', 400);
        }

        return $this->handleView($view);
    }
}
