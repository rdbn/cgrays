<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 14.10.17
 * Time: 22:43
 */

namespace ApiCasesBundle\Service;

use AppBundle\Entity\CasesDomain;
use AppBundle\Services\Helper\MbStrimWidthHelper;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;

class CasesUserPickUpSkinsHandler
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Connection
     */
    private $dbal;

    /**
     * @var ProducerInterface
     */
    private $producer;

    /**
     * CasesUserPickUpSkinsHandler constructor.
     * @param EntityManager $em
     * @param Connection $dbal
     * @param ProducerInterface $producer
     */
    public function __construct(EntityManager $em, Connection $dbal, ProducerInterface $producer)
    {
        $this->em = $em;
        $this->dbal = $dbal;
        $this->producer = $producer;
    }

    /**
     * @param array $resultGame
     * @param $userId
     * @param $domainId
     * @throws \Exception
     */
    public function handler(array $resultGame, $userId, $domainId)
    {
        $casesDomain = $this->em->getRepository(CasesDomain::class)
            ->findOneBy(['uuid' => $domainId]);

        if (!$casesDomain) {
            throw new \Exception('Not found domain');
        }

        try {
            $this->dbal->insert('cases_skins_pick_up_user', [
                'skins_id' => $resultGame['skins_id'],
                'cases_domain_id' => $resultGame['cases_domain_id'],
                'user_id' => $userId,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            $resultGame['skins_name'] = MbStrimWidthHelper::strimWidth($resultGame['skins_name']);
            unset($resultGame['skins_id'], $resultGame['cases_domain_id']);
            $this->producer->publish(json_encode($resultGame));
        } catch (DBALException $e) {
            throw new \Exception($e->getMessage());
        }
    }
}