<?php

namespace AppBundle\Controller;

use AppBundle\Entity\SkinsPrice;
use AppBundle\Entity\SkinsTrade;
use AppBundle\Form\SkinsTradeTypeForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SkinsTradeController extends Controller
{
    /**
     * @param Request $request
     *
     * @Route("/basket", name="app.skins_trade.basket")
     * @return Response
     */
    public function basketAction(Request $request)
    {
        $userId = $this->getUser()->getId();
        $em = $this->getDoctrine()->getManager();
        $orders = $em->getRepository(SkinsTrade::class)
            ->findOrderSkinsByUserId($userId);

        $form = $this->createForm(SkinsTradeTypeForm::class);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $sumAll = $em->getRepository(SkinsTrade::class)
                ->findSumAllOrderSkinsByUserId($userId);

            if ($this->getUser()->getBalance() <= $sumAll) {
                return $this->redirectToRoute('app.payment.pay_in_out', ['actionPay' => 'in']);
            }

            $paymentSystemId = $form->get('payment_system')->getData()->getId();
            foreach ($orders as $order) {
                $this->get('app.service.payment.payment_skins')
                    ->handler($paymentSystemId, $order['price_id'], $userId);
            }

            return $this->redirectToRoute('app.skins.main');
        }

        return $this->render('default/basket.html.twig', [
            'form' => $form->createView(),
            'orders' => $orders,
        ]);
    }

    /**
     * @param Request $request
     * @param int $skinsPriceId
     *
     * @Route("/basket/skins_price/{skinsPriceId}", requirements={"skinsPriceId": "\d+"}, name="app.skins_trade.fast_trade")
     * @return Response
     */
    public function fastTradeAction(Request $request, $skinsPriceId)
    {
        $em = $this->getDoctrine()->getManager();
        $skinsPrice = $em->getRepository(SkinsPrice::class)
            ->findOneBy(['id' => $skinsPriceId]);

        $form = $this->createForm(SkinsTradeTypeForm::class);
        $form->handleRequest($request);
        if ($form->isValid()) {
            if ($this->getUser()->getBalance() <= $skinsPrice->getPrice()) {
                return $this->redirectToRoute('app.payment.pay_in_out', [
                    'actionPay' => 'in',
                    'skinsPriceId' => $skinsPriceId,
                ]);
            }

            $paymentSystemId = $form->get('payment_system')->getData()->getId();
            $this->get('app.service.payment.payment_skins')
                ->handler($paymentSystemId, $skinsPrice->getId(), $this->getUser()->getId(), true);

            return $this->redirectToRoute('app.skins.main');
        }

        return $this->render('default/fast_trade.html.twig', [
            'form' => $form->createView(),
            'skinsPrice' => $skinsPrice,
        ]);
    }
}
