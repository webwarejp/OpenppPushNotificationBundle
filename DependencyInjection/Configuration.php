<?php

namespace Openpp\PushNotificationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('openpp_push_notification');

        $supportedManagerTypes = array('orm');

        $rootNode
            ->children()
                ->booleanNode('consumer')
                    ->defaultValue(true)
                ->end()
                ->scalarNode('pusher')
                    ->defaultValue('openpp.push_notification.pusher.own')
                ->end()
                ->scalarNode('db_driver')
                    ->defaultValue('orm')
                    ->validate()
                        ->ifNotInArray($supportedManagerTypes)
                        ->thenInvalid('The db_driver %s is not supported. Please choose one of ' . json_encode($supportedManagerTypes))
                    ->end()
                ->end()
                ->arrayNode('class')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('application')->defaultValue('Application\\Openpp\\PushNotificationBundle\\Entity\\Application')->end()
                        ->scalarNode('device')->defaultValue('Application\\Openpp\\PushNotificationBundle\\Entity\\Device')->end()
                        ->scalarNode('tag')->defaultValue('Application\\Openpp\\PushNotificationBundle\\Entity\\Tag')->end()
                        ->scalarNode('user')->defaultValue('Application\\Openpp\\PushNotificationBundle\\Entity\\User')->end()
                        ->scalarNode('condition')->defaultValue('Application\\Openpp\\PushNotificationBundle\\Entity\\Condition')->end()
                        ->scalarNode('areaCircle')->defaultValue('Application\\Openpp\\MapBundle\\Entity\\Circle')->end()
                        ->scalarNode('location')->defaultValue('Application\\Openpp\\MapBundle\\Entity\\Point')->end()
                        ->scalarNode('media')->defaultValue('Application\\Sonata\\MediaBundle\\Entity\\Media')->end()
                        ->scalarNode('history')->defaultValue('Application\\Openpp\\PushNotificationBundle\\Entity\\History')->end()
                    ->end()
                ->end()
                ->arrayNode('web_push')
                    ->children()
                        ->scalarNode('public_key_path')
                            ->cannotBeEmpty()
                            ->validate()
                            ->ifTrue(function($v) { return !\file_exists($v); })
                                ->thenInvalid('Public key file %s is not found')
                            ->end()
                        ->end()
                        ->scalarNode('private_key_path')
                            ->cannotBeEmpty()
                            ->validate()
                            ->ifTrue(function($v) { return !\file_exists($v); })
                                ->thenInvalid('Private key file %s is not found')
                            ->end()
                        ->end()
                        ->integerNode('ttl')
                            ->min(0)
                        ->end()
                    ->end()
                    ->validate()
                    ->ifTrue(function ($v) {
                        return (isset($v['public_key_path']) && !isset($v['private_key_path'])) ||
                            (!isset($v['public_key_path']) && isset($v['private_key_path']));
                    })
                        ->thenInvalid("The key pair 'public_key_path' and 'private_key_path' must be configured.")
                    ->end()
                ->end()
                ->arrayNode('report')
                    ->children()
                        ->arrayNode('email')
                            ->children()
                                ->scalarNode('from')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('to')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('subject')->defaultValue('Push Notification Report')->end()
                                ->scalarNode('template')->defaultValue('OpenppPushNotificationBundle:report:push_report_email.txt.twig')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
