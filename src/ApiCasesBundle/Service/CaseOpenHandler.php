<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 11.09.17
 * Time: 10:24
 */

namespace ApiCasesBundle\Service;

use AppBundle\Entity\CasesBalanceUser;
use AppBundle\Entity\CasesSkins;
use AppBundle\Entity\User;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CaseOpenHandler
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
     * @var User
     */
    private $user;

    /**
     * @var GamesService
     */
    private $gamesService;

    /**
     * @var MetricsEventSender
     */
    private $eventSender;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * CaseOpenService constructor.
     * @param EntityManager $em
     * @param Connection $dbal
     * @param TokenStorageInterface $tokenStorage
     * @param GamesService $gamesService
     * @param MetricsEventSender $eventSender
     * @param LoggerInterface $logger
     */
    public function __construct(
        EntityManager $em,
        Connection $dbal,
        TokenStorageInterface $tokenStorage,
        GamesService $gamesService,
        MetricsEventSender $eventSender,
        LoggerInterface $logger
    )
    {
        $this->em = $em;
        $this->dbal = $dbal;
        $this->user = $tokenStorage->getToken()->getUser();
        $this->gamesService = $gamesService;
        $this->eventSender = $eventSender;
        $this->logger = $logger;
    }

    /**
     * @param $domainId
     * @param $casesId
     * @param $userId
     * @param $currencyId
     * @return array
     * @throws \Exception
     */
    public function handler($domainId, $casesId, $userId, $currencyId)
    {
        $skins = $this->em->getRepository(CasesSkins::class)
            ->findSkinsByCasesId($domainId, $casesId);

        if (!count($skins)) {
            return [];
        }

        $skins = $skins[mt_rand(0, (count($skins) - 1))];

        $this->dbal->beginTransaction();
        try {
            $this->em->getRepository(CasesSkins::class)
                ->findCasesSkinsByCasesIdForUpdate($skins['cases_skins_id']);

            $casesBalanceUser = $this->em->getRepository(CasesBalanceUser::class)
                ->findUserBalanceForUpdateByUserIdCurrencyIdDomain(
                    $userId, $currencyId, $skins['cases_domain_id']
                );

            $balance = (float) $casesBalanceUser['balance'] - (float) $skins['cases_price'];
            if ($balance < 0) {
                throw new \Exception('User money is empty!!');
            }

            $this->dbal->update(
                'cases_balance_user',
                ['balance' => $balance],
                ['id' => $casesBalanceUser['id']]
            );

            $date = new \DateTime();
            $this->dbal->insert(
                'cases_skins_drop_user',
                [
                    'skins_id' => $skins['id'],
                    'user_id' => $userId,
                    'cases_domain_id' => $skins['cases_domain_id'],
                    'created_at' => $date->format('Y-m-d H:i:s'),
                ]
            );

            $this->dbal->commit();
        } catch (DBALException $e) {
            $this->dbal->rollBack();

            $this->logger->error($e->getMessage());
            throw new \Exception($e->getMessage());
        }

        $this->gamesService->flushRedisGame($userId, [
            'skins_id' => $skins['id'],
            'cases_id' => $casesId,
            'cases_domain_id' => $skins['cases_domain_id'],
            'cases_category_id' => $skins['cases_category_id'],
            'weapon_name' => $skins['weapon'],
            'skins_name' => $skins['name'],
            'rarity' => $skins['rarity'],
            'steam_image' => $skins['icon_url'],
            'price' => $skins['cases_price'],
            'balance' => $balance,
        ]);

        $this->eventSender->sender([
            'user_id' => $userId,
            'cases_id' => $casesId,
            'cases_domain_id' => $skins['cases_domain_id'],
            'cases_category_id' => $skins['cases_category_id'],
            'event_type' => 'open',
        ]);

        return [
            'weapon_name' => $skins['weapon'],
            'skin_name' => $skins['name'],
            'rarity' => $skins['rarity'],
            'steam_image' => "/{$skins['icon_url']}",
            'price' => number_format($skins['steam_price'], 2),
            'balance' => $balance,
        ];
    }
}