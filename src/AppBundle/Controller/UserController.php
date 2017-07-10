<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Entity\ProductPrice;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    /**
     * @Route("/user", name="app.user.main")
     */
    public function mainAction()
    {
        $user = $this->getUser();
        $products = $this->getDoctrine()->getRepository(ProductPrice::class)
            ->findProductsByUser($user);

        return $this->render('default/user.html.twig', [
            'products' => $products,
            'user' => $user,
        ]);
    }

    /**
     * @Route("/sell", name="app.user.sell")
     */
    public function sellAction()
    {
        $user = $this->getUser();
        if ($user) {
            $products = $this->getDoctrine()->getRepository(ProductPrice::class)
                ->findProductsByUser($this->getUser());
        }

        return $this->render("default/sell.html.twig", [
            "products" => isset($products) ? $products : [],
        ]);
    }
}
