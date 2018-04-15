<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 11.09.17
 * Time: 10:24
 */

namespace ApiCasesPUBGBundle\Service;

use ApiCasesBundle\Service\GamesService;
use ApiCasesBundle\Service\MetricsEventSender;
use AppBundle\Entity\CasesBalanceUser;
use AppBundle\Entity\CasesSkinsPUBG;
use AppBundle\Entity\User;
use AppBundle\Services\Helper\MbStrimWidthHelper;
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
     * @param User $user
     * @param $currencyId
     * @return array
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Exception
     */
    public function handler($domainId, $casesId, User $user, $currencyId)
    {
        $userId = $user->getId();
        $skins = $this->em->getRepository(CasesSkinsPUBG::class)
            ->findSkinsByCasesId($domainId, $casesId);

        if (!count($skins)) {
            return [];
        }

        $skins = $skins[mt_rand(0, (count($skins) - 1))];

        $this->dbal->beginTransaction();
        try {
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
                'cases_skins_drop_user_pubg',
                [
                    'skins_id' => $skins['id'],
                    'user_id' => $userId,
                    'cases_domain_id' => $skins['cases_domain_id'],
                    'cases_id' => $casesId,
                    'created_at' => $date->format('Y-m-d H:i:s'),
                ]
            );
            $casesSkinsDropUserId = $this->dbal->lastInsertId();

            $this->dbal->commit();
        } catch (DBALException $e) {
            $this->dbal->rollBack();

            $this->logger->error($e->getMessage());
            throw new \Exception($e->getMessage());
        }

        $this->persistGame($skins, $userId, $casesId, $balance);
        $this->metricsSender($skins, $userId, $casesId);

        return [
            'skins_drop_id' => $casesSkinsDropUserId,
            'skin_name' => MbStrimWidthHelper::strimWidth($skins['name']),
            'rarity' => $skins['rarity_id'],
            'rarity_id' => $skins['rarity_id'],
            'username' => $user->getUsername(),
            'user_id' => $user->getId(),
            'cases_image' => "/{$skins['cases_image']}",
            'steam_image' => "/{$skins['icon_url']}",
            'price' => number_format($skins['steam_price'], 2),
            'balance' => $balance,
        ];
    }

    /**
     * @param array $skins
     * @param $userId
     * @param $casesId
     * @param $balance
     */
    public function persistGame(array $skins, $userId, $casesId, $balance)
    {
        $this->gamesService->flushRedisGame($userId, [
            'skins_id' => $skins['id'],
            'cases_id' => $casesId,
            'cases_domain_id' => $skins['cases_domain_id'],
            'cases_category_id' => $skins['cases_category_id'],
            'skin_name' => $skins['name'],
            'rarity' => $skins['rarity'],
            'rarity_id' => $skins['rarity_id'],
            'steam_image' => "/{$skins['icon_url']}",
            'price' => $skins['cases_price'],
            'balance' => $balance,
        ]);
    }

    /**
     * @param array $skins
     * @param $userId
     * @param $casesId
     */
    public function metricsSender(array $skins, $userId, $casesId)
    {
        $this->eventSender->sender([
            'user_id' => $userId,
            'cases_id' => $casesId,
            'cases_domain_id' => $skins['cases_domain_id'],
            'cases_category_id' => $skins['cases_category_id'],
            'event_type' => 'open',
        ]);
    }
}