<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Order;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class BasketController extends Controller
{
    /**
     * @Route("/basket", name="app.basket.basket")
     */
    public function basketAction()
    {
        $user = $this->getUser();
        if ($user) {
            $orders = $this->getDoctrine()->getRepository(Order::class)
                ->findOrderProductsByUserId($user->getId());
        }

        return $this->render('default/basket.html.twig', [
            'orders' => isset($orders) ? $orders : [],
        ]);
    }

    /**
     * @Route("/order", name="app.product.order")
     */
    public function orderAction(Request $request)
    {
        $user = $this->getUser();
        $orders = $this->getDoctrine()->getRepository(Order::class)
            ->findBy(['users' => $user->getId()]);

        return $this->render('default/basket.html.twig', [
            'orders' => $orders,
        ]);
    }
}
