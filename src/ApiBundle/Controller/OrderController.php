<?php

namespace ApiBundle\Controller;

use AppBundle\Entity\Order;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductPrice;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class OrderController extends FOSRestController
{
    /**
     * @param int $productId
     * @param int $productPriceId
     *
     * @Rest\Post("/order/add/{productId}/{productPriceId}")
     * @Rest\View()
     * @return string
     */
    public function postOrderAddAction($productId, $productPriceId)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)
            ->findOneBy(["id" => $productId]);

        $productPrice = $this->getDoctrine()->getRepository(ProductPrice::class)
            ->findOneBy(["id" => $productPriceId]);

        try {
            $order = new Order();
            $order->setProduct($product);
            $order->setProductPrice($productPrice);
            $order->setUsers($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($order);
            $em->flush();

            $view = $this->view("successful", 200);
        } catch (\Exception $e) {
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
        $order = $this->getDoctrine()->getRepository(Order::class)
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
