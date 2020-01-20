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

        $treeBuilder
            ->getRootNode()
            ->children()
                ->scalarNode('entity_manager')
                    ->info('Custom entity manager')
                    ->defaultValue('doctrine.orm.default_entity_manager')
                ->end()
                ->integerNode('max_retries')
                    ->info('Set a maximum number of retries that spool will try to send in case of failure')
                    ->defaultValue(10)
                ->end()
                ->booleanNode('delete_sent_messages')
                    ->info('Delete messages after send')
                    ->defaultFalse()
                ->end()
                ->booleanNode('auto_flush')
                    ->defaultTrue()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
