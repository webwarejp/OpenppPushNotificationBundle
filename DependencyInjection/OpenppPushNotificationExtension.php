<?php

namespace Openpp\PushNotificationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;

class OpenppPushNotificationExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $bundles = $container->getParameter('kernel.bundles');

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('orm_tag.xml');
        $loader->load('push_service_manager.xml');
        $loader->load('orm.xml');

        $mapBundleEnable = false;
        if (isset($bundles['OpenppMapBundle'])) {
            $mapBundleEnable = true;
        }

        if (isset($bundles['SonataAdminBundle'])) {
            $loader->load('admin.xml');
            $this->configureAdmin($mapBundleEnable, $config, $container);
        }

        $this->configureClass($config, $container);
        $this->configurePushServiceManager($config, $container);

        $this->configureORMManager($mapBundleEnable, $container);
        $this->registerDoctrineMapping($config, $mapBundleEnable);

        if ($mapBundleEnable) {
            $container->getDefinition('openpp.push_notification.manipurator.register')
                ->addMethodCall('setPointManager', [new Reference('openpp.map.manager.point')]);
        }
        $container->getDefinition('openpp.push_notification.manipurator.register')
            ->replaceArgument(4, $config['uid_auto_prefix']);

        if ($config['consumer']) {
            $loader->load('pusher.xml');
            $loader->load('consumer.xml');
            $loader->load('report.xml');

            if (isset($config['report'])) {
                $container->getDefinition('openpp.push_notification.listener.push_result_email')
                    ->replaceArgument(2, $config['report']);
            } else {
                $container->getDefinition('openpp.push_notification.listener.push_result_email')
                    ->replaceArgument(2, []);
            }

            $this->configurePusher($config, $container);
        }
    }

    /**
     * Configures the admin services.
     *
     * @param bool             $mapBundleEnable
     * @param array            $config
     * @param ContainerBuilder $container
     */
    protected function configureAdmin($mapBundleEnable, array $config, ContainerBuilder $container)
    {
        $container->getDefinition('openpp.push_notification.admin.application')
            ->addMethodCall('setPusherName', [$config['pusher']])
        ;

        $container->getDefinition('openpp.push_notification.admin.condition')
            ->addMethodCall('setMapBundleEnable', [$mapBundleEnable])
        ;
    }

    /**
     * Configures the ORM Manager.
     *
     * @param bool             $mapBundleEnable
     * @param ContainerBuilder $container
     */
    protected function configureORMManager($mapBundleEnable, ContainerBuilder $container)
    {
        $deviceManagerDefinition = $container->getDefinition('openpp.push_notification.manager.device');
        if ($mapBundleEnable) {
            $deviceManagerDefinition->replaceArgument(4, $container->getParameter('openpp.map.point.class'));
        } else {
            $deviceManagerDefinition->replaceArgument(4, '');
        }
    }

    /**
     * Configures the Entity classes.
     *
     * @param array            $config
     * @param ContainerBuilder $container
     */
    protected function configureClass($config, ContainerBuilder $container)
    {
        // manager configuration
        $container->setParameter('openpp.push_notification.application.class', $config['class']['application']);
        $container->setParameter('openpp.push_notification.device.class', $config['class']['device']);
        $container->setParameter('openpp.push_notification.tag.class', $config['class']['tag']);
        $container->setParameter('openpp.push_notification.user.class', $config['class']['user']);
        $container->setParameter('openpp.push_notification.condition.class', $config['class']['condition']);
        $container->setParameter('openpp.push_notification.history.class', $config['class']['history']);

        // admin configuration
        $container->setParameter('openpp.push_notification.admin.applicaiton.entity', $config['class']['application']);
        $container->setParameter('openpp.push_notification.admin.device.entity', $config['class']['device']);
        $container->setParameter('openpp.push_notification.admin.tag.entity', $config['class']['tag']);
        $container->setParameter('openpp.push_notification.admin.user.entity', $config['class']['user']);
        $container->setParameter('openpp.push_notification.admin.condition.entity', $config['class']['condition']);
    }

    /**
     * Configures the Push Service Manager.
     *
     * @param array            $config
     * @param ContainerBuilder $container
     */
    protected function configurePushServiceManager($config, ContainerBuilder $container)
    {
        if ($config['consumer']) {
            $container->getDefinition('openpp.push_notification.push_service_manager')
                ->replaceArgument(3, new Reference($config['pusher']))
            ;
        } else {
            $container->getDefinition('openpp.push_notification.push_service_manager')
                ->replaceArgument(3, null)
            ;
        }
    }

    /**
     * Configures the Pusher.
     *
     * @param array            $config
     * @param ContainerBuilder $container
     */
    protected function configurePusher($config, ContainerBuilder $container)
    {
        if (isset($config['web_push']['public_key_path'])) {
            $container->setParameter('openpp.push_notification.web_push.public_key_path', $config['web_push']['public_key_path']);

            $container->getDefinition('openpp.push_notification.pusher.own')
                ->addMethodCall('setKeyPair', [$config['web_push']['public_key_path'], $config['web_push']['private_key_path']])
            ;
        } else {
            $container->setParameter('openpp.push_notification.web_push.public_key_path', null);
        }
        if (isset($config['web_push']['ttl'])) {
            $container->getDefinition('openpp.push_notification.pusher.own')
                ->addMethodCall('setTTL', [$config['web_push']['ttl']])
            ;
        }
    }

    /**
     * Registers doctrine mapping on concrete push notification entities.
     *
     * @param array $config
     * @param bool  $mapBundleEnable
     */
    protected function registerDoctrineMapping(array $config, $mapBundleEnable = true)
    {
        $collector = DoctrineCollector::getInstance();

        // One-To-Many Bidirectional for Application and User
        $collector->addAssociation($config['class']['application'], 'mapOneToMany', [
            'fieldName' => 'users',
            'targetEntity' => $config['class']['user'],
            'cascade' => [
                'remove',
                'persist',
            ],
            '',
            'mappedBy' => 'application',
            'orphanRemoval' => false,
        ]);

        // Many-To-One Unidirectional for Application and Media
        $collector->addAssociation($config['class']['application'], 'mapManyToOne', [
            'fieldName' => 'icon',
            'targetEntity' => $config['class']['media'],
            'cascade' => [
                'remove',
                'persist',
            ],
            'joinColumns' => [
                [
                    'name' => 'icon_id',
                    'referencedColumnName' => 'id',
                ],
            ],
            'orphanRemoval' => false,
        ]);

        $collector->addAssociation($config['class']['user'], 'mapManyToOne', [
            'fieldName' => 'application',
            'targetEntity' => $config['class']['application'],
            'cascade' => [
                'persist',
           ],
            'inversedBy' => 'users',
            'joinColumns' => [
                [
                    'name' => 'application_id',
                    'referencedColumnName' => 'id',
                ],
            ],
            'orphanRemoval' => false,
        ]);

        // One-To-Many Bidirectional for User and Device
        $collector->addAssociation($config['class']['user'], 'mapOneToMany', [
            'fieldName' => 'devices',
            'targetEntity' => $config['class']['device'],
            'cascade' => [
                'remove',
                'persist',
            ],
            'mappedBy' => 'user',
            'orphanRemoval' => false,
        ]);

        $collector->addAssociation($config['class']['device'], 'mapManyToOne', [
            'fieldName' => 'user',
            'targetEntity' => $config['class']['user'],
            'cascade' => [
                'persist',
            ],
            'inversedBy' => 'devices',
            'joinColumns' => [
                [
                    'name' => 'user_id',
                    'referencedColumnName' => 'id',
                ],
            ],
            'orphanRemoval' => false,
        ]);

        // Many-To-One Bidirectional for Application and Device
        $collector->addAssociation($config['class']['application'], 'mapOneToMany', [
            'fieldName' => 'devices',
            'targetEntity' => $config['class']['device'],
            'cascade' => [
                'remove',
                'persist',
            ],
            'mappedBy' => 'application',
            'orphanRemoval' => false,
        ]);

        $collector->addAssociation($config['class']['device'], 'mapManyToOne', [
            'fieldName' => 'application',
            'targetEntity' => $config['class']['application'],
            'cascade' => [
                'persist',
            ],
            'inversedBy' => 'devices',
            'joinColumns' => [
                [
                    'name' => 'application_id',
                    'referencedColumnName' => 'id',
                ],
            ],
            'orphanRemoval' => false,
        ]);

        // Many-To-Many Unidirectional for User and Tag
        $collector->addAssociation($config['class']['user'], 'mapManyToMany', [
            'fieldName' => 'tags',
            'targetEntity' => $config['class']['tag'],
            'cascade' => [
                'persist',
            ],
            'joinTable' => [
                'name' => 'push__user_tag',
                'joinColumns' => [
                    [
                        'name' => 'user_id',
                        'referencedColumnName' => 'id',
                    ],
                ],
                'inverseJoinColumns' => [
                    [
                        'name' => 'tag_id',
                        'referencedColumnName' => 'id',
                    ],
                ],
            ],
            'orphanRemoval' => false,
        ]);

        // Many-To-One Unidirectional for Application and Condition
        $collector->addAssociation($config['class']['condition'], 'mapManyToOne', [
            'fieldName' => 'application',
            'targetEntity' => $config['class']['application'],
            'cascade' => [
                'persist',
            ],
            'joinColumns' => [
                [
                    'name' => 'application_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ],
            ],
            'orphanRemoval' => false,
        ]);

        // Many-To-One Unidirectional for Condition and Media
        $collector->addAssociation($config['class']['condition'], 'mapManyToOne', [
            'fieldName' => 'icon',
            'targetEntity' => $config['class']['media'],
            'cascade' => [
                'persist',
            ],
            'joinColumns' => [
                [
                    'name' => 'icon_id',
                    'referencedColumnName' => 'id',
                ],
            ],
            'orphanRemoval' => false,
        ]);

        // Many-To-One Unidirectional for Application and History
        $collector->addAssociation($config['class']['history'], 'mapManyToOne', [
            'fieldName' => 'application',
            'targetEntity' => $config['class']['application'],
            'cascade' => [
                'persist',
            ],
            'joinColumns' => [
                [
                    'name' => 'application_id',
                    'referencedColumnName' => 'id',
                    'onDelete' => 'CASCADE',
                ],
            ],
            'orphanRemoval' => false,
        ]);

        if ($mapBundleEnable) {
            // One-To-One, Unidirectional Device and Point
            $collector->addAssociation($config['class']['device'], 'mapOneToOne', [
                'fieldName' => 'location',
                'targetEntity' => $config['class']['location'],
                'cascade' => [
                    'persist',
                    'remove',
                ],
                'joinColumns' => [
                    [
                        'name' => 'location_id',
                        'referencedColumnName' => 'id',
                    ],
                ],
                'orphanRemoval' => false,
            ]);

            // One-To-One, Unidirectional Condition and Circle
            $collector->addAssociation($config['class']['condition'], 'mapOneToOne', [
                'fieldName' => 'areaCircle',
                'targetEntity' => $config['class']['areaCircle'],
                'cascade' => [
                    'persist',
                    'remove',
                ],
                'joinColumns' => [
                    [
                        'name' => 'area_circle_id',
                        'referencedColumnName' => 'id',
                    ],
                ],
                'orphanRemoval' => false,
            ]);
        }
    }
}
