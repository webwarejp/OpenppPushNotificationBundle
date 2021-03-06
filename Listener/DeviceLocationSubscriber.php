<?php

namespace Openpp\PushNotificationBundle\Listener;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Events;
use Openpp\MapBundle\Model\PointInterface;
use Openpp\MapBundle\Querier\ORM\GeometryQuerier;
use Openpp\PushNotificationBundle\Model\ConditionManagerInterface;
use Openpp\PushNotificationBundle\Model\DeviceInterface;
use Openpp\PushNotificationBundle\Model\DeviceManagerInterface;
use Openpp\PushNotificationBundle\Pusher\PushServiceManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DeviceLocationSubscriber implements EventSubscriber
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Initializes a new DeviceLocationSubscriber.
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
        return [
            Events::onFlush,
        ];
    }

    /**
     * @param OnFlushEventArgs $eventArgs
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $uow = $eventArgs->getEntityManager()->getUnitOfWork();

        foreach ($uow->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof DeviceInterface) {
                if ($entity->getLocation()) {
                    $this->judgeLocation($entity, $entity->getLocation()->getPoint());
                }
            }
        }
        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if ($entity instanceof PointInterface) {
                $device = $this->getDeviceManager()->findDeviceBy(['location' => $entity]);
                if ($device) {
                    $original = $uow->getOriginalEntityData($entity);
                    $this->judgeLocation($device, $entity->getPoint(), $original['point']);
                }
            }
        }
    }

    /**
     * @param DeviceInterface $device
     * @param Point           $prevPoint
     * @param Point           $currentPoint
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
                // TODO: tag expression check
                $this->getPushServiceManager()->pushToDevices(
                    $condition->getApplication()->getName(),
                    [$device->getId()],
                    $condition->getMessage()
                );
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
