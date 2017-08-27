<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ProductDota;
use AppBundle\Entity\ProductPriceDota;
use AppBundle\Entity\SkinsPrice;
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
        $skins = $this->getDoctrine()->getRepository(SkinsPrice::class)
            ->findSkinsPriceByUser($user);

        return $this->render('default/user.html.twig', [
            'skins' => $skins,
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
            $skins = $this->getDoctrine()->getRepository(SkinsPrice::class)
                ->findSkinsPriceByUser($this->getUser());
        }

        return $this->render("default/sell.html.twig", [
            "skins" => isset($skins) ? $skins : [],
        ]);
    }
}
