<?php

namespace Openpp\PushNotificationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Sonata\EasyExtendsBundle\Mapper\DoctrineCollector;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class OpenppPushNotificationExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $bundles = $container->getParameter('kernel.bundles');

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('orm_tag.xml');
        $loader->load('push_service_manager.xml');

        if ($config['consumer']) {
            $loader->load('orm.xml');
            $loader->load('form.xml');
            $loader->load('pusher.xml');
            $loader->load('consumer.xml');
            $loader->load('api_controllers.xml');
            $mapBundleEnable = false;
            if (isset($bundles['OpenppMapBundle'])) {
                $mapBundleEnable = true;
            }

            if (isset($bundles['SonataAdminBundle'])) {
                $loader->load('admin.xml');
                $this->configureAdmin($mapBundleEnable, $config, $container);
            }

            $this->configureORMManager($mapBundleEnable, $container);
            $this->registerDoctrineMapping($config, $mapBundleEnable);
        }

        $this->configureClass($config, $container);
        $this->configurePushServiceManager($config, $container);
    }

    /**
     * @param boolean          $mapBundleEnable
     * @param array            $config
     * @param ContainerBuilder $container
     */
    protected function configureAdmin($mapBundleEnable, array $config, ContainerBuilder $container)
    {
        $container->getDefinition('openpp.push_notification.admin.application')
            ->addMethodCall('setPusherName', array($config['pusher']))
        ;

        $container->getDefinition('openpp.push_notification.admin.condition')
            ->addMethodCall('setMapBundleEnable', array($mapBundleEnable))
        ;
    }

    /**
     * @param boolean          $mapBundleEnable
     * @param ContainerBuilder $container
     */
    protected function configureORMManager($mapBundleEnable, ContainerBuilder $container)
    {
        $deviceManagerDefinition = $container->getDefinition('openpp.push_notification.manager.device');
        if ($mapBundleEnable) {
            $deviceManagerDefinition->replaceArgument(3, $container->getParameter('openpp.map.point.class'));
        } else {
            $deviceManagerDefinition->replaceArgument(3, '');
        }
    }

    /**
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

        // admin configuration
        $container->setParameter('openpp.push_notification.admin.applicaiton.entity', $config['class']['application']);
        $container->setParameter('openpp.push_notification.admin.device.entity', $config['class']['device']);
        $container->setParameter('openpp.push_notification.admin.tag.entity', $config['class']['tag']);
        $container->setParameter('openpp.push_notification.admin.user.entity', $config['class']['user']);
        $container->setParameter('openpp.push_notification.admin.condition.entity', $config['class']['condition']);
    }

    /**
     * @param array            $config
     * @param ContainerBuilder $container
     */
    protected function configurePushServiceManager($config, ContainerBuilder $container)
    {
        if ($config['consumer']) {
            $container->getDefinition('openpp.push_notification.push_service_manager')
                ->replaceArgument(2, new Reference($config['pusher']))
            ;
        } else {
            $container->getDefinition('openpp.push_notification.push_service_manager')
                ->replaceArgument(2, null)
            ;
        }
    }

    /**
     * Registers doctrine mapping on concrete push notification entities
     *
     * @param array   $config
     * @param boolean $mapBundleEnable
     *
     * @return void
     */
    protected function registerDoctrineMapping(array $config, $mapBundleEnable = true)
    {
        $collector = DoctrineCollector::getInstance();

        // One-To-Many Bidirectional for Application and User
        $collector->addAssociation($config['class']['application'], 'mapOneToMany', array(
            'fieldName'     => 'users',
            'targetEntity'  => $config['class']['user'],
            'cascade'       => array(
                'remove',
                'persist',
            ),
            'mappedBy'      => 'application',
            'orphanRemoval' => false,
        ));

        $collector->addAssociation($config['class']['user'], 'mapManyToOne', array(
            'fieldName'     => 'application',
            'targetEntity'  => $config['class']['application'],
            'cascade'       => array(
                'persist',
            ),
            'inversedBy'    => 'users',
            'joinColumns'   =>  array(
                array(
                    'name'  => 'application_id',
                    'referencedColumnName' => 'id',
                ),
            ),
            'orphanRemoval' => false,
        ));

        // One-To-Many Bidirectional for User and Device
        $collector->addAssociation($config['class']['user'], 'mapOneToMany', array(
            'fieldName'     => 'devices',
            'targetEntity'  => $config['class']['device'],
            'cascade'       => array(
                'remove',
                'persist',
            ),
            'mappedBy'      => 'user',
            'orphanRemoval' => false,
        ));

        $collector->addAssociation($config['class']['device'], 'mapManyToOne', array(
            'fieldName'     => 'user',
            'targetEntity'  => $config['class']['user'],
            'cascade'       => array(
                'persist',
            ),
            'inversedBy'    => 'devices',
            'joinColumns'   =>  array(
                array(
                    'name'  => 'user_id',
                    'referencedColumnName' => 'id',
                ),
            ),
            'orphanRemoval' => false,
        ));

        // Many-To-One Unidirectional for Application and Device
        $collector->addAssociation($config['class']['device'], 'mapManyToOne', array(
            'fieldName' => 'application',
            'targetEntity' => $config['class']['application'],
            'cascade' => array(
                'persist',
            ),
            'joinColumns'   =>  array(
                array(
                    'name'  => 'application_id',
                    'referencedColumnName' => 'id',
                ),
            ),
            'orphanRemoval' => false,
        ));

        // Many-To-Many Unidirectional for User and Tag
        $collector->addAssociation($config['class']['user'], 'mapManyToMany', array(
            'fieldName' => 'tags',
            'targetEntity' => $config['class']['tag'],
            'cascade' => array(
                'persist',
            ),
            'joinTable' => array(
                'name' => 'push__user_tag',
                'joinColumns' => array(
                    array(
                        'name' => 'user_id',
                        'referencedColumnName' => 'id',
                    ),
                ),
                'inverseJoinColumns' => array(
                    array(
                        'name' => 'tag_id',
                        'referencedColumnName' => 'id',
                    ),
                ),
            ),
            'orphanRemoval' => false,
        ));

        // Many-To-One Unidirectional for Application and Condition
        $collector->addAssociation($config['class']['condition'], 'mapManyToOne', array(
            'fieldName' => 'application',
            'targetEntity' => $config['class']['application'],
            'cascade' => array(
                'remove',
            ),
            'joinColumns'   =>  array(
                array(
                    'name'  => 'application_id',
                    'referencedColumnName' => 'id',
                ),
            ),
            'orphanRemoval' => false,
        ));

        if ($mapBundleEnable) {
            // One-To-One, Unidirectional Device and Point
            $collector->addAssociation($config['class']['device'], 'mapOneToOne', array(
                'fieldName' => 'location',
                'targetEntity' => $config['class']['location'],
                'cascade' => array(
                    'persist',
                    'remove',
                ),
                'joinColumns'   =>  array(
                    array(
                        'name'  => 'location_id',
                        'referencedColumnName' => 'id',
                    ),
                ),
                'orphanRemoval' => false,
            ));

            // One-To-One, Unidirectional Condition and Circle
            $collector->addAssociation($config['class']['condition'], 'mapOneToOne', array(
                'fieldName' => 'areaCircle',
                'targetEntity' => $config['class']['areaCircle'],
                'cascade' => array(
                    'persist',
                    'remove',
                ),
                'joinColumns'   =>  array(
                    array(
                        'name'  => 'area_circle_id',
                        'referencedColumnName' => 'id',
                    ),
                ),
                'orphanRemoval' => false,
            ));
        }
    }
}
