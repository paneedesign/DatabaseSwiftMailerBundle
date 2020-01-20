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

        $container->setParameter('ped_database_swift_mailer.entity_manager', $config['entity_manager']);
        $container->setParameter('ped_database_swift_mailer.max_retries', $config['max_retries']);
        $container->setParameter('ped_database_swift_mailer.delete_sent_messages', $config['delete_sent_messages']);
        $container->setParameter('ped_database_swift_mailer.auto_flush', $config['auto_flush']);
        $container->setParameter('ped_database_swift_mailer.views.max_page_rows', $config['views']['max_page_rows']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
    }
}
