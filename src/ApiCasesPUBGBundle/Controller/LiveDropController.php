<?php

namespace ApiCasesPUBGBundle\Controller;

use AppBundle\Entity\CasesSkinsDropUserPUBG;
use AppBundle\Entity\User;
use AppBundle\Services\Helper\MbStrimWidthHelper;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LiveDropController extends FOSRestController
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
     *     description="Список скинов выпавших пользователем с информацией о кейсе и пользователе",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=CasesSkinsDropUserPUBG::class, groups={"full"}))
     *     ),
     *     examples={
     *         "application/json": {
     *             "skins": {
     *                 {
     *                     "avatar": "image/1a420b23bafb9fcc6b0096522d17646d.png",
     *                     "cases_image": "/image/ddfa66680e2455bb344af5a78762ea98.png",
     *                     "created_at": "2018-04-14 17:50:01+02",
     *                     "rarity_id": 15,
     *                     "skin_name": "Camo Combat ...",
     *                     "skins_drop_id": 1,
     *                     "steam_image": "/image/3042e2e7894a6c6e52896bea363d2f48.png",
     *                     "user_id": 9,
     *                     "username": "username"
     *                 },
     *                 {
     *                     "avatar": "image/1a420b23bafb9fcc6b0096522d17646d.png",
     *                     "cases_image": "/image/ddfa66680e2455bb344af5a78762ea98.png",
     *                     "created_at": "2018-04-14 17:50:01+02",
     *                     "rarity_id": 15,
     *                     "skin_name": "Fingerless G...",
     *                     "skins_drop_id": 2,
     *                     "steam_image": "/image/3042e2e7894a6c6e52896bea363d2f48.png",
     *                     "user_id": 9,
     *                     "username": "username"
     *                 }
     *             },
     *             "count_skins_drop": 0,
     *             "count_user": 6
     *         }
     *     }
     * )
     *
     * @param Request $request
     *
     * @Rest\Get("/live-drop")
     * @Rest\View()
     * @return Response
     */
    public function getListAction(Request $request)
    {
        $domainId = $request->headers->get('x-domain-id');
        $listSkins = $this->getDoctrine()->getRepository(CasesSkinsDropUserPUBG::class)
            ->findLastSkinsDrop($domainId);

        $listSkins = array_map(function ($item) {
            $item['skin_name'] = MbStrimWidthHelper::strimWidth($item['skin_name']);
            $item['steam_image'] = "/{$item['steam_image']}";
            $item['cases_image'] = "/{$item['cases_image']}";

            return $item;
        }, $listSkins);

        $countSkinsDrop = $this->getDoctrine()->getRepository(CasesSkinsDropUserPUBG::class)
            ->getCountSkinsDrop($domainId);

        $countUser = $this->getDoctrine()->getRepository(User::class)
            ->getCountUserByDomainId($domainId);

        return $this->handleView($this->view(
            ['skins' => $listSkins, 'count_skins_drop' => $countSkinsDrop, 'count_user' => $countUser],
            200
        ));
    }


}
