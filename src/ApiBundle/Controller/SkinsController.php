<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\SkinsPrice;
use AppBundle\Form\SkinsFilterTypeForm;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SkinsController extends FOSRestController
{
    /**
     * @param Request $request
     *
     * @Rest\View()
     * @return Response
     */
    public function getSkinsAllAction(Request $request)
    {
        $sort = $request->query->get('sort');
        $order = $request->query->get('order');

        $form = $this->createForm(SkinsFilterTypeForm::class);
        $form->handleRequest($request);

        $data = [];
        if ($form->isSubmitted()) {
            foreach ($form->getIterator() as $name => $item) {
                $data[$name] = $item->getData();
            }
        }

        $skins = $this->getDoctrine()->getRepository(SkinsPrice::class)
            ->querySkinsByFilters($data, $sort, $order);

        $paginator = $this->get("knp_paginator");
        $pagination = $paginator->paginate(
            $skins,
            $request->query->getInt("page", 1),
            $request->query->getInt("limit", 12)
        );

        $skins = [];
        foreach ($pagination as $item) {
            $skins[] = $item;
        }

        $view = $this->view($skins, 200);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @Rest\Get("/skins/price/{id}", requirements={"id": "\d+"})
     * @Rest\View()
     * @return Response
     */
    public function getSkinsPriceAction(Request $request, $id)
    {
        $skins = $this->getDoctrine()->getRepository(SkinsPrice::class)
            ->querySkinsPrice($id);

        $paginator = $this->get("knp_paginator");
        $pagination = $paginator->paginate(
            $skins,
            $request->query->getInt("page", 1),
            $request->query->getInt("limit", 12)
        );

        $skins = [];
        foreach ($pagination as $item) {
            $skins[] = $item;
        }

        $view = $this->view($skins, 200);
        return $this->handleView($view);
    }

    /**
     * @param int $skinsPriceId
     *
     * @Rest\Get("/skins/{skinsPriceId}", requirements={"skinsPriceId": "\d+"})
     * @Rest\View(serializerGroups={"skins"})
     * @return Response
     */
    public function getSkinsAction($skinsPriceId)
    {
        $skinsPriceId = $this->getDoctrine()->getRepository(SkinsPrice::class)
            ->findSkinsPriceById($skinsPriceId);

        $view = $this->view($skinsPriceId, 200);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     *
     * @Rest\Post("/skins/add")
     * @Rest\View()
     * @return Response
     */
    public function postSaveSkinsAction(Request $request)
    {
        $itemSell = $request->request->get('item');
        $validator = $this->get('api.service.validator_item_sell')
            ->handler($itemSell);

        if ($validator->isValid()) {
            $skinsPriceItem =  $this->get('app.service.save_sell_skins_handler')
                ->handler($this->getUser(), $itemSell, 730);

            $view = $this->view([
                'skins_price_id' => $skinsPriceItem['id'],
                'icon_url' => $skinsPriceItem['icon_url'],
                'name' => $skinsPriceItem['name'],
                'price' => $skinsPriceItem['price'],
            ], 200);
        } else {
            $view = $this->view($validator->getMessage(), 400);
        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     *
     * @Rest\Post("/skins/update/price")
     * @Rest\View()
     * @return Response
     */
    public function postUpdatePriceAction(Request $request)
    {
        $itemPrice = $request->request->get('item');
        if (is_array($itemPrice)) {
            $em = $this->getDoctrine()->getManager();
            $skinsPrice = $em->getRepository(SkinsPrice::class)
                ->findOneBy(['id' => $itemPrice['id']]);

            $skinsPrice->setPrice($itemPrice['price']);
            $em->flush();

            $view = $this->view('success', 200);
        } else {
            $view = $this->view('Bad request', 400);
        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     *
     * @Rest\Post("/skins/remove")
     * @Rest\View()
     * @return Response
     */
    public function postRemoveSkinsAction(Request $request)
    {
        $id = (int) $request->request->get('id');

        $em = $this->getDoctrine()->getManager();
        $skins = $em->getRepository(SkinsPrice::class)
            ->findOneBy(['id' => $id]);

        if ($skins) {
            $skins->setIsSell(false);
            $em->flush();

            $view = $this->view("success", 200);
        } else {
            $view = $this->view("Bad request", 400);
        }

        return $this->handleView($view);
    }
}
