<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\ProductPrice;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends FOSRestController
{
    /**
     * @param Request $request
     *
     * @Rest\Get("/user/steam/inventory")
     * @Rest\View()
     * @return Response
     */
    public function getUserSteamInventoryAction(Request $request)
    {
        $page = $request->query->getInt('page', 1);

        try {
            $inventory = $this->get('app.service.steam_user_inventory')
                ->handler($this->getUser(), $page);

            $view = $this->view($inventory, 200);
        } catch (\Exception $e) {
            $view = $this->view('Bad request', 400);
        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     *
     * @Rest\Post("/user/steam/add")
     * @Rest\View()
     * @return Response
     */
    public function postSaveProductAction(Request $request)
    {
        $itemSell = $request->request->get('item');
        if (is_array($itemSell)) {
            $productPriceSell =  $this->get('app.service.add_product_sell')
                ->handler($this->getUser(), $itemSell);

            $view = $this->view([
                'product_id' => $productPriceSell->getProduct()->getId(),
                'product_price_id' => $productPriceSell->getId(),
                'icon_url' => $productPriceSell->getProduct()->getIconUrl(),
                'name' => $productPriceSell->getProduct()->getName(),
                'price' => $productPriceSell->getPrice(),
            ], 200);
        } else {
            $view = $this->view('Bad request', 400);
        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     *
     * @Rest\Post("/user/item/update/price")
     * @Rest\View()
     * @return Response
     */
    public function postUpdatePriceAction(Request $request)
    {
        $itemPrice = $request->request->get('item');
        if (is_array($itemPrice)) {
            $em = $this->getDoctrine()->getManager();
            $productPrice = $em->getRepository(ProductPrice::class)
                ->findOneBy(['id' => $itemPrice['id']]);

            $productPrice->setPrice($itemPrice['price']);
            $em->flush();

            $view = $this->view('success', 200);
        } else {
            $view = $this->view('Bad request', 400);
        }

        return $this->handleView($view);
    }

    /**
     *
     * @Rest\Post("/user/refresh/inventory")
     * @Rest\View()
     * @return Response
     */
    public function postRefreshInventoryAction()
    {
        $userId = $this->getUser();
        $redis = $this->get('snc_redis.default_client');
        $redis->del(["user:inventory:{$userId}", "user:inventory:{$userId}:start_id"]);

        $view = $this->view('success', 200);
        return $this->handleView($view);
    }

    /**
     * @param Request $request
     *
     * @Rest\Post("/user/isSell")
     * @Rest\View()
     * @return Response
     */
    public function postSellAction(Request $request)
    {
        $isSell = (bool) $request->request->get('isSell');

        $user = $this->getUser();
        if ($user->getIsOnline()) {
            $user->setIsSell($isSell);
            $this->getDoctrine()->getManager()->flush();

            $view = $this->view("success", 200);
        } else {
            $view = $this->view("User is not online", 400);
        }

        return $this->handleView($view);
    }
}
