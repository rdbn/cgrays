<?php
namespace SteamBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

class SteamExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('steam.steam_api_url', $config['steam_api_url']);
        $container->setParameter('steam.steam_community_url', $config['steam_community_url']);
        $container->setParameter('steam.steam_inventory_count', $config['steam_inventory_count']);
        $container->setParameter('steam.steam_key', $config['steam_key']);
        $container->setParameter('steam.user_class', $config['user_class']);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    public function prepend(ContainerBuilder $container)
    {
        $config = [
            'clients' => [
                'steam' => [
                    'base_url' => "{$container->getParameter("steam_community_url")}/openid/",
                ],
                'steam_user' => [
                    'base_url' => "{$container->getParameter("steam_api_url")}/ISteamUser/",
                ],
            ]
        ];

        $container->prependExtensionConfig('guzzle', $config);
    }
}
