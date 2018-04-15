<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 30.03.2018
 * Time: 19:10
 */

namespace ProcessingBundle\ParserPUBG;

use AppBundle\Entity\RarityPUBG;
use AppBundle\Entity\SkinsPUBG;
use AppBundle\Services\UploadImageService;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\Client;
use JonnyW\PhantomJs\Client as Phantom;
use Psr\Log\LoggerInterface;
use Symfony\Component\DomCrawler\Crawler;

class SkinsHandler
{
    /**
     * @var Connection
     */
    private $dbal;

    /**
     * @var Phantom
     */
    private $phantom;

    /**
     * @var Guzzle
     */
    private $guzzle;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var UploadImageService
     */
    private $imageService;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * SkinsHandler constructor.
     * @param Connection $dbal
     * @param EntityManager $em
     * @param UploadImageService $imageService
     * @param LoggerInterface $logger
     */
    public function __construct(Connection $dbal, EntityManager $em, UploadImageService $imageService, LoggerInterface $logger)
    {
        $this->dbal = $dbal;
        $this->em = $em;
        $this->imageService = $imageService;
        $this->logger = $logger;

        $this->guzzle = new Client();
        $this->phantom = Phantom::getInstance();
        $this->phantom->getEngine()->setPath('/usr/local/bin/phantomjs');
    }

    /**
     * @param array $skins
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Exception
     */
    public function handle(array $skins)
    {
        $skinsEntity = $this->em->getRepository(SkinsPUBG::class)
            ->findOneBy(['name' => trim($skins['name'])]);

        if ($skinsEntity) {
            return;
        }

        $content = $this->getPhantomParser("https://pubgitems.pro{$skins['href']}");
        if ($content['status'] != 200) {
            $content = $this->getGuzzleParser("https://pubgitems.pro{$skins['href']}");
        }

        $this->logger->info("Status: {$content['status']}");
        $this->logger->info("Url: https://pubgitems.pro{$skins['href']}");

        if ($content['status'] != 200) {
            $this->logger->info("Content: {$content['content']}");
            throw new \Exception('Not valid response.');
        }

        if($content['status'] == 200) {
            $crawler = new Crawler($content['content']);
            $skinsName = $crawler->filter('#item-image img')->first()->attr('alt');
            $imageHref = $crawler->filter('#item-image img')->first()->attr('src');

            $element = $crawler->filter('#item-image .item-rarirty');
            if ($element->count() > 0) {
                $rarityName = $element->text();
            } else {
                $rarityName = "Common";
            }

            $rarityId = $this->getIdRarity($rarityName);
            $steamImage = $this->getImage($imageHref);

            $this->dbal->insert('skins_pubg', [
                'rarity_id' => $rarityId,
                'name' => $skinsName,
                'image' => $steamImage,
                'steam_price' => (float)str_replace(" ", "", $skins['price']),
            ]);
        }
    }

    /**
     * @param $href
     * @return array
     */
    private function getGuzzleParser($href)
    {
        $request = $this->guzzle->request('GET', $href);
        $status = $request->getStatusCode();
        $content = $request->getBody()->getContents();

        return [
            'status' => $status,
            'content' => $content,
        ];
    }

    /**
     * @param $href
     * @return array
     */
    private function getPhantomParser($href)
    {
        $request  = $this->phantom->getMessageFactory()->createRequest();
        $response = $this->phantom->getMessageFactory()->createResponse();

        $request->setMethod('GET');
        $request->setUrl("https://pubgitems.pro{$href}");

        $this->phantom->send($request, $response);

        return [
            'status' => $response->getStatus(),
            'content' => $response->getContent(),
        ];
    }

    /**
     * @param $rarityName
     * @return int
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Exception
     */
    private function getIdRarity($rarityName)
    {
        $rarity = $this->em->getRepository(RarityPUBG::class)
            ->findOneBy(['localizedTagName' => $rarityName]);

        if ($rarity) {
            return $rarity->getId();
        }

        $this->dbal->beginTransaction();
        try {
            $this->dbal->insert('rarity_pubg', ['localized_tag_name' => $rarityName]);
            $rarityId = $this->dbal->lastInsertId();
            $this->dbal->commit();
        } catch (DBALException $e) {
            $this->dbal->rollBack();
            throw new \Exception($e->getMessage());
        }

        return $rarityId;
    }

    /**
     * @param $imageUrl
     * @return string
     */
    private function getImage($imageUrl)
    {
        try {
            $image = $this->imageService->upload("https:{$imageUrl}", false);
            if (is_array(getimagesize("http://cgrays.com/image/{$image}"))) {
                return "image/{$image}";
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        try {
            $image = $this->imageService->upload("https://pubgitems.pro{$imageUrl}", false);
            if (is_array(getimagesize("http://cgrays.com/image/{$image}"))) {
                return "image/{$image}";
            }
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }

        sleep(30);
        $this->logger->info('recursion');
        $this->getImage($imageUrl);
    }
}