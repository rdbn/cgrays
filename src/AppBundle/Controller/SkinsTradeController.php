<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Currency;
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
        $orders = $this->getDoctrine()->getRepository(SkinsTrade::class)
            ->findOrderSkinsByUserId($userId);

        $form = $this->createForm(SkinsTradeTypeForm::class);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $currency = $this->getDoctrine()->getRepository(Currency::class)
                ->findOneBy(['code' => $request->getSession()->get('currency_code')]);

            try {
                $ids = implode(",", array_map(function ($item) {
                    return $item['price_id'];
                }, $orders));

                $this->get('app.service.payment.basket_payment_skins')
                    ->handler($currency->getId(), $ids, $userId);

                return $this->redirectToRoute('app.skins.main');
            } catch (\Exception $e) {
                if ($e->getCode() == 403) {
                    return $this->redirectToRoute('app.payment.pay_in_out', ['actionPay' => 'in']);
                }
                $this->get('logger')->error($e->getMessage());
            }
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
     * @Route("/basket/hot/{skinsPriceId}", requirements={"skinsPriceId": "\d+"}, name="app.skins_trade.fast_trade")
     * @return Response
     */
    public function hotTradeAction(Request $request, $skinsPriceId)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var SkinsPrice $skinsPrice */
        $skinsPrice = $em->getRepository(SkinsPrice::class)
            ->findOneBy(['id' => $skinsPriceId]);

        $form = $this->createForm(SkinsTradeTypeForm::class);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $currency = $em->getRepository(Currency::class)
                ->findOneBy(['code' => $request->getSession()->get('currency_code')]);

            try {
                $this->get('app.service.payment.hot_payment_skins')
                    ->handler($currency->getId(), $skinsPriceId, $this->getUser()->getId());

                return $this->redirectToRoute('app.skins.main');
            } catch (\Exception $e) {
                if ($e->getCode() == 403) {
                    return $this->redirectToRoute('app.payment.pay_in_out', [
                        'actionPay' => 'in',
                        'skinsPriceId' => $skinsPriceId,
                    ]);
                }
                $this->get('logger')->error($e->getMessage());
            }
        }

        return $this->render('default/hot_trade.html.twig', [
            'form' => $form->createView(),
            'skinsPrice' => $skinsPrice,
        ]);
    }
}
