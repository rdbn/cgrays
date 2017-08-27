<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\SkinsPrice;
use AppBundle\Entity\SkinsTrade;
use Doctrine\DBAL\DBALException;
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
        $productPriceId = (int)$request->request->get('productPriceId');
        $productPrice = $this->getDoctrine()->getRepository(SkinsPrice::class)
            ->findOneBy(["id" => $productPriceId]);

        try {
            $order = new SkinsTrade();
            $order->setSkinsPrice($productPrice);
            $order->setUsers($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($order);
            $em->flush();

            $view = $this->view("success", 200);
        } catch (DBALException $e) {
            $view = $this->view("Bad request", 400);
        }

        return $this->handleView($view);
    }

    /**
     * @param int $orderId
     *
     * @Rest\Post("/order/remove/{orderId}")
     * @Rest\View()
     * @return string
     */
    public function postOrderRemoveAction($orderId)
    {
        $order = $this->getDoctrine()->getRepository(SkinsTrade::class)
            ->findOneBy(["id" => $orderId]);

        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($order);
            $em->flush();

            $view = $this->view("successful", 200);
        } catch (\Exception $e) {
            $view = $this->view("Bad request", 400);

        }

        return $this->handleView($view);
    }
}
