<?php

declare(strict_types=1);

namespace PaneeDesign\DatabaseSwiftMailerBundle\DependencyInjection;

use PaneeDesign\DatabaseSwiftMailerBundle\Controller\EmailController;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
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
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
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
                    ->info('Persist email entity immediately, without check pending persists')
                    ->defaultTrue()
                ->end()
            ->end();

        $this->addViewsSection($rootNode);

        return $treeBuilder;
    }

    private function addViewsSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('views')
                    ->info('Section can be enabled to handle views parameters')
                    ->canBeEnabled()
                    ->children()
                        ->integerNode('max_page_rows')
                            ->info('Number of item per page')
                            ->defaultValue(EmailController::MAX_PAGE_ROWS)
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
