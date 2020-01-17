<?php

declare(strict_types=1);

namespace PaneeDesign\DatabaseSwiftMailerBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class PedDatabaseSwiftMailerExtension extends Extension
{
    /**
     * {@inheritdoc}
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('ped_database_swift_mailer.params', $config);
        $container->setParameter('ped_database_swift_mailer.entity_manager', $config['entity_manager']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/config'));
        $loader->load('services.yaml');
    }
}
