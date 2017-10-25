<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 22.10.17
 * Time: 20:57
 */

namespace AppBundle\EventListener;

use AppBundle\Entity\User;
use AppBundle\Repository\CurrencyRepository;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class AuthenticationListener
{
    /**
     * @var CurrencyRepository
     */
    private $currencyRepository;

    /**
     * CurrencyListener constructor.
     * @param CurrencyRepository $currencyRepository
     */
    public function __construct(CurrencyRepository $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function onAuthenticationSuccess(InteractiveLoginEvent $event)
    {
        $user = $event->getAuthenticationToken()->getUser();
        if ($user instanceof User) {
            $session = $event->getRequest()->getSession();
            if (!$session->has('currency_code')) {
                $currency = $this->currencyRepository
                    ->getDefaultCurrency();

                $session->set('currency_code', $currency->getCode());
            }
        }
    }
}