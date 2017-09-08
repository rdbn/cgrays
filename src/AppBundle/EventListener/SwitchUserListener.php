<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 07.09.17
 * Time: 23:46
 */

namespace AppBundle\EventListener;

use Symfony\Component\Security\Http\Event\SwitchUserEvent;

class SwitchUserListener
{
    public function onSwitchUser(SwitchUserEvent $event)
    {
        $event->getRequest()->getSession()->set(
            '_locale',
            $event->getTargetUser()->getLocale()
        );
    }
}