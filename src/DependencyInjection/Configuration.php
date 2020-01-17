<?php

declare(strict_types=1);

namespace PaneeDesign\DatabaseSwiftMailerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('ped_database_swift_mailer');

        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->getRootNode();
        }

        $rootNode
            ->children()
            ->scalarNode('max_retries')->defaultValue('10')->end()
            ->scalarNode('delete_sent_messages')->defaultFalse()->end()
            ->scalarNode('entity_manager')->defaultValue('doctrine.orm.default_entity_manager')->end()
            ->end();

        return $treeBuilder;
    }
}
