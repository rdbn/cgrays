<?php
/**
 * Created by PhpStorm.
 * User: rdbn
 * Date: 09.10.17
 * Time: 18:07
 */

namespace ProcessingBundle\Services;

use AppBundle\Repository\SkinsRepository;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Psr\Log\LoggerInterface;
use SteamBundle\SteamMethod\Market;
use Symfony\Component\DomCrawler\Crawler;

class ParserMarketHandler
{
    /**
     * @var SkinsRepository
     */
    private $skinsRepository;

    /**
     * @var ProducerInterface
     */
    private $priceProducer;

    /**
     * @var ProducerInterface
     */
    private $skinsProducer;

    /**
     * @var Market
     */
    private $market;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var integer
     */
    private $count;

    /**
     * ParserPriceHandler constructor.
     * @param SkinsRepository $skinsRepository
     * @param ProducerInterface $priceProducer
     * @param ProducerInterface $skinsProducer
     * @param Market $market
     * @param LoggerInterface $logger
     */
    public function __construct(
        SkinsRepository $skinsRepository,
        ProducerInterface $priceProducer,
        ProducerInterface $skinsProducer,
        Market $market,
        LoggerInterface $logger
    )
    {
        $this->skinsRepository = $skinsRepository;
        $this->priceProducer = $priceProducer;
        $this->skinsProducer = $skinsProducer;
        $this->market = $market;
        $this->logger = $logger;
    }

    /**
     * @param $startId
     * @param $gameId
     * @param $count
     */
    public function handler($startId, $gameId, $count)
    {
        $skins = $this->getNormalizeArray(
            $this->market->getResult($startId, $gameId, $count)
        );

        $skinsFoundDB = $this->getSkinsFoundDB($skins);
        foreach ($skins as $skin) {
            if (isset($skinsFoundDB[$skin['name']])) {
                $this->priceProducer->publish(json_encode([
                    'id' => $skinsFoundDB[$skin['name']]['id'],
                    'price' => $skin['price'],
                ]));
            } else {
                $this->skinsProducer->publish(json_encode($skin));
            }
        }

        $this->count = count($skins);
    }

    /**
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * @param array $skins
     * @return array
     */
    private function getNormalizeArray(array $skins)
    {
        $crawler = new Crawler($skins['results_html']);
        $skinsNames = $crawler->filter('.market_listing_item_name')->each(function (Crawler $node, $i) {
            return $node->text();
        });

        $skinsLinks = $crawler->filter('.market_listing_row_link')->each(function (Crawler $node, $i) {
            return $node->attr('href');
        });

        $skinsPrice = $crawler->filter('span[class="normal_price"]')->each(function (Crawler $node, $i) {
            preg_match('(\d+\.\d+)', $node->text(), $matches);
            return $matches[0];
        });

        $skins = [];
        foreach ($skinsNames as $key => $name) {
            $skins[$name] = [
                'name' => $name,
                'link' => $skinsLinks[$key],
                'price' => $skinsPrice[$key],
            ];
        }

        return $skins;
    }

    /**
     * @param array $skins
     * @return array
     */
    private function getSkinsFoundDB(array $skins)
    {
        $skins = array_map(function ($item) {
            return "'{$item['name']}'";
        }, $skins);

        $skins = $this->skinsRepository->findSkinsByNames(
            implode(",", $skins)
        );

        $skinsFoundDB = [];
        foreach ($skins as $skin) {
            $skinsFoundDB[$skin['name']] = $skin['id'];
        }

        return $skinsFoundDB;
    }
}