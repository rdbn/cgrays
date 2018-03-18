<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 10.10.17
 * Time: 17:18
 */

namespace ProcessingBundle\Parser;

use AppBundle\Entity\Decor;
use AppBundle\Entity\ItemSet;
use AppBundle\Entity\Quality;
use AppBundle\Entity\Rarity;
use AppBundle\Entity\TypeSkins;
use AppBundle\Entity\Weapon;
use AppBundle\Modal\DictionaryInterface;
use AppBundle\Services\UploadImageService;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

class SkinsImageHandler
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
     * @var Client
     */
    private $guzzle;

    /**
     * @var UploadImageService
     */
    private $uploadImage;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $steamCommunityImageUrl;

    /**
     * SkinsHandler constructor.
     * @param EntityManager $em
     * @param Connection $dbal
     * @param Client $guzzle
     * @param UploadImageService $uploadImage
     * @param LoggerInterface $logger
     * @param $steamCommunityImageUrl
     */
    public function __construct(
        EntityManager $em,
        Connection $dbal,
        Client $guzzle,
        UploadImageService $uploadImage,
        LoggerInterface $logger,
        $steamCommunityImageUrl
    )
    {
        $this->em = $em;
        $this->dbal = $dbal;
        $this->guzzle = $guzzle;
        $this->uploadImage = $uploadImage;
        $this->logger = $logger;
        $this->steamCommunityImageUrl = $steamCommunityImageUrl;
    }

    /**
     * @param array $skin
     * @throws \Exception
     */
    public function handler(array $skin)
    {
        $request = $this->guzzle->request('GET', $skin['link']);
        $skinHTML = $request->getBody();

        preg_match('/var g_rgAssets = ({.*})/i', $skinHTML, $matches);
        $skin = json_decode($matches[1], 1);
        $skin = array_shift($skin);
        $skin = array_shift($skin);
        $skin = array_shift($skin);

        $this->addSkin($skin);
    }

    /**
     * @param array $skin
     * @throws \Exception
     */
    private function addSkin(array $skin)
    {
        $iconUrl = "image/" . $this->uploadImage
                ->upload($this->steamCommunityImageUrl . "/" . $skin['icon_url'], false);
    }
}