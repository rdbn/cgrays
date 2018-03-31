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
        $guzzle = $container->get('guzzle.client.steam');
        $logger = $container->get('logger');

        $request = $guzzle->request('GET', 'https://pubgitems.pro/en/containers');
        $status = $request->getStatusCode();
        $content = $request->getBody()->getContents();

        $logger->info("Status: {$status}");
        if ($status != 200) {
            $logger->info("Content: {$content}");
            throw new \Exception('Not valid status');
        }

        if($status === 200) {
            $crawler = new Crawler($content);
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
