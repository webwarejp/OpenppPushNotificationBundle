<?php

namespace Openpp\PushNotificationBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Openpp\PushNotificationBundle\Model\DeviceInterface;
use Openpp\MapBundle\Model\PointInterface;
use Openpp\PushNotificationBundle\Model\DeviceManagerInterface;
use Openpp\PushNotificationBundle\Model\ConditionManagerInterface;
use Openpp\PushNotificationBundle\Pusher\PushServiceManagerInterface;
use Openpp\MapBundle\Querier\ORM\GeometryQuerier;
use CrEOF\Spatial\PHP\Types\Geometry\Point;

/**
 *
 * @author shiroko@webware.co.jp
 *
 */
class DeviceLocationSubscriber implements EventSubscriber
{
    /**
     * @var ContainerIneterface
     */
    protected $container;

    /**
     * Constructor
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return array(
            Events::postPersist,
            Events::preUpdate,
        );
    }

    /**
     *
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof DeviceInterface) {
            if ($entity->getLocation()) {
                $this->judgeLocation($entity, $entity->getLocation()->getPoint());
            }
        }
    }

    /**
     *
     * @param LifecycleEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();

        if ($entity instanceof PointInterface) {
            $device = $this->getDeviceManager()->findDeviceBy(array('location' => $entity));
            if ($device) {
                $this->judgeLocation($device, $args->getNewValue('point'), $args->getOldValue('point'));
            }
        }
    }

    /**
     *
     * @param DeviceInterface $device
     * @param Point $prevPoint
     * @param Point $currentPoint
     */
    protected function judgeLocation(DeviceInterface $device, Point $currentPoint, Point $prevPoint = null)
    {
        $conditions = $this->getConditionManager()->getContinuingConditions();
        foreach ($conditions as $condition) {
            if ($prevPoint) {
                if ($this->getGeometryQuerier()->isPointInCircle($prevPoint, $condition->getAreaCircle())) {
                    // do nothing
                    return;
                }
            }

            if ($this->getGeometryQuerier()->isPointInCircle($currentPoint, $condition->getAreaCircle())) {
                $tagExpression = $condition->getTagExpression();
                $uidTag = $device->getUser()->getUidTag();
                $tagExpression = $tagExpression ? '(' . $tagExpression . ') && ' . $uidTag : $uidTag;
                $this->getPushServiceManager()->push($condition->getApplication(), $tagExpression, $condition->getMessage());
            }
        }
    }

    /**
     * @return DeviceManagerInterface
     */
    protected function getDeviceManager()
    {
        return $this->container->get('openpp.push_notification.manager.device');
    }

    /**
     * @return ConditionManagerInterface
     */
    protected function getConditionManager()
    {
        return $this->container->get('openpp.push_notification.manager.condition');
    }

    /**
     * @return PushServiceManagerInterface
     */
    protected function getPushServiceManager()
    {
        return $this->container->get('openpp.push_notification.push_service_manager');
    }

    /**
     * @return GeometryQuerier
     */
    protected function getGeometryQuerier()
    {
        return $this->container->get('openpp.map.geometry_querier');
    }
}