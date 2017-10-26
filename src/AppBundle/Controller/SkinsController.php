<?php

namespace AppBundle\Controller;

use AppBundle\Entity\SkinsPrice;
use AppBundle\Entity\SkinsTrade;
use AppBundle\Form\SkinsFilterTypeForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SkinsController extends Controller
{
    /**
     * @param Request $request
     *
     * @Route("/", name="app.skins.main")
     * @return Response
     */
    public function mainAction(Request $request)
    {
        $sort = $request->query->get('sort');
        $order = $request->query->get('order');

        $form = $this->createForm(SkinsFilterTypeForm::class, null, [
            'action' => $this->generateUrl('app.skins.main')
        ]);
        $form->handleRequest($request);

        $data = [];
        if ($form->isSubmitted()) {
            foreach ($form->getIterator() as $name => $item) {
                $data[$name] = $item->getData();
            }

            if (isset($data['name'])) {
                $data['name'] = preg_replace("/[^a-zа-яё\d]+/i", '', $data['name']);
            }
        }

        $products = $this->getDoctrine()->getRepository(SkinsPrice::class)
            ->querySkinsByFilters($data, $sort, $order);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $products,
            $request->query->getInt("page", 1),
            $request->query->getInt("limit", 24)
        );

        return $this->render('default/skins.html.twig', [
            "form" => $form->createView(),
            "pagination" => $pagination,
        ]);
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @Route("/skins/{id}", name="app.skins.skins_price")
     * @return Response
     */
    public function skinsPriceAction(Request $request, $id)
    {
        $sort = $request->query->get('sort');
        $order = $request->query->get('order');

        $products = $this->getDoctrine()->getRepository(SkinsPrice::class)
            ->querySkinsPrice($id, $sort, $order);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $products,
            $request->query->getInt("page", 1),
            $request->query->getInt("limit", 12)
        );

        $form = $this->createForm(SkinsFilterTypeForm::class, null, [
            'action' => $this->generateUrl('app.skins.main')
        ]);

        return $this->render(":default:skins.html.twig", [
            "form" => $form->createView(),
            "pagination" => $pagination,
        ]);
    }

    /**
     * @param int $skinsPriceId
     *
     * @Route("/skins/price/{skinsPriceId}", name="app.skins.skin")
     * @return Response
     */
    public function productAction($skinsPriceId)
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();

        /* @var SkinsPrice $skin */
        $skin = $em->getRepository(SkinsPrice::class)
            ->findSkinsPriceById($skinsPriceId);

        $skins = $em->getRepository(SkinsPrice::class)
            ->findSkinsPriceBySkinsId($skin->getSkins()->getId(), $skinsPriceId);

        if ($user) {
            $countSkinsTrade = $em->getRepository(SkinsTrade::class)
                ->findCountSkinsTradeByUserId($user->getId());

            $checkSkinsTradeUser = $em->getRepository(SkinsTrade::class)
                ->findOneBy(['skinsPrice' => $skinsPriceId, 'users' => $user]);
        }

        return $this->render('default/skin.html.twig', [
            'checkSkinsTradeUser' => empty($checkSkinsTradeUser) ? true : false,
            'countSkinsTrade' => isset($countSkinsTrade) ? $countSkinsTrade : 0,
            'skins' => $skins,
            'skin' => $skin,
        ]);
    }
}
