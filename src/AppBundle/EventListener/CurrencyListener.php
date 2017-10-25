<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 22.10.17
 * Time: 20:57
 */

namespace AppBundle\EventListener;

use AppBundle\Repository\CurrencyRepository;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CurrencyListener
{
    /**
     * @var CurrencyRepository
     */
    private $currencyRepository;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * CurrencyListener constructor.
     * @param CurrencyRepository $currencyRepository
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(CurrencyRepository $currencyRepository, TokenStorageInterface $tokenStorage)
    {
        $this->currencyRepository = $currencyRepository;
        $this->tokenStorage = $tokenStorage;
    }


    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$this->tokenStorage->getToken()) {
            return;
        }

        $session = $event->getRequest()->getSession();
        if (!$session->has('currency_code')) {
            $currency = $this->currencyRepository
                ->getDefaultCurrency();

            $session->set('currency_code', $currency->getCode());
        }
    }
}