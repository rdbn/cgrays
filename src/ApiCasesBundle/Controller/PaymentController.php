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
        $paymentInformation = $request->request->all();
        $domainId = $request->headers->get('x-domain-id');

        try {
            $this->get('api_cases.service.payment_handler')
                ->handle($domainId, $paymentInformation['label'], $paymentInformation);

            $view = $this->view("success", 200);
        } catch (\Exception $e) {
            $this->get('logger')->error(json_encode($paymentInformation));
            $this->get('logger')->error($e->getMessage());

            $view = $this->view("Bad request", 200);
        }

        return $this->handleView($view);
    }
}
