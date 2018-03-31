<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 13.03.2018
 * Time: 11:06
 */

namespace ProcessingBundle\ParserCSGO;

use AppBundle\Entity\Skins;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;

class PriceHandler
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * PriceHandler constructor.
     * @param EntityManager $em
     * @param Connection $dbal
     * @param LoggerInterface $logger
     */
    public function __construct(EntityManager $em, Connection $dbal, LoggerInterface $logger)
    {
        $this->em = $em;
        $this->dbal = $dbal;
        $this->logger = $logger;
    }

    /**
     * @param array $priceSkins
     * @throws \Doctrine\DBAL\DBALException
     */
    public function handle(array $priceSkins)
    {
        foreach ($priceSkins as $skin) {
            $weaponName = preg_match('/(.*)\s\|/i', $skin['name'], $matchesWeapon);
            $rarityName = preg_match('/\((.*)\)/i', $skin['name'], $matchesRarity);
            $skinName = preg_match('/(.*)\(/i', $skin['name'], $matchesSkin);

            if (isset($weaponName[1])) {
                $weaponName = $weaponName[1];
            } else {
                $weaponName = 'Другое';
            }
            if (isset($rarityName[1])) {
                $rarityName = $rarityName[1];
            } else {
                $rarityName = 'Другое';
            }
            if (isset($skinName[1])) {
                $skinName = $skinName[1];
            } else {
                $skinName = $skin['name'];
            }

            $skin = $this->em->getRepository(Skins::class)
                ->findOneSkinsByNameAndWeaponAndRarity($skinName, $weaponName, $rarityName);

            if ($skin) {
                $this->dbal->update('skins', ['steam_price' => $skin['price']], ['id' => $skin['id']]);
                $this->logger->info("{$skinName}:{$rarityName}:{$weaponName}");
            }
        }
    }
}