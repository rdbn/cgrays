<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Payment;
use AppBundle\Form\PaymentTypeForm;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    /**
     * @param Request $request
     * @param string $actionPay
     * @param int $skinsPriceId
     *
     * @Route("/payment/{actionPay}/{skinsPriceId}", requirements={"actionPay": "in|out", "skinsPriceId": "\d+"}, name="app.payment.pay_in_out")
     * @return Response
     */
    public function payInOutAction(Request $request, $actionPay, $skinsPriceId = null)
    {
        $urlParameters = ['actionPay' => $actionPay];
        if ($skinsPriceId) {
            $urlParameters['skinsPriceId'] = $skinsPriceId;
        }

        $form = $this->createForm(PaymentTypeForm::class, null, [
            'action' => $this->generateUrl('app.payment.pay_in_out', $urlParameters),
        ]);
        $form->handleRequest($request);

        $isPayment = $this->get('app.service.payment.pay_in_out')
            ->handler($form, $this->getUser(), $actionPay);

        if ($isPayment && $skinsPriceId) {
            return $this->redirectToRoute('app.skins_trade.fast_trade', ['skinsPriceId' => $skinsPriceId]);
        }

        if ($isPayment) {
            return $this->redirectToRoute('app.skins.main');
        }

        return $this->render(':default:payment.html.twig', [
            'skinsPriceId' => $skinsPriceId,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/payment/list", name="app.payment.payment_list")
     * @return Response
     */
    public function paymentListAction()
    {
        $user = $this->getUser();
        $payments = $this->getDoctrine()->getRepository(Payment::class)
            ->findBy(['user' => $user]);

        return $this->render(':default:payment_list.html.twig', [
            'payments' => $payments,
        ]);
    }
}
