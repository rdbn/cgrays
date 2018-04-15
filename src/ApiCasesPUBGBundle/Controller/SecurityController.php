<?php

namespace ApiCasesPUBGBundle\Controller;

use AppBundle\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityController extends FOSRestController
{
    /**
     * @SWG\Parameter(
     *     name="x-domain-id",
     *     in="header",
     *     type="string",
     *     description="UIID - уникальный ключь для индентификации домена",
     *     required=true
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Авторизация, используется только в примерах документации",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=User::class, groups={"full"}))
     *     ),
     *     examples={
     *         "application/json": {
     *             "token": "Bearer {hash_key}"
     *         }
     *     }
     * )
     * @param Request $request
     * @Rest\Post("/login_check")
     * @Rest\View()
     * @return Response
     */
    public function postLoginCheckAction(Request $request)
    {
        $idDefaultUser = $this->getParameter('id_default_user');

        $user = $this->getDoctrine()->getRepository(User::class)
            ->findOneBy(['id' => $idDefaultUser]);

        $token = $this->get('lexik_jwt_authentication.jwt_manager')
            ->create($user);

        $view = $this->view("Bearer {$token}", 200);
        return $this->handleView($view);
    }
}
