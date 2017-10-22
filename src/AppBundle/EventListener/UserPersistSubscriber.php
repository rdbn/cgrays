<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 01.10.17
 * Time: 18:57
 */

namespace AppBundle\EventListener;

use AppBundle\Entity\BalanceUser;
use AppBundle\Entity\CasesBalanceUser;
use AppBundle\Entity\CasesDomain;
use AppBundle\Entity\Currency;
use AppBundle\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

class UserPersistSubscriber implements EventSubscriber
{
    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            'postPersist',
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        /* @var User $cases */
        $user = $args->getObject();
        if (!$user instanceof User) {
            return;
        }

        $om = $args->getObjectManager();
        $currency = $om->getRepository(Currency::class)
            ->findAll();

        $casesDomains = $om->getRepository(CasesDomain::class)
            ->findAll();

        foreach ($casesDomains as $domain) {
            foreach ($currency as $item) {
                $casesBalanceUser = new CasesBalanceUser();
                $casesBalanceUser->setCasesDomain($domain);
                $casesBalanceUser->setCurrency($item);
                $casesBalanceUser->setUser($user);

                $om->persist($casesBalanceUser);
            }
        }

        foreach ($currency as $item) {
            $balanceUser = new BalanceUser();
            $balanceUser->setCurrency($item);
            $balanceUser->setUser($user);

            $om->persist($balanceUser);
        }

        $om->flush();
    }
}