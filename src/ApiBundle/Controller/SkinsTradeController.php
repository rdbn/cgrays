<?php

namespace ApiBundle\Controller;

use ApiBundle\Validator\SkinsPriceIdConstraint;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

class SkinsTradeController extends FOSRestController
{
    /**
     * @param Request $request
     *
     * @Rest\Post("/order/add")
     * @Rest\View()
     * @return string
     */
    public function postOrderAddAction(Request $request)
    {
        $skinsPriceId = $request->request->get('skins_price_id');
        $skinsPriceIdConstraint = new SkinsPriceIdConstraint();
        $validator = $this->get('validator')
            ->validate($skinsPriceId, $skinsPriceIdConstraint);

        if (!count($validator)) {
            $view = $this->view($validator->get(0)->getMessage(), 400);
            return $this->handleView($view);
        }

        try {
            $countSkinsTradeUser = $this->get('api.service.add_skins_trade')
                ->handler($skinsPriceId, $this->getUser()->getId());

            $view = $this->view($countSkinsTradeUser, 200);
        } catch (\Exception $e) {
            $this->get('logger')->error($e->getMessage());
            $view = $this->view("Bad request", 400);
        }

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     *
     * @Rest\Post("/order/remove")
     * @Rest\View()
     * @return string
     */
    public function postOrderRemoveAction(Request $request)
    {
        $skinsPriceId = $request->request->get('skins_price_id');
        $skinsPriceIdConstraint = new SkinsPriceIdConstraint();
        $validator = $this->get('validator')
            ->validate($skinsPriceId, $skinsPriceIdConstraint);

        if (!count($validator)) {
            $view = $this->view($validator->get(0)->getMessage(), 400);
            return $this->handleView($view);
        }

        try {
            $countSkins = $this->get('api.service.remove_skins_trade')
                ->handler($skinsPriceId, $this->getUser()->getId());

            $view = $this->view($countSkins, 200);
        } catch (\Exception $e) {
            $this->get('logger')->error($e->getMessage());
            $view = $this->view("Bad request", 400);

        }

        return $this->handleView($view);
    }
}
