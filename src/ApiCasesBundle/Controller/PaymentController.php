<?php

namespace ApiCasesBundle\Controller;

use AppBundle\Form\PaymentTypeForm;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends FOSRestController
{
    /**
     * @param Request $request
     *
     * @Rest\Post("/payment")
     * @Rest\View()
     * @return Response
     */
    public function postPaymentAction(Request $request)
    {
        $this->get('logger')->error(implode(",", $request->query->all()));
        $this->get('logger')->error(implode(",", $request->request->all()));
        $this->get('logger')->error(implode(",", $request->headers->all()));


//        $form = $this->createForm(PaymentTypeForm::class);
//        $form->handleRequest($request);
//
//        $isPayment = $this->get('app.service.payment.pay_in_out')
//            ->handler($form, $this->getUser(), 'in');
//
//        if ($isPayment && $skinsPriceId) {
//            return $this->redirectToRoute('app.skins_trade.fast_trade', ['skinsPriceId' => $skinsPriceId]);
//        }
//
//        if ($isPayment) {
//            return $this->redirectToRoute('app.skins.main');
//        }

//        return $this->render(':default:payment.html.twig', [
//            'skinsPriceId' => $skinsPriceId,
//            'form' => $form->createView(),
//        ]);

        $view = $this->view("success", 200);
        return $this->handleView($view);
    }
}
