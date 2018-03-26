<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 10.10.17
 * Time: 17:18
 */

namespace ProcessingBundle\Parser;

use AppBundle\Entity\Skins;
use AppBundle\Services\UploadImageService;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use ProcessingBundle\Parser\Helper\FilterSkinHelper;

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
     * @var UploadImageService
     */
    private $uploadImage;

    /**
     * @var string
     */
    private $steamCommunityImageUrl;

    /**
     * SkinsHandler constructor.
     * @param EntityManager $em
     * @param Connection $dbal
     * @param UploadImageService $uploadImage
     * @param $steamCommunityImageUrl
     */
    public function __construct(EntityManager $em, Connection $dbal, UploadImageService $uploadImage, $steamCommunityImageUrl)
    {
        $this->em = $em;
        $this->dbal = $dbal;
        $this->uploadImage = $uploadImage;
        $this->steamCommunityImageUrl = $steamCommunityImageUrl;
    }

    /**
     * @param $jsStringSkin
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Exception
     */
    public function handler($jsStringSkin)
    {
        preg_match('/var g_rgAssets = ({.*})/i', $jsStringSkin, $matches);
        $skin = json_decode($matches[1], 1);
        $skin = array_shift($skin);
        $skin = array_shift($skin);
        $skin = array_shift($skin);

        $filter = FilterSkinHelper::normalize($skin);

        $filter['name'] = $skin['name'];
        $skinRow = $this->em->getRepository(Skins::class)
            ->findOneSkinsByFilter($filter);

        if (!is_array($skinRow)) {
            throw new \Exception('Not found skins.');
        }

        $imageInfo = getimagesize(__DIR__ . "/../../../web/{$skinRow['icon_url']}");
        if ($imageInfo) {
            return;
        }

        $iconUrl = "image/" . $this->uploadImage
                ->upload($this->steamCommunityImageUrl . "/" . $skin['icon_url'], false);

        $imageInfo = getimagesize($iconUrl);
        if (!$imageInfo) {
            throw new \Exception('Not upload image.');
        }

        $this->dbal->update('skins', ['icon_url' => $iconUrl], ['id' => $skin['id']]);
    }
}