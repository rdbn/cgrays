<?php

namespace ProcessingBundle\Command;

use JonnyW\PhantomJs\Client;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;

class ParserPUBGCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('processing:parser_pubg')
            ->setDescription('Parser pubg. https://pubgitems.pro/en/containers');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $producer = $container->get('old_sound_rabbit_mq.parser_pubg_producer');
        $logger = $container->get('logger');

        $client = Client::getInstance();
        $client->getEngine()->setPath('/usr/local/bin/phantomjs');

        $request  = $client->getMessageFactory()->createRequest();
        $response = $client->getMessageFactory()->createResponse();

        $request->setMethod('GET');
        $request->setUrl('https://pubgitems.pro/en/containers');

        $client->send($request, $response);

        $logger->info("Status: {$response->getStatus()}");
        if ($response->getStatus() != 200) {
            $logger->info("Content: {$response->getContent()}");
            throw new \Exception('Not valid status');
        }

        if($response->getStatus() === 200) {
            $crawler = new Crawler($response->getContent());
            $crawler->filter('#browse-collections a.mdc-list-item')->each(function (Crawler $node, $i) use ($producer) {
                $text = trim($node->text());
                preg_match('/(.*)/i', $text, $matchesName);
                preg_match('/(\n\t(.*)\n(.*)\t)(.*)/i', $text, $matchesPrice);

                $producer->publish(json_encode([
                    'href' => $node->attr('href'),
                    'name' => $matchesName[0],
                    'price' => isset($matchesPrice[4]) ? $matchesPrice[4] : 0,
                ]));
            });
        }
    }
}
