<?php

namespace AppBundle\Command;

use AppBundle\Entity\BalanceUser;
use AppBundle\Entity\CasesBalanceUser;
use AppBundle\Entity\CasesDomain;
use AppBundle\Entity\Currency;
use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateBalanceUserCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:create_balance_user')
            ->setDescription('Hello PhpStorm');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $em = $container->get('doctrine.orm.entity_manager');
        $logger = $container->get('logger');

        $users = $em->getRepository(User::class)
            ->findAll();

        foreach ($users as $user) {
            $currency = $em->getRepository(Currency::class)
                ->findAll();

            $casesDomains = $em->getRepository(CasesDomain::class)
                ->findAll();

            foreach ($casesDomains as $domain) {
                foreach ($currency as $item) {
                    $casesBalanceUser = new CasesBalanceUser();
                    $casesBalanceUser->setCasesDomain($domain);
                    $casesBalanceUser->setCurrency($item);
                    $casesBalanceUser->setUser($user);

                    $em->persist($casesBalanceUser);
                }
            }

            foreach ($currency as $item) {
                $balanceUser = new BalanceUser();
                $balanceUser->setCurrency($item);
                $balanceUser->setUser($user);

                $em->persist($balanceUser);
            }

            $em->flush();

            $logger->info($user->getUsername());
        }
    }
}
