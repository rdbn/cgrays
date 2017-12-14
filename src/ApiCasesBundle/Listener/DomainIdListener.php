<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 07.12.2017
 * Time: 22:03
 */

namespace ApiCasesBundle\Listener;

use ApiCasesBundle\Validator\DomainIdConstraint;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DomainIdListener
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * DomainIdListener constructor.
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $routeName = $event->getRequest()->get('_route');
        if (!preg_match("/apicases/i", $routeName, $matches)) {
            return;
        }

        $request = $event->getRequest();
        $domainId = $request->headers->get('x-domain-id');
        $domainIdConstraint = new DomainIdConstraint();
        $validator = $this->validator->validate($domainId, $domainIdConstraint);
        if (count($validator) > 0) {
            $event->setResponse(new Response("Not found", 404));
        }

        return;
    }
}